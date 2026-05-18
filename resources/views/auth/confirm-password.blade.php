<x-guest-layout>
    <div class="text-center mb-4">
        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary mb-3" style="width: 3.5rem; height: 3.5rem;">
            <i class="bi bi-shield-lock fs-4"></i>
        </span>
        <h1 class="h4 fw-bold mb-2" style="letter-spacing: -0.02em;">{{ __('Confirm password') }}</h1>
        <p class="text-secondary small mb-0">{{ __('This is a secure area. Please enter your password to continue.') }}</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" novalidate>
        @csrf

        <div class="mb-4">
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

        <button type="submit" class="btn btn-auth-primary w-100 py-2">{{ __('Confirm') }}</button>
    </form>
</x-guest-layout>
