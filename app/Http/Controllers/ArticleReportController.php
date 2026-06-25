<?php

namespace App\Http\Controllers;

use App\Enums\ReportCategory;
use App\Enums\ReportStatus;
use App\Http\Requests\StoreArticleReportRequest;
use App\Models\Article;
use App\Models\ArticleReport;
use Illuminate\Http\RedirectResponse;

class ArticleReportController extends Controller
{
    public function store(StoreArticleReportRequest $request, Article $article): RedirectResponse
    {
        $existing = ArticleReport::query()
            ->where('article_id', $article->id)
            ->where('user_id', $request->user()->id)
            ->where('status', ReportStatus::Pending)
            ->exists();

        if ($existing) {
            return back()->with('status', 'You already have a pending report on this article.');
        }

        ArticleReport::query()->create([
            'article_id' => $article->id,
            'user_id' => $request->user()->id,
            'category' => $request->enum('category', ReportCategory::class),
            'details' => $request->input('details'),
            'status' => ReportStatus::Pending,
        ]);

        return back()->with('status', 'Thank you. Your report was sent to the moderation queue.');
    }
}
