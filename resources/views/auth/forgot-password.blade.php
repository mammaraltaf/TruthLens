<x-guest-layout>
    <h1 class="h4 fw-bold mb-1" style="letter-spacing: -0.02em;">{{ __('Reset your password') }}</h1>
    <p class="text-secondary small mb-4">
        {{ __('Forgot your password? No problem — enter your email and we will send a reset link.') }}
    </p>

    @if (session('status'))
        <div class="alert alert-success border-0 rounded-3 small py-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" novalidate>
        @csrf

        <div class="mb-4">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="you@example.com">
            </div>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-auth-primary w-100 py-2">{{ __('Email password reset link') }}</button>
    </form>

    <p class="text-center small mt-4 mb-0">
        <a href="{{ route('login') }}" class="btn-auth-ghost text-decoration-none">← {{ __('Back to log in') }}</a>
    </p>
</x-guest-layout>
