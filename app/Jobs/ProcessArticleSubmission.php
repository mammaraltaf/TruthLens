<?php

namespace App\Jobs;

use App\Enums\ArticleStatus;
use App\Enums\ArticleSubmissionType;
use App\Models\Article;
use App\Models\Badge;
use App\Models\FactCheckResult;
use App\Models\Source;
use App\Services\ArticleContentFetcher;
use App\Services\FactCheckCredibilityScorer;
use App\Services\GoogleFactCheckClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ProcessArticleSubmission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $articleId) {}

    public function handle(
        ArticleContentFetcher $fetcher,
        GoogleFactCheckClient $factCheckClient,
        FactCheckCredibilityScorer $scorer,
    ): void {
        $article = Article::query()->find($this->articleId);
        if (! $article) {
            return;
        }

        $article->update(['status' => ArticleStatus::Processing]);

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

            $fetched = $fetcher->fromUrl($article->url);
            if ($fetched['text'] !== '') {
                $content = $fetched['text'];
            }
            $title = $title ?? $fetched['title'];
        }

        $normalized = trim(preg_replace('/\s+/u', ' ', $content) ?? '');
        $hash = hash('sha256', $normalized);

        $duplicate = Article::query()
            ->where('content_hash', $hash)
            ->where('id', '!=', $article->id)
            ->where('status', ArticleStatus::Completed)
            ->first();

        if ($duplicate) {
            $badgeId = $duplicate->badge_id ?? Badge::findForScore(
                $duplicate->credibility_score !== null ? (float) $duplicate->credibility_score : null
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
            $this->syncSource($article, $host);

            return;
        }

        [$apiPayload, $scored] = $this->searchAndScore(
            $factCheckClient,
            $scorer,
            is_string($title) ? trim($title) : '',
            $normalized,
        );

        $badge = Badge::findForScore($scored['score']);

        $article->update([
            'title' => $title,
            'content' => Str::limit($normalized, 50_000, ''),
            'content_hash' => $hash,
            'credibility_score' => $scored['score'],
            'badge_id' => $badge?->id,
            'status' => ArticleStatus::Completed,
            'processed_at' => now(),
        ]);

        FactCheckResult::query()->updateOrCreate(
            ['article_id' => $article->id],
            [
                'api_response' => $apiPayload,
                'computed_score' => $scored['score'],
                'verdict' => $scored['verdict'],
            ]
        );

        $this->syncSource($article, $host);
    }

    /**
     * @return array{0: array<string, mixed>|null, 1: array{score: float|null, verdict: string|null}}
     */
    private function searchAndScore(
        GoogleFactCheckClient $factCheckClient,
        FactCheckCredibilityScorer $scorer,
        string $title,
        string $content,
    ): array {
        $queries = [Str::limit(trim($title.' '.$content), 500, '')];
        if ($title !== '') {
            $queries[] = Str::limit($title, 500, '');
        }

        $apiPayload = null;
        $scored = ['score' => null, 'verdict' => null];

        foreach (array_unique($queries) as $query) {
            if ($query === '') {
                continue;
            }

            $apiPayload = $factCheckClient->searchClaims($query);
            $scored = $scorer->score($apiPayload);

            if ($scored['score'] !== null) {
                break;
            }
        }

        return [$apiPayload, $scored];
    }

    private function syncSource(Article $article, ?string $host): void
    {
        if (! is_string($host) || $host === '') {
            return;
        }

        $source = Source::query()->firstOrCreate(
            ['domain' => $host],
            ['trust_score' => 50, 'is_banned' => false, 'article_count' => 0]
        );

        $source->increment('article_count');

        $article->update(['source_id' => $source->id]);
    }
}
