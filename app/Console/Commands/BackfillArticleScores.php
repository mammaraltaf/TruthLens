<?php

namespace App\Console\Commands;

use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\Badge;
use Illuminate\Console\Command;

class BackfillArticleScores extends Command
{
    protected $signature = 'articles:backfill-scores {--reprocess : Re-run full analysis instead of applying stored fallbacks}';

    protected $description = 'Ensure every completed article has a credibility score and scored badge';

    public function handle(): int
    {
        if ($this->option('reprocess')) {
            $articles = Article::query()->orderBy('id')->pluck('id');

            foreach ($articles as $articleId) {
                $this->call('article:reprocess', ['article' => $articleId]);
            }

            $this->info("Reprocessed {$articles->count()} article(s).");

            return self::SUCCESS;
        }

        $defaultScore = (float) config('truthlens.default_neutral_score', 50);
        $badge = Badge::findForScore($defaultScore);
        $updated = 0;

        Article::query()
            ->where('status', ArticleStatus::Completed)
            ->where(function ($q) use ($badge) {
                $q->whereNull('credibility_score')
                    ->orWhereNull('badge_id')
                    ->when($badge, fn ($q2) => $q2->orWhere('badge_id', Badge::query()->where('slug', 'unverified')->value('id')));
            })
            ->orderBy('id')
            ->chunkById(100, function ($articles) use ($defaultScore, $badge, &$updated) {
                foreach ($articles as $article) {
                    $score = $article->credibility_score !== null
                        ? (float) $article->credibility_score
                        : $defaultScore;

                    $article->update([
                        'credibility_score' => $score,
                        'badge_id' => Badge::findForScore($score)?->id ?? $badge?->id,
                    ]);

                    $updated++;
                }
            });

        $this->info("Updated {$updated} completed article(s) with scores.");

        return self::SUCCESS;
    }
}
