<?php

namespace App\Http\Controllers;

use App\Enums\ArticleStatus;
use App\Enums\ArticleSubmissionType;
use App\Http\Requests\StoreArticleRequest;
use App\Jobs\ProcessArticleSubmission;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
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
        $user = $request->user();
        $type = ArticleSubmissionType::from($request->string('submission_type')->toString());

        Log::info('Article submission store started', [
            'user_id' => $user->id,
            'submission_type' => $type->value,
            'has_url' => $request->filled('url'),
            'has_title' => $request->filled('title'),
            'has_category' => $request->filled('category'),
            'content_length' => $type === ArticleSubmissionType::Text
                ? strlen($request->string('content')->toString())
                : 0,
        ]);

        $content = $type === ArticleSubmissionType::Text
            ? $request->string('content')->toString()
            : '';

        Log::debug('Article submission payload prepared', [
            'user_id' => $user->id,
            'submission_type' => $type->value,
            'content_length' => strlen($content),
        ]);

        $article = Article::query()->create([
            'user_id' => $user->id,
            'submission_type' => $type,
            'url' => $type === ArticleSubmissionType::Url ? $request->string('url')->toString() : null,
            'title' => $request->input('title'),
            'content' => $content,
            'category' => $request->input('category'),
            'status' => ArticleStatus::Pending,
        ]);

        Log::info('Article record created', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'submission_type' => $type->value,
            'status' => $article->status->value,
            'url' => $article->url,
            'title_length' => is_string($article->title) ? strlen($article->title) : 0,
            'category' => $article->category,
        ]);

        Log::info('Dispatching ProcessArticleSubmission job', [
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        $pendingDispatch = ProcessArticleSubmission::dispatch($article->id);

        Log::info('ProcessArticleSubmission job dispatched', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'queue_connection' => config('queue.default'),
            'job_class' => ProcessArticleSubmission::class,
            'dispatch_result' => $pendingDispatch !== null ? 'success' : 'unknown',
        ]);

        Log::info('Article submission store completed, redirecting to article show', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'redirect_route' => 'articles.show',
        ]);

        return redirect()->route('articles.show', $article)
            ->with('status', 'Your article is being analyzed. Refresh this page shortly to see the results.');
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
