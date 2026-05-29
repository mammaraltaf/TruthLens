<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;0,9..40,800;1,9..40,400&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/truthlens.css') }}?v=1" rel="stylesheet">
    @stack('head')
</head>
<body class="tl-app min-vh-100 d-flex flex-column">
    <nav class="navbar navbar-expand-lg navbar-dark tl-navbar">
        <div class="container">
            <a class="navbar-brand tl-brand d-flex align-items-center text-white" href="{{ route('home') }}">
                <span class="tl-brand-mark" aria-hidden="true"><i class="bi bi-eye"></i></span>
                <span class="d-flex flex-column lh-sm py-1">
                    <span>{{ config('app.name') }}</span>
                    <span class="tl-brand-sub">Credibility platform</span>
                </span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('articles.index') }}"><i class="bi bi-collection me-1 opacity-75"></i> Feed</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('articles.create') }}"><i class="bi bi-shield-check me-1 opacity-75"></i> Check</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-1 opacity-75"></i> Dashboard</a>
                        </li>
                    @endauth
                </ul>
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                    @guest
                        <li class="nav-item">
                            <a class="nav-link text-white" href="{{ route('login') }}">{{ __('Log in') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-sm btn-light text-dark fw-semibold px-3 rounded-pill" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1 fs-5"></i>
                                <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 mt-2">
                                <li><a class="dropdown-item rounded-2" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2 text-muted"></i> Profile</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item rounded-2 text-danger"><i class="bi bi-box-arrow-right me-2"></i> Log out</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    <main class="tl-main flex-grow-1">
        <div class="container">
            @if (session('status'))
                <div class="alert alert-success tl-alert alert-dismissible fade show border-0 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger tl-alert border-0 mb-4" role="alert">
                    <div class="fw-semibold mb-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Please fix the following</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="tl-footer mt-auto">
        <div class="container d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2">
            <span><i class="bi bi-info-circle me-1"></i> Scores reflect matched fact-check reviews and community input. They are indicators only—not a final judgment.</span>
            <span class="text-nowrap small">&copy; {{ date('Y') }} {{ config('app.name') }}</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
