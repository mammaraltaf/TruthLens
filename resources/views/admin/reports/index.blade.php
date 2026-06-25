@extends('layouts.bootstrap')

@section('title', 'Moderation reports — '.config('app.name'))

@section('content')
    @include('admin._nav')

    <p class="tl-kicker mb-1">Moderation</p>
    <h1 class="tl-section-title h2 mb-4">Article reports</h1>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-sm-4">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Pending</p>
                <p class="tl-stat-value mb-0 text-warning">{{ $stats['pending'] }}</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Reviewed</p>
                <p class="tl-stat-value mb-0 text-success">{{ $stats['reviewed'] }}</p>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Dismissed</p>
                <p class="tl-stat-value mb-0 text-secondary">{{ $stats['dismissed'] }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.reports.index') }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === '') btn-primary @else btn-outline-secondary @endif">All</a>
            <a href="{{ route('admin.reports.index', ['status' => 'pending']) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === 'pending') btn-primary @else btn-outline-secondary @endif">Pending</a>
            <a href="{{ route('admin.reports.index', ['status' => 'reviewed']) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === 'reviewed') btn-primary @else btn-outline-secondary @endif">Reviewed</a>
            <a href="{{ route('admin.reports.index', ['status' => 'dismissed']) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if ($status === 'dismissed') btn-primary @else btn-outline-secondary @endif">Dismissed</a>
        </div>
        <a href="{{ route('admin.reports.export', $status !== '' ? ['status' => $status] : []) }}" class="btn btn-sm btn-outline-primary rounded-pill fw-semibold px-3">
            <i class="bi bi-download me-1"></i> Download CSV
        </a>
    </div>

    <div class="tl-table-wrap bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>Article</th>
                        <th>Reporter</th>
                        <th>Category</th>
                        <th>Details</th>
                        <th>Status</th>
                        <th>Reviewed by</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $report->id }}</td>
                            <td style="max-width: 200px;">
                                <div class="fw-semibold small text-truncate">{{ $report->article->title ?: 'Untitled' }}</div>
                                <a href="{{ route('articles.show', $report->article) }}" class="small">View article #{{ $report->article_id }}</a>
                            </td>
                            <td>
                                <div class="small fw-semibold">{{ $report->user->name }}</div>
                                <div class="text-muted small">{{ $report->user->email }}</div>
                            </td>
                            <td><span class="tl-meta-pill text-capitalize small">{{ str_replace('_', ' ', $report->category->value) }}</span></td>
                            <td class="small text-secondary" style="max-width: 180px;">{{ \Illuminate\Support\Str::limit($report->details, 80) ?: '—' }}</td>
                            <td><span class="tl-meta-pill text-capitalize small">{{ $report->status->value }}</span></td>
                            <td class="small text-secondary">
                                @if ($report->reviewer)
                                    {{ $report->reviewer->name }}
                                    <div class="text-muted">{{ $report->reviewed_at?->timezone(config('app.timezone'))->format('M j, Y') }}</div>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="pe-4 text-end text-nowrap">
                                @if ($report->status === \App\Enums\ReportStatus::Pending)
                                    <form method="post" action="{{ route('admin.reports.update', $report) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="reviewed">
                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">Reviewed</button>
                                    </form>
                                    <form method="post" action="{{ route('admin.reports.update', $report) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="dismissed">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary rounded-pill px-3">Dismiss</button>
                                    </form>
                                @else
                                    <span class="text-muted small">Resolved</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-secondary py-5">No reports found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($reports->hasPages())
        <div class="d-flex justify-content-center mt-4">{{ $reports->links() }}</div>
    @endif
@endsection
