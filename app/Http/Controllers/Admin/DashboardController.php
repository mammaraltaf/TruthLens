<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleStatus;
use App\Enums\ReportStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleReport;
use App\Models\Source;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'articles_total' => Article::query()->count(),
            'articles_completed' => Article::query()->where('status', ArticleStatus::Completed)->count(),
            'reports_pending' => ArticleReport::query()->where('status', ReportStatus::Pending)->count(),
            'sources_banned' => Source::query()->where('is_banned', true)->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
