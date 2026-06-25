<nav class="mb-4">
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if (request()->routeIs('admin.dashboard')) btn-primary @else btn-outline-secondary @endif">
            <i class="bi bi-grid me-1"></i> Overview
        </a>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if (request()->routeIs('admin.articles.*')) btn-primary @else btn-outline-secondary @endif">
            <i class="bi bi-journal-text me-1"></i> All articles
        </a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if (request()->routeIs('admin.reports.*')) btn-primary @else btn-outline-secondary @endif">
            <i class="bi bi-flag me-1"></i> Reports
        </a>
        <a href="{{ route('admin.sources.index') }}" class="btn btn-sm rounded-pill fw-semibold px-3 @if (request()->routeIs('admin.sources.*')) btn-primary @else btn-outline-secondary @endif">
            <i class="bi bi-globe2 me-1"></i> Sources
        </a>
    </div>
</nav>
