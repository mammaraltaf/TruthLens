<?php

namespace App\Console\Commands;

use App\Enums\ArticleStatus;
use App\Jobs\ProcessArticleSubmission;
use App\Models\Article;
use Illuminate\Console\Command;

class ReprocessArticle extends Command
{
    protected $signature = 'article:reprocess {article : The article ID to re-analyze}';

    protected $description = 'Re-run fact-check analysis and scoring for an existing article';

    public function handle(): int
    {
        $article = Article::query()->find($this->argument('article'));

        if (! $article) {
            $this->error('Article not found.');

            return self::FAILURE;
        }

        $article->update([
            'status' => ArticleStatus::Pending,
            'credibility_score' => null,
            'badge_id' => null,
            'duplicate_of_id' => null,
            'content_hash' => null,
            'processed_at' => null,
        ]);

        ProcessArticleSubmission::dispatch($article->id);

        $this->info("Article #{$article->id} queued for reprocessing.");

        return self::SUCCESS;
    }
}
