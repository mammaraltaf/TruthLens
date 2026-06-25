<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleStatus;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();

        $articles = Article::query()
            ->with(['user', 'badge', 'source'])
            ->withCount([
                'votes as real_votes_count' => fn ($q) => $q->where('vote_type', 'real'),
                'votes as fake_votes_count' => fn ($q) => $q->where('vote_type', 'fake'),
            ])
            ->when(
                $status !== '' && ArticleStatus::tryFrom($status),
                fn ($q) => $q->where('status', ArticleStatus::from($status)),
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total' => Article::query()->count(),
            'completed' => Article::query()->where('status', ArticleStatus::Completed)->count(),
            'in_progress' => Article::query()->whereIn('status', [ArticleStatus::Pending, ArticleStatus::Processing])->count(),
            'failed' => Article::query()->where('status', ArticleStatus::Failed)->count(),
        ];

        return view('admin.articles.index', compact('articles', 'stats', 'status'));
    }
}
