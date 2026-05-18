<div class="card tl-card border-0 mb-4 border-start border-danger border-4 rounded-3">
    <div class="card-body p-4 p-md-5">
        <h2 class="h5 fw-bold mb-1 text-danger">{{ __('Delete account') }}</h2>
        <p class="text-secondary small mb-4">
            {{ __('Once your account is deleted, all of its data is permanently removed. Download anything you need before proceeding.') }}
        </p>

        <button type="button" class="btn btn-outline-danger rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            <i class="bi bi-trash3 me-1"></i>{{ __('Delete account') }}
        </button>
    </div>
</div>

<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <div class="modal-header border-0 pb-0">
                    <h2 class="modal-title h5 fw-bold" id="deleteAccountModalLabel">{{ __('Delete account?') }}</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-secondary small mb-4">
                        {{ __('Enter your password to confirm permanent deletion.') }}
                    </p>
                    <label for="delete_account_password" class="form-label fw-semibold">{{ __('Password') }}</label>
                    <input id="delete_account_password" name="password" type="password"
                        class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                        placeholder="{{ __('Password') }}"
                        autocomplete="current-password">
                    @error('password', 'userDeletion')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="modal-footer border-0 pt-0 flex-nowrap gap-2">
                    <button type="button" class="btn btn-light rounded-pill flex-grow-1" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger rounded-pill flex-grow-1 fw-semibold">{{ __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->userDeletion->isNotEmpty())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var el = document.getElementById('deleteAccountModal');
                if (el && typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(el).show();
                }
            });
        </script>
    @endpush
@endif
