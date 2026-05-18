<div class="card tl-card border-0 mb-4">
    <div class="card-body p-4 p-md-5">
        <h2 class="h5 fw-bold mb-1">{{ __('Profile information') }}</h2>
        <p class="text-secondary small mb-4">{{ __("Update your account's profile information and email address.") }}</p>

        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">{{ __('Name') }}</label>
                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="bio" class="form-label fw-semibold">{{ __('Bio') }}</label>
                <textarea id="bio" name="bio" rows="4" class="form-control @error('bio') is-invalid @enderror" placeholder="{{ __('Short introduction (optional)') }}">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="form-label fw-semibold">{{ __('Email') }}</label>
                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="alert alert-warning border-0 rounded-3 small mt-3 mb-0">
                        {{ __('Your email address is unverified.') }}
                        <button type="submit" form="send-verification" class="btn btn-link btn-sm p-0 align-baseline text-decoration-none fw-semibold" style="color: #0f766e;">
                            {{ __('Resend verification email') }}
                        </button>
                        @if (session('status') === 'verification-link-sent')
                            <span class="d-block mt-2 text-success fw-semibold">{{ __('A new verification link has been sent.') }}</span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="d-flex flex-wrap align-items-center gap-3">
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-semibold" style="background: linear-gradient(135deg, #0d9488, #0f766e); border: none;">
                    {{ __('Save') }}
                </button>
                @if (session('status') === 'profile-updated')
                    <span class="small text-success fw-semibold"><i class="bi bi-check-circle-fill me-1"></i>{{ __('Saved.') }}</span>
                @endif
            </div>
        </form>
    </div>
</div>
