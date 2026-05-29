<x-guest-layout>
    <h1 class="h4 fw-bold mb-1" style="letter-spacing: -0.02em;">{{ __('Create your account') }}</h1>
    <p class="text-secondary small mb-4">{{ __('Create an account to submit articles, vote on results, and track your activity.') }}</p>

    <form method="POST" action="{{ route('register') }}" class="auth-form" novalidate>
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">{{ __('Name') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-person"></i></span>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                    class="form-control border-start-0 ps-0 @error('name') is-invalid @enderror" placeholder="{{ __('Your name') }}">
            </div>
            @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
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
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="form-control border-start-0 ps-0 @error('password') is-invalid @enderror" placeholder="••••••••">
            </div>
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">{{ __('Confirm password') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-lock-fill"></i></span>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="form-control border-start-0 ps-0" placeholder="••••••••">
            </div>
        </div>

        <button type="submit" class="btn btn-auth-primary w-100 py-2">{{ __('Register') }}</button>
    </form>

    <p class="text-center small text-secondary mt-4 mb-0">
        {{ __('Already registered?') }}
        <a href="{{ route('login') }}" class="fw-semibold text-decoration-none" style="color: #0f766e;">{{ __('Log in') }}</a>
    </p>
</x-guest-layout>
