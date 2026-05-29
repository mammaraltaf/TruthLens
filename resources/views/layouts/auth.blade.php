<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/truthlens.css') }}?v=2" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}?v=1" rel="stylesheet">
</head>
<body class="tl-app">
    <div class="row g-0 min-vh-100 auth-split">
        <aside class="col-lg-5 col-xl-5 auth-aside d-none d-lg-flex flex-column justify-content-between">
            <div>
                <a href="{{ route('home') }}" class="auth-brand d-inline-flex align-items-center gap-3 text-white text-decoration-none">
                    <span class="auth-brand-mark d-inline-flex align-items-center justify-content-center rounded-3 flex-shrink-0">
                        <i class="bi bi-eye fs-5"></i>
                    </span>
                    <span class="d-flex flex-column lh-sm">
                        <span class="fw-bold fs-4 auth-brand-title">{{ config('app.name') }}</span>
                        <span class="small text-white-50 text-uppercase auth-brand-tag">Credibility checks</span>
                    </span>
                </a>
                <p class="auth-aside-lead mt-5 mb-0 text-white-50">
                    Submit a link or pasted article text for automated fact-check matching, then review community credibility votes on each result.
                </p>
                <ul class="auth-aside-list list-unstyled mt-4 mb-0 small text-white-50">
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-teal mt-1"></i><span>Automated matching against published fact-check reviews</span></li>
                    <li class="d-flex gap-2 mb-2"><i class="bi bi-check-circle-fill text-teal mt-1"></i><span>Community votes on every published result</span></li>
                    <li class="d-flex gap-2"><i class="bi bi-check-circle-fill text-teal mt-1"></i><span>Personal dashboard for your submission history</span></li>
                </ul>
            </div>
            <p class="small text-white-25 mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </aside>

        <div class="col-lg-7 col-xl-7 auth-form-panel d-flex align-items-stretch align-items-lg-center py-4 py-lg-5 px-3 px-sm-4">
            <div class="w-100 my-auto mx-auto" style="max-width: 28rem;">
                <div class="d-lg-none text-center mb-4">
                    <a href="{{ route('home') }}" class="auth-brand d-inline-flex align-items-center gap-2 text-dark text-decoration-none">
                        <span class="auth-brand-mark d-inline-flex align-items-center justify-content-center rounded-3"><i class="bi bi-eye"></i></span>
                        <span class="fw-bold fs-5">{{ config('app.name') }}</span>
                    </a>
                </div>

                <div class="card auth-card border-0 shadow-lg">
                    <div class="card-body p-4 p-sm-5">
                        {{ $slot }}
                    </div>
                </div>

                <p class="text-center mt-4 mb-0">
                    <a href="{{ route('home') }}" class="auth-footer-link small text-decoration-none">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('Back to feed') }}
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
