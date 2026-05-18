<?php

namespace App\Http\Controllers;

use App\Enums\ArticleStatus;
use App\Enums\ArticleSubmissionType;
use App\Http\Requests\StoreArticleRequest;
use App\Jobs\ProcessArticleSubmission;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->with(['user', 'badge', 'source'])
            ->withCount([
                'votes as real_votes_count' => fn ($q) => $q->where('vote_type', 'real'),
                'votes as fake_votes_count' => fn ($q) => $q->where('vote_type', 'fake'),
            ])
            ->where('status', ArticleStatus::Completed)
            ->latest()
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function create(): View
    {
        return view('articles.create');
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $type = ArticleSubmissionType::from($request->string('submission_type')->toString());

        $content = $type === ArticleSubmissionType::Text
            ? $request->string('content')->toString()
            : '';

        $article = Article::query()->create([
            'user_id' => $request->user()->id,
            'submission_type' => $type,
            'url' => $type === ArticleSubmissionType::Url ? $request->string('url')->toString() : null,
            'title' => $request->input('title'),
            'content' => $content,
            'category' => $request->input('category'),
            'status' => ArticleStatus::Pending,
        ]);

        ProcessArticleSubmission::dispatch($article->id);

        return redirect()->route('articles.show', $article)
            ->with('status', 'Your submission is being analyzed. Refresh this page in a few seconds.');
    }

    public function show(Article $article): View
    {
        $article->load([
            'user',
            'badge',
            'source',
            'factCheckResult',
            'votes',
        ]);

        $userVote = null;
        if (auth()->check()) {
            $userVote = $article->votes->where('user_id', auth()->id())->first();
        }

        $claimReviews = $article->factCheckResult?->claimReviews() ?? [];

        return view('articles.show', compact('article', 'userVote', 'claimReviews'));
    }
}
