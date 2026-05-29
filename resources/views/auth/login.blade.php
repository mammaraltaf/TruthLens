<x-guest-layout>
    <h1 class="h4 fw-bold mb-1" style="letter-spacing: -0.02em;">{{ __('Welcome back') }}</h1>
    <p class="text-secondary small mb-4">{{ __('Sign in to check articles, vote, and track your submissions.') }}</p>

    @if (session('status'))
        <div class="alert alert-success border-0 rounded-3 small py-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="{{ __('Email address') }}">
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-lock"></i></span>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••">
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
            <div class="form-check">
                <input id="remember_me" type="checkbox" name="remember" class="form-check-input border-secondary">
                <label for="remember_me" class="form-check-label small text-secondary">{{ __('Remember me') }}</label>
            </div>
            @if (Route::has('password.request'))
                <a class="btn-auth-ghost small text-decoration-none" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
            @endif
        </div>

        <button type="submit" class="btn btn-auth-primary w-100 py-2">{{ __('Log in') }}</button>
    </form>

    <p class="text-center small text-secondary mt-4 mb-0">
        {{ __('No account yet?') }}
        <a href="{{ route('register') }}" class="fw-semibold text-decoration-none" style="color: #0f766e;">{{ __('Create one') }}</a>
    </p>
</x-guest-layout>
