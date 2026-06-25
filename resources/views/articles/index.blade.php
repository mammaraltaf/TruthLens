@extends('layouts.bootstrap')

@section('title', 'Community feed — '.config('app.name'))

@section('content')
    <header class="tl-hero">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <p class="text-white-50 mb-2 small fw-semibold text-uppercase" style="letter-spacing: 0.08em;">Public feed</p>
                <h1 class="display-6 fw-bold mb-3">Verified articles</h1>
                <p class="mb-0 lead fs-6">Browse completed credibility checks, scores, and community votes. Sign in to submit a new article for analysis.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                @auth
                    <a href="{{ route('articles.create') }}" class="btn btn-light tl-btn-hero text-dark"><i class="bi bi-plus-lg me-1"></i> Check an article</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-light tl-btn-hero text-dark me-2"><i class="bi bi-person-plus me-1"></i> Join</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light tl-btn-hero"><i class="bi bi-box-arrow-in-right me-1"></i> Log in</a>
                @endauth
            </div>
        </div>
    </header>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div>
            <p class="tl-kicker mb-1">Latest results</p>
            <h2 class="tl-section-title h4 mb-0">Feed</h2>
        </div>
        <span class="tl-meta-pill"><i class="bi bi-grid-3x3-gap me-1"></i> {{ $articles->total() }} {{ \Illuminate\Support\Str::plural('item', $articles->total()) }}</span>
    </div>

    <div class="row g-4">
        @forelse ($articles as $article)
            @php
                $feedScore = $article->resolvedCredibilityScore();
                $feedBadge = $article->resolvedBadge();
            @endphp
            <div class="col-md-6 col-xl-4">
                <div
                    class="card tl-card tl-card-feed h-100 border-0 position-relative"
                    style="--tl-accent: {{ $feedBadge->color ?? '#94a3b8' }}"
                >
                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            @if ($feedBadge)
                                <span class="badge rounded-pill flex-shrink-0 border-0 shadow-sm" style="background: {{ $feedBadge->color }}">
                                    {{ $feedBadge->name }}
                                </span>
                            @endif
                            <span class="tl-meta-pill small">{{ $article->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="h5 card-title fw-bold mt-1">
                            <a href="{{ route('articles.show', $article) }}" class="text-decoration-none text-reset stretched-link">
                                {{ \Illuminate\Support\Str::limit($article->title ?: 'Untitled submission', 72) }}
                            </a>
                        </h3>
                        <p class="card-text small text-secondary flex-grow-1 mb-3" style="line-height: 1.55;">
                            {{ \Illuminate\Support\Str::limit(strip_tags($article->content), 130) }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between mt-auto pt-2 border-top border-light-subtle">
                            @if ($feedScore !== null)
                                <span class="small fw-semibold text-secondary"><i class="bi bi-speedometer2 me-1" style="color: var(--tl-teal);"></i> {{ number_format($feedScore, 1) }}<span class="text-muted fw-normal">/100</span></span>
                            @else
                                <span class="small text-muted"><i class="bi bi-hourglass-split me-1"></i> Analyzing…</span>
                            @endif
                            <span class="small text-muted"><i class="bi bi-hand-thumbs-up me-1"></i>{{ $article->real_votes_count }} &nbsp; <i class="bi bi-hand-thumbs-down ms-1 me-1"></i>{{ $article->fake_votes_count }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card tl-card border-0 text-center py-5 px-4">
                    <div class="card-body">
                        <i class="bi bi-inbox display-4 text-muted mb-3 d-block"></i>
                        <p class="text-secondary mb-3">No completed analyses yet. Be the first to submit one.</p>
                        @auth
                            <a href="{{ route('articles.create') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">Check an article</a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary rounded-pill px-4 fw-semibold">Create an account</a>
                        @endauth
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    @if ($articles->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
