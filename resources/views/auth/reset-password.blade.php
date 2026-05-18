<x-guest-layout>
    <h1 class="h4 fw-bold mb-1" style="letter-spacing: -0.02em;">{{ __('Choose a new password') }}</h1>
    <p class="text-secondary small mb-4">{{ __('Enter your email and a new password below.') }}</p>

    <form method="POST" action="{{ route('password.store') }}" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('Email') }}</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-secondary"><i class="bi bi-envelope"></i></span>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                    class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="you@example.com">
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

        <button type="submit" class="btn btn-auth-primary w-100 py-2">{{ __('Reset password') }}</button>
    </form>
</x-guest-layout>
