@extends('layouts.bootstrap')

@section('title', 'Check article — '.config('app.name'))

@section('content')
    <div class="mb-4">
        <a href="{{ route('articles.index') }}" class="tl-back-link"><i class="bi bi-arrow-left"></i> Back to feed</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-7 col-xl-8">
            <p class="tl-kicker mb-1">New submission</p>
            <h1 class="tl-section-title h2 mb-2">Check credibility</h1>
            <p class="text-secondary mb-4">Paste a URL to fetch the page text, or submit raw copy. Analysis uses the Google Fact Check Tools API when <code>GOOGLE_FACT_CHECK_API_KEY</code> is set.</p>

            <form method="post" action="{{ route('articles.store') }}" class="card tl-card tl-form-card border-0" id="article-form">
                @csrf
                <div class="card-body p-4 p-lg-5">
                    <div class="mb-4">
                        <label class="form-label fw-semibold"><i class="bi bi-sliders me-2 text-muted"></i>Submission type</label>
                        <select name="submission_type" id="submission_type" class="form-select form-select-lg" required>
                            <option value="url" @selected(old('submission_type', 'url') === 'url')>Article URL — we fetch and clean the HTML</option>
                            <option value="text" @selected(old('submission_type') === 'text')>Raw text — paste the story body yourself</option>
                        </select>
                    </div>

                    <div class="mb-4" id="field-url-wrap">
                        <label for="url" class="form-label fw-semibold"><i class="bi bi-link-45deg me-2 text-muted"></i>URL</label>
                        <div class="input-group input-group-lg tl-input-group-icon">
                            <span class="input-group-text border-end-0"><i class="bi bi-globe2"></i></span>
                            <input type="url" name="url" id="url" value="{{ old('url') }}" class="form-control border-start-0 ps-0" placeholder="https://example.com/news/...">
                        </div>
                        <div class="form-text" id="help-url">Required for URL mode. We strip tags and send a text excerpt to the fact-check API.</div>
                    </div>

                    <div class="mb-4" id="field-text-wrap">
                        <label for="content" class="form-label fw-semibold"><i class="bi bi-text-left me-2 text-muted"></i>Text</label>
                        <textarea name="content" id="content" rows="10" class="form-control" placeholder="Paste the article text (minimum 40 characters in text mode)">{{ old('content') }}</textarea>
                        <div class="form-text" id="help-text">Required for text mode.</div>
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-md-6">
                            <label for="title" class="form-label fw-semibold">Title <span class="text-muted fw-normal small">(optional)</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" placeholder="Headline override">
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Category <span class="text-muted fw-normal small">(optional)</span></label>
                            <input type="text" name="category" id="category" value="{{ old('category') }}" class="form-control" placeholder="Politics, Health, …">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 px-4 px-lg-5 pb-4 pt-0 d-flex flex-wrap justify-content-end gap-2">
                    <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancel</a>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm">
                        <i class="bi bi-play-fill me-1"></i> Run analysis
                    </button>
                </div>
            </form>
        </div>
        <div class="col-lg-5 col-xl-4">
            <div class="card tl-card border-0 sticky-lg-top" style="top: 1rem;">
                <div class="card-body p-4">
                    <h2 class="h6 fw-bold mb-3"><i class="bi bi-lightbulb text-warning me-2"></i>Tips</h2>
                    <ul class="small text-secondary ps-3 mb-0" style="line-height: 1.7;">
                        <li class="mb-2">The API matches <strong>claims</strong>, not full stories—clear headlines or quotes work best.</li>
                        <li class="mb-2"><strong>No API key</strong> still saves your submission; the badge will show as unverified.</li>
                        <li class="mb-0">Duplicate text reuses the prior score and links submissions.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const type = document.getElementById('submission_type');
            const urlWrap = document.getElementById('field-url-wrap');
            const textWrap = document.getElementById('field-text-wrap');
            function sync() {
                const isUrl = type.value === 'url';
                urlWrap.classList.toggle('opacity-50', !isUrl);
                textWrap.classList.toggle('opacity-50', isUrl);
                document.getElementById('url').toggleAttribute('disabled', !isUrl);
                document.getElementById('content').toggleAttribute('disabled', isUrl);
            }
            type.addEventListener('change', sync);
            sync();
        })();
    </script>
@endpush
