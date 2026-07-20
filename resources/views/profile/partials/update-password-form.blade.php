<section>
    <div class="d-flex align-items-start gap-3 mb-4">
        <span class="profile-section-icon">
            <i class="fas fa-lock"></i>
        </span>
        <div>
            <h2 class="h5 fw-bold text-dark-blue mb-1">{{ __('messages.profile_password_section') }}</h2>
            <p class="text-muted small mb-0">{{ __('messages.profile_password_hint') }}</p>
        </div>
    </div>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">{{ __('messages.current_password') }}</label>
            <div class="input-icon-wrap">
                <i class="fas fa-key"></i>
                <input type="password" id="update_password_current_password" name="current_password"
                       class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                       autocomplete="current-password">
            </div>
            @error('current_password', 'updatePassword')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label">{{ __('messages.new_password') }}</label>
            <div class="input-icon-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" id="update_password_password" name="password"
                       class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                       autocomplete="new-password">
            </div>
            @error('password', 'updatePassword')
            <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="update_password_password_confirmation" class="form-label">{{ __('messages.password_confirmation') }}</label>
            <div class="input-icon-wrap">
                <i class="fas fa-lock"></i>
                <input type="password" id="update_password_password_confirmation" name="password_confirmation"
                       class="form-control" autocomplete="new-password">
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-shield-halved me-2"></i>{{ __('messages.update_password') }}
            </button>

            @if (session('status') === 'password-updated')
            <span class="text-success small fw-semibold" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)">
                <i class="fas fa-check-circle me-1"></i>{{ __('messages.password_updated') }}
            </span>
            @endif
        </div>
    </form>
</section>
