<section>
    <div class="d-flex align-items-start gap-3 mb-3">
        <span class="profile-section-icon profile-section-icon--danger">
            <i class="fas fa-triangle-exclamation"></i>
        </span>
        <div>
            <h2 class="h5 fw-bold text-danger mb-1">{{ __('messages.profile_delete_section') }}</h2>
            <p class="text-muted small mb-0">{{ __('messages.profile_delete_hint') }}</p>
        </div>
    </div>

    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i class="fas fa-trash-alt me-2"></i>{{ __('messages.delete_account') }}
    </button>

    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content feedback-modal border-0">
                <div class="modal-header feedback-modal-header bg-danger border-0">
                    <h5 class="modal-title text-white" id="confirmUserDeletionLabel">
                        {{ __('messages.delete_account_confirm') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">{{ __('messages.delete_account_warning') }}</p>
                        <label for="password" class="form-label">{{ __('messages.password') }}</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password"
                                   class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                                   placeholder="{{ __('messages.password') }}" required>
                        </div>
                        @error('password', 'userDeletion')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer feedback-modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ __('messages.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt me-2"></i>{{ __('messages.delete_account') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($errors->userDeletion->isNotEmpty())
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
            modal.show();
        });
    </script>
    @endif
</section>
