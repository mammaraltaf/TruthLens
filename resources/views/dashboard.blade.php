@extends('layouts.bootstrap')

@section('title', 'Dashboard — '.config('app.name'))

@section('content')
    <p class="tl-kicker mb-1">Overview</p>
    <h1 class="tl-section-title h2 mb-4">Dashboard</h1>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-sm-4">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">All submissions</p>
                <p class="tl-stat-value mb-0">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Completed</p>
                <p class="tl-stat-value mb-0 text-success">{{ $stats['completed'] }}</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">In progress</p>
                <p class="tl-stat-value mb-0" style="color: var(--tl-teal-dark);">{{ $stats['in_progress'] }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <h2 class="h5 tl-section-title mb-0">Recent activity</h2>
        <a href="{{ route('articles.create') }}" class="btn btn-primary btn-sm rounded-pill fw-semibold px-3">
            <i class="bi bi-plus-lg me-1"></i> New check
        </a>
    </div>

    <div class="tl-table-wrap bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Badge</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($articles as $article)
                        @php
                            $dashScore = $article->resolvedCredibilityScore();
                            $dashBadge = $article->resolvedBadge();
                        @endphp
                        <tr>
                            <td class="ps-4 fw-medium">{{ \Illuminate\Support\Str::limit($article->title ?: '—', 52) }}</td>
                            <td>
                                @if ($dashBadge)
                                    <span class="badge rounded-pill border-0" style="background: {{ $dashBadge->color }}">{{ $dashBadge->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-secondary">
                                @if ($dashScore !== null)
                                    {{ number_format($dashScore, 1) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td><span class="tl-meta-pill text-capitalize">{{ $article->status->value }}</span></td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('articles.show', $article) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-5">
                                <i class="bi bi-folder2-open d-block fs-2 mb-2 opacity-50"></i>
                                No submissions yet. <a href="{{ route('articles.create') }}" class="fw-semibold">Check an article</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
