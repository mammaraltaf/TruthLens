<div class="card tl-card border-0 mb-4">
    <div class="card-body p-4 p-md-5">
        <h2 class="h5 fw-bold mb-1">{{ __('Update password') }}</h2>
        <p class="text-secondary small mb-4">{{ __('Use a long, random password to keep your account secure.') }}</p>

        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="update_password_current_password" class="form-label fw-semibold">{{ __('Current password') }}</label>
                <input id="update_password_current_password" name="current_password" type="password"
                    class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                    autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="update_password_password" class="form-label fw-semibold">{{ __('New password') }}</label>
                <input id="update_password_password" name="password" type="password"
                    class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password">
                @error('password', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="update_password_password_confirmation" class="form-label fw-semibold">{{ __('Confirm password') }}</label>
                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror"
                    autocomplete="new-password">
                @error('password_confirmation', 'updatePassword')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background: linear-gradient(135deg, #0d9488, #0f766e); border: none;">
                    {{ __('Save') }}
                </button>
                @if (session('status') === 'password-updated')
                    <span class="small text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>{{ __('Saved.') }}</span>
                @endif
            </div>
        </form>
    </div>
</div>
