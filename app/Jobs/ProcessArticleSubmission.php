<?php

namespace App\Jobs;

use App\Enums\ArticleStatus;
use App\Enums\ArticleSubmissionType;
use App\Models\Article;
use App\Models\Badge;
use App\Models\FactCheckResult;
use App\Models\Source;
use App\Support\UnknownDomainScoring;
use App\Services\ArticleContentFetcher;
use App\Services\ClaimQueryBuilder;
use App\Services\CredibilityScoreResolver;
use App\Services\FactCheckCredibilityScorer;
use App\Services\GoogleFactCheckClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProcessArticleSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $articleId) {}

    public function handle(
        ArticleContentFetcher $fetcher,
        GoogleFactCheckClient $factCheckClient,
        FactCheckCredibilityScorer $scorer,
        ClaimQueryBuilder $queryBuilder,
        CredibilityScoreResolver $scoreResolver,
    ): void {
        Log::info('ProcessArticleSubmission job started', [
            'article_id' => $this->articleId,
            'job_id' => $this->job?->getJobId(),
            'queue' => $this->job?->getQueue(),
            'attempt' => $this->attempts(),
        ]);

        $article = Article::query()->find($this->articleId);
        if (! $article) {
            Log::warning('ProcessArticleSubmission aborted: article not found', [
                'article_id' => $this->articleId,
            ]);

            return;
        }

        Log::info('ProcessArticleSubmission article loaded', [
            'article_id' => $article->id,
            'user_id' => $article->user_id,
            'submission_type' => $article->submission_type->value,
            'status' => $article->status->value,
            'url' => $article->url,
        ]);

        $article->update(['status' => ArticleStatus::Processing]);

        Log::info('ProcessArticleSubmission status updated to processing', [
            'article_id' => $article->id,
            'status' => ArticleStatus::Processing->value,
        ]);

        $title = $article->title;
        $content = $article->content;
        $host = null;

        if ($article->submission_type === ArticleSubmissionType::Url && $article->url) {
            $host = parse_url($article->url, PHP_URL_HOST);
            if (is_string($host)) {
                $host = Str::lower(preg_replace('/^www\./', '', $host) ?? $host);
            } else {
                $host = null;
            }

            Log::info('ProcessArticleSubmission fetching URL content', [
                'article_id' => $article->id,
                'url' => $article->url,
                'host' => $host,
            ]);

            $fetched = $fetcher->fromUrl($article->url);

            Log::info('ProcessArticleSubmission URL content fetched', [
                'article_id' => $article->id,
                'url' => $article->url,
                'fetched_text_length' => strlen($fetched['text']),
                'fetched_title_present' => $fetched['title'] !== null && $fetched['title'] !== '',
            ]);

            if ($fetched['text'] !== '') {
                $content = $fetched['text'];
            }
            $title = $title ?? $fetched['title'];
        }

        $normalized = trim(preg_replace('/\s+/u', ' ', $content) ?? '');
        $hash = hash('sha256', $normalized);

        Log::info('ProcessArticleSubmission content normalized', [
            'article_id' => $article->id,
            'normalized_length' => strlen($normalized),
            'content_hash' => $hash,
        ]);

        $duplicate = Article::query()
            ->with('factCheckResult')
            ->where('content_hash', $hash)
            ->where('id', '!=', $article->id)
            ->where('status', ArticleStatus::Completed)
            ->whereNotNull('credibility_score')
            ->orderByDesc('credibility_score')
            ->first();

        if ($duplicate) {
            Log::info('ProcessArticleSubmission duplicate article detected', [
                'article_id' => $article->id,
                'duplicate_of_id' => $duplicate->id,
                'duplicate_credibility_score' => $duplicate->credibility_score,
            ]);

            $badgeId = $duplicate->badge_id ?? Badge::findForScore(
                (float) $duplicate->credibility_score
            )?->id;

            $article->update([
                'title' => $title,
                'content' => Str::limit($normalized, 50_000, ''),
                'content_hash' => $hash,
                'duplicate_of_id' => $duplicate->id,
                'credibility_score' => $duplicate->credibility_score,
                'badge_id' => $badgeId,
                'status' => ArticleStatus::Completed,
                'processed_at' => now(),
            ]);

            Log::info('ProcessArticleSubmission completed via duplicate reuse', [
                'article_id' => $article->id,
                'duplicate_of_id' => $duplicate->id,
                'credibility_score' => $duplicate->credibility_score,
                'badge_id' => $badgeId,
                'status' => ArticleStatus::Completed->value,
            ]);

            $this->syncSource($article, $host);

            Log::info('ProcessArticleSubmission job finished (duplicate path)', [
                'article_id' => $article->id,
            ]);

            return;
        }

        Log::info('ProcessArticleSubmission starting fact-check search and scoring', [
            'article_id' => $article->id,
            'title_length' => is_string($title) ? strlen(trim($title)) : 0,
        ]);

        [$apiPayload, $scored] = $this->searchAndScore(
            $factCheckClient,
            $scorer,
            $queryBuilder,
            is_string($title) ? trim($title) : '',
            $normalized,
        );

        Log::info('ProcessArticleSubmission fact-check API scoring completed', [
            'article_id' => $article->id,
            'api_score' => $scored['score'],
            'api_verdict' => $scored['verdict'],
            'api_payload_present' => $apiPayload !== null,
        ]);

        $source = $this->syncSource($article, $host);

        $resolved = $scoreResolver->resolve(
            $scored['score'],
            $scored['verdict'],
            $host,
            $source,
        );

        Log::info('ProcessArticleSubmission credibility score resolved', [
            'article_id' => $article->id,
            'credibility_score' => $resolved['score'],
            'verdict' => $resolved['verdict'],
            'score_source' => $resolved['score_source'],
            'host' => $host,
        ]);

        $badge = Badge::findForScore($resolved['score']);

        $article->update([
            'title' => $title,
            'content' => Str::limit($normalized, 50_000, ''),
            'content_hash' => $hash,
            'credibility_score' => $resolved['score'],
            'badge_id' => $badge?->id,
            'status' => ArticleStatus::Completed,
            'processed_at' => now(),
        ]);

        Log::info('ProcessArticleSubmission article updated with fact-check results', [
            'article_id' => $article->id,
            'credibility_score' => $resolved['score'],
            'badge_id' => $badge?->id,
            'status' => ArticleStatus::Completed->value,
        ]);

        FactCheckResult::query()->updateOrCreate(
            ['article_id' => $article->id],
            [
                'api_response' => $apiPayload,
                'computed_score' => $resolved['score'],
                'verdict' => $resolved['verdict'],
            ]
        );

        Log::info('ProcessArticleSubmission fact-check result persisted', [
            'article_id' => $article->id,
            'computed_score' => $resolved['score'],
            'verdict' => $resolved['verdict'],
        ]);

        Log::info('ProcessArticleSubmission job finished (fact-check path)', [
            'article_id' => $article->id,
            'status' => ArticleStatus::Completed->value,
        ]);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ProcessArticleSubmission job failed', [
            'article_id' => $this->articleId,
            'attempt' => $this->attempts(),
            'exception' => $exception?->getMessage(),
            'exception_class' => $exception !== null ? $exception::class : null,
        ]);
    }

    /**
     * @return array{0: array<string, mixed>|null, 1: array{score: float|null, verdict: string|null}}
     */
    private function searchAndScore(
        GoogleFactCheckClient $factCheckClient,
        FactCheckCredibilityScorer $scorer,
        ClaimQueryBuilder $queryBuilder,
        string $title,
        string $content,
    ): array {
        $queries = $queryBuilder->build($title, $content);

        Log::debug('ProcessArticleSubmission fact-check queries built', [
            'query_count' => count($queries),
        ]);

        $apiPayload = null;
        $scored = ['score' => null, 'verdict' => null];

        foreach ($queries as $query) {
            Log::debug('ProcessArticleSubmission fact-check query executing', [
                'query_length' => strlen($query),
                'query_preview' => Str::limit($query, 120, '…'),
            ]);

            $apiPayload = $factCheckClient->searchClaims($query);
            $scored = $scorer->score($apiPayload);

            Log::debug('ProcessArticleSubmission fact-check query scored', [
                'query_length' => strlen($query),
                'score' => $scored['score'],
                'verdict' => $scored['verdict'],
            ]);

            if ($scored['score'] !== null) {
                break;
            }
        }

        return [$apiPayload, $scored];
    }

    private function syncSource(Article $article, ?string $host): ?Source
    {
        if (! is_string($host) || $host === '') {
            Log::debug('ProcessArticleSubmission source sync skipped', [
                'article_id' => $article->id,
                'reason' => 'no_host',
            ]);

            return null;
        }

        $trustedDomains = config('truthlens.trusted_domains', []);
        $configuredTrust = is_array($trustedDomains) ? ($trustedDomains[$host] ?? null) : null;
        $trustScore = is_numeric($configuredTrust)
            ? (int) $configuredTrust
            : UnknownDomainScoring::randomTrustScore();

        Log::info('ProcessArticleSubmission syncing source', [
            'article_id' => $article->id,
            'host' => $host,
            'trust_score' => $trustScore,
        ]);

        $source = Source::query()->firstOrCreate(
            ['domain' => $host],
            ['trust_score' => $trustScore, 'is_banned' => false, 'article_count' => 0]
        );

        if ($trustScore > $source->trust_score) {
            $source->update(['trust_score' => $trustScore]);
        }

        $source->increment('article_count');

        $article->update(['source_id' => $source->id]);

        Log::info('ProcessArticleSubmission source synced', [
            'article_id' => $article->id,
            'source_id' => $source->id,
            'host' => $host,
            'trust_score' => $source->trust_score,
            'article_count' => $source->article_count,
        ]);

        return $source->fresh();
    }
}
