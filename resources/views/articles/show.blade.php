@extends('layouts.bootstrap')

@section('title', ($article->title ?: 'Article').' — '.config('app.name'))

@php
    $numScore = $article->resolvedCredibilityScore();
    $displayBadge = $article->resolvedBadge();
    $barClass = '';
    if ($numScore !== null) {
        $barClass = $numScore < 40 ? 'tl-progress-low' : ($numScore < 70 ? 'tl-progress-mid' : '');
    }
@endphp

@section('content')
    <div class="mb-3">
        <a href="{{ route('articles.index') }}" class="tl-back-link"><i class="bi bi-arrow-left"></i> Back to feed</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="card tl-card border-0 mb-4">
                <div class="card-body p-4 p-lg-5">
                    <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                        @if ($displayBadge)
                            <span class="badge rounded-pill px-3 py-2 border-0 shadow-sm" style="background: {{ $displayBadge->color }}; font-size: 0.8rem;">
                                {{ $displayBadge->name }}
                            </span>
                        @endif
                        <span class="tl-meta-pill text-capitalize"><i class="bi bi-activity me-1"></i>{{ $article->status->value }}</span>
                        @if ($article->category)
                            <span class="tl-meta-pill"><i class="bi bi-tag me-1"></i>{{ $article->category }}</span>
                        @endif
                    </div>

                    <h1 class="tl-section-title display-6 fs-3 fw-bold mb-3">{{ $article->title ?: 'Untitled submission' }}</h1>

                    @if ($article->url)
                        <p class="mb-3">
                            <a href="{{ $article->url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary rounded-pill">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Open original
                            </a>
                        </p>
                    @endif

                    @if ($article->duplicate_of_id)
                        <div class="alert alert-info border-0 rounded-3 d-flex align-items-start gap-2 mb-4" role="alert">
                            <i class="bi bi-link-45deg fs-5"></i>
                            <span>This submission matches a previous one (<strong>#{{ $article->duplicate_of_id }}</strong>); scores are linked.</span>
                        </div>
                    @endif

                    @if ($numScore !== null)
                        <div class="tl-score-panel mb-4">
                            <p class="tl-kicker mb-2">Credibility score</p>
                            <div class="d-flex flex-wrap align-items-end gap-3 mb-3">
                                <span class="tl-score-number">{{ number_format($numScore, 1) }}</span>
                                <span class="text-secondary pb-1 fw-medium mb-1">/ 100</span>
                            </div>
                            <div class="tl-progress-wrap" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ (int) $numScore }}" aria-label="Credibility score">
                                <div class="tl-progress-bar {{ $barClass }}" style="width: {{ min(100, max(0, $numScore)) }}%;"></div>
                            </div>
                            <p class="small text-secondary mt-3 mb-0">Based on fact-check matches, publisher reputation, and content analysis. Always read the original sources linked below.</p>
                        </div>
                    @elseif ($article->status->value !== 'completed')
                        <div class="tl-score-panel mb-4 border border-2 border-dashed rounded-3" style="border-color: #cbd5e1 !important;">
                            <p class="mb-1 fw-semibold text-secondary"><i class="bi bi-hourglass-split me-2"></i>Score pending</p>
                            <p class="small text-muted mb-0">Analysis is in progress. Refresh this page in a moment to see the credibility score.</p>
                        </div>
                    @endif

                    <h2 class="h6 tl-kicker mb-2">Submitted text</h2>
                    <div class="tl-excerpt-box p-3 rounded-3">
                        {{ \Illuminate\Support\Str::limit($article->content, 8000) }}
                    </div>
                </div>
            </article>

            @if ($claimReviews !== [])
                <section class="card tl-card border-0 mb-4 overflow-hidden">
                    <div class="card-header tl-card-header border-0">
                        <i class="bi bi-journal-check me-2 text-muted"></i> Fact-check references
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach ($claimReviews as $review)
                            <div class="tl-review-item">
                                <div class="fw-semibold mb-1">{{ $review['publisher'] ?? 'Publisher unknown' }}</div>
                                <div class="small text-secondary mb-2">{{ $review['title'] }}</div>
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    @if (! empty($review['rating']))
                                        <span class="badge rounded-pill text-bg-light border">{{ $review['rating'] }}</span>
                                    @endif
                                    @if (! empty($review['url']))
                                        <a href="{{ $review['url'] }}" target="_blank" rel="noopener" class="small fw-semibold text-decoration-none">
                                            View review <i class="bi bi-arrow-up-right small"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card tl-card border-0 mb-4 sticky-lg-top" style="top: 1rem;">
                <div class="card-header tl-card-header border-0">
                    <i class="bi bi-people me-2 text-muted"></i> Community
                </div>
                <div class="card-body p-4">
                    <div class="row g-2 text-center mb-4">
                        <div class="col-6">
                            <div class="rounded-3 py-3" style="background: rgba(25, 135, 84, 0.12);">
                                <div class="fs-4 fw-bold text-success">{{ $article->realVoteCount() }}</div>
                                <div class="small text-secondary fw-semibold">Real</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded-3 py-3" style="background: rgba(220, 53, 69, 0.1);">
                                <div class="fs-4 fw-bold text-danger">{{ $article->fakeVoteCount() }}</div>
                                <div class="small text-secondary fw-semibold">Fake</div>
                            </div>
                        </div>
                    </div>

                    @auth
                        <p class="small text-secondary mb-2 fw-semibold">Your verdict</p>
                        <form method="post" action="{{ route('articles.votes.store', $article) }}" class="tl-vote-bar d-grid gap-2">
                            @csrf
                            <button type="submit" name="vote_type" value="real" class="btn @if ($userVote?->vote_type->value === 'real') btn-success @else btn-outline-success @endif rounded-pill fw-semibold">
                                <i class="bi bi-hand-thumbs-up me-1"></i> Credible
                            </button>
                            <button type="submit" name="vote_type" value="fake" class="btn @if ($userVote?->vote_type->value === 'fake') btn-danger @else btn-outline-danger @endif rounded-pill fw-semibold">
                                <i class="bi bi-hand-thumbs-down me-1"></i> Not credible
                            </button>
                        </form>
                        <p class="small text-muted mt-3 mb-0">One vote per account. You can change your vote at any time.</p>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill fw-semibold">Log in to vote</a>
                    @endauth
                </div>
            </div>

            @auth
                <div class="card tl-card border-0 mb-4">
                    <div class="card-header tl-card-header border-0">
                        <i class="bi bi-flag me-2 text-muted"></i> Report content
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="{{ route('articles.reports.store', $article) }}" class="d-grid gap-3">
                            @csrf
                            <div>
                                <label for="report_category" class="form-label small fw-semibold text-secondary">Reason</label>
                                <select name="category" id="report_category" class="form-select form-select-sm rounded-3" required>
                                    <option value="" disabled selected>Choose a category</option>
                                    @foreach (\App\Enums\ReportCategory::cases() as $category)
                                        <option value="{{ $category->value }}" @selected(old('category') === $category->value)>{{ ucfirst(str_replace('_', ' ', $category->value)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="report_details" class="form-label small fw-semibold text-secondary">Details (optional)</label>
                                <textarea name="details" id="report_details" rows="3" class="form-control form-control-sm rounded-3" maxlength="2000" placeholder="What is misleading or wrong about this content?">{{ old('details') }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill fw-semibold">
                                <i class="bi bi-flag me-1"></i> Submit report
                            </button>
                        </form>
                    </div>
                </div>
            @endauth

            <div class="card tl-card border-0 mb-4">
                <div class="card-body p-4 small text-secondary">
                    @if ($article->source)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-globe2"></i>
                            <span class="fw-semibold text-dark">{{ $article->source->domain }}</span>
                        </div>
                    @endif
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-person"></i>
                        <span>Submitted by {{ $article->user->name }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-clock"></i>
                        <span>{{ $article->created_at->timezone(config('app.timezone'))->format('M j, Y g:i a') }}</span>
                    </div>
                </div>
            </div>

            @if ($article->status->value !== 'completed')
                <div class="alert alert-warning border-0 rounded-3 small mb-0">
                    <i class="bi bi-hourglass-split me-1"></i> Analysis is still in progress. Refresh this page in a moment.
                </div>
            @endif
        </div>
    </div>
@endsection
