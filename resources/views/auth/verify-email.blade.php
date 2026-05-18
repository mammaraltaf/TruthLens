<x-guest-layout>
    <div class="text-center mb-4">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary mb-3" style="width: 3.5rem; height: 3.5rem;">
            <i class="bi bi-envelope-check fs-4"></i>
        </span>
        <h1 class="h4 fw-bold mb-2" style="letter-spacing: -0.02em;">{{ __('Verify your email') }}</h1>
        <p class="text-secondary small mb-0 px-sm-1">
            {{ __('Thanks for signing up! Before you continue, please verify your email using the link we sent. Did not receive it? You can request another below.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success border-0 rounded-3 small py-3 mb-4" role="alert">
            <i class="bi bi-envelope-plus me-2"></i>{{ __('A new verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="d-grid gap-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-auth-primary w-100 py-2">{{ __('Resend verification email') }}</button>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-semibold">{{ __('Log out') }}</button>
        </form>
    </div>
</x-guest-layout>
