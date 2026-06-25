@extends('layouts.bootstrap')

@section('title', 'Admin — All articles — '.config('app.name'))

@section('content')
    <p class="tl-kicker mb-1">Administration</p>
    <h1 class="tl-section-title h2 mb-4">All articles</h1>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Total</p>
                <p class="tl-stat-value mb-0">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Completed</p>
                <p class="tl-stat-value mb-0 text-success">{{ $stats['completed'] }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">In progress</p>
                <p class="tl-stat-value mb-0" style="color: var(--tl-teal-dark);">{{ $stats['in_progress'] }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Failed</p>
                <p class="tl-stat-value mb-0 text-danger">{{ $stats['failed'] }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.articles.index') }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === '') btn-primary @else btn-outline-secondary @endif">All</a>
            <a href="{{ route('admin.articles.index', ['status' => 'completed']) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === 'completed') btn-primary @else btn-outline-secondary @endif">Completed</a>
            <a href="{{ route('admin.articles.index', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === 'pending') btn-primary @else btn-outline-secondary @endif">Pending</a>
            <a href="{{ route('admin.articles.index', ['status' => 'processing']) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === 'processing') btn-primary @else btn-outline-secondary @endif">Processing</a>
            <a href="{{ route('admin.articles.index', ['status' => 'failed']) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === 'failed') btn-primary @else btn-outline-secondary @endif">Failed</a>
        </div>
        <span class="tl-meta-pill"><i class="bi bi-people me-1"></i> All users</span>
    </div>

    <div class="tl-table-wrap bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Type</th>
                        <th>Badge</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $article)
                        @php
                            $adminScore = $article->resolvedCredibilityScore();
                            $adminBadge = $article->resolvedBadge();
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted small">{{ $article->id }}</td>
                            <td class="fw-medium" style="max-width: 220px;">
                                <div class="text-truncate" title="{{ $article->title }}">{{ $article->title ?: 'Untitled' }}</div>
                                @if ($article->url)
                                    <div class="small text-muted text-truncate" style="max-width: 220px;">{{ $article->url }}</div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold small">{{ $article->user->name }}</div>
                                <div class="text-muted small">{{ $article->user->email }}</div>
                            </td>
                            <td><span class="tl-meta-pill text-capitalize small">{{ $article->submission_type->value }}</span></td>
                            <td>
                                @if ($adminBadge)
                                    <span class="badge rounded-pill border-0" style="background: {{ $adminBadge->color }}">{{ $adminBadge->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-secondary">
                                @if ($adminScore !== null)
                                    {{ number_format($adminScore, 1) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="tl-meta-pill text-capitalize small">{{ $article->status->value }}</span></td>
                            <td class="small text-secondary text-nowrap">{{ $article->created_at->timezone(config('app.timezone'))->format('M j, Y g:i a') }}</td>
                            <td class="pe-4 text-end text-nowrap">
                                <a href="{{ route('articles.show', $article) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-secondary py-5">
                                <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                No articles found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($articles->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $articles->links() }}
        </div>
    @endif
@endsection
