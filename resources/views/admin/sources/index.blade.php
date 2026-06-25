@extends('layouts.bootstrap')

@section('title', 'Source domains — '.config('app.name'))

@section('content')
    @include('admin._nav')

    <p class="tl-kicker mb-1">Moderation</p>
    <h1 class="tl-section-title h2 mb-4">Publisher sources</h1>

    <div class="row g-3 g-lg-4 mb-4">
        <div class="col-sm-6">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Total domains</p>
                <p class="tl-stat-value mb-0">{{ $stats['total'] }}</p>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="tl-stat-card">
                <p class="tl-stat-label mb-1">Banned</p>
                <p class="tl-stat-value mb-0 text-danger">{{ $stats['banned'] }}</p>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.sources.index') }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if (! request()->boolean('banned')) btn-primary @else btn-outline-secondary @endif">All</a>
        <a href="{{ route('admin.sources.index', ['banned' => 1]) }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if (request()->boolean('banned')) btn-primary @else btn-outline-secondary @endif">Banned only</a>
    </div>

    <div class="tl-table-wrap bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Domain</th>
                        <th>Trust score</th>
                        <th>Articles</th>
                        <th>Status</th>
                        <th class="pe-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sources as $source)
                        <tr>
                            <td class="ps-4 fw-semibold">{{ $source->domain }}</td>
                            <td>
                                <form method="post" action="{{ route('admin.sources.update', $source) }}" class="d-flex align-items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_banned" value="{{ $source->is_banned ? '1' : '0' }}">
                                    <input type="number" name="trust_score" value="{{ $source->trust_score }}" min="0" max="100" class="form-control form-control-sm" style="width: 5rem;">
                                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill">Save</button>
                                </form>
                            </td>
                            <td>{{ $source->articles_count }}</td>
                            <td>
                                @if ($source->is_banned)
                                    <span class="badge rounded-pill text-bg-danger">Banned</span>
                                @else
                                    <span class="badge rounded-pill text-bg-success">Active</span>
                                @endif
                            </td>
                            <td class="pe-4 text-end">
                                <form method="post" action="{{ route('admin.sources.update', $source) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="trust_score" value="{{ $source->trust_score }}">
                                    <input type="hidden" name="is_banned" value="{{ $source->is_banned ? '0' : '1' }}">
                                    @if ($source->is_banned)
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill px-3">Unban</button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">Ban</button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-secondary py-5">No sources recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($sources->hasPages())
        <div class="d-flex justify-content-center mt-4">{{ $sources->links() }}</div>
    @endif
@endsection
