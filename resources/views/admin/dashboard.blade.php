@extends('layouts.bootstrap')

@section('title', 'Staff — '.config('app.name'))

@section('content')
    @include('admin._nav')

    <p class="tl-kicker mb-1">Staff area</p>
    <h1 class="tl-section-title h2 mb-4">Moderation overview</h1>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">All articles</p>
                <p class="tl-stat-value mb-0">{{ $stats['articles_total'] }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Completed</p>
                <p class="tl-stat-value mb-0 text-success">{{ $stats['articles_completed'] }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Pending reports</p>
                <p class="tl-stat-value mb-0 text-warning">{{ $stats['reports_pending'] }}</p>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Banned sources</p>
                <p class="tl-stat-value mb-0 text-danger">{{ $stats['sources_banned'] }}</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card tl-card border-0 h-100">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-2"><i class="bi bi-journal-text me-2 text-muted"></i> All articles</h2>
                    <p class="small text-secondary mb-3">Browse every submission from all users, filter by status, and open details.</p>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Open articles</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card tl-card border-0 h-100">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-2"><i class="bi bi-flag me-2 text-muted"></i> Report queue</h2>
                    <p class="small text-secondary mb-3">Review user flags for misleading, fabricated, or out-of-context content.</p>
                    <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="btn btn-sm btn-outline-primary rounded-pill">Review reports</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card tl-card border-0 h-100">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-2"><i class="bi bi-globe2 me-2 text-muted"></i> Source domains</h2>
                    <p class="small text-secondary mb-3">Manage publisher domains, trust scores, and ban unreliable websites.</p>
                    <a href="{{ route('admin.sources.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Manage sources</a>
                </div>
            </div>
        </div>
    </div>
@endsection
