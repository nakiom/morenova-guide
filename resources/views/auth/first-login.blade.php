<x-guest-layout>
    <div class="first-login">
        <h1 class="h4 fw-bold text-dark-blue mb-2">{{ __('messages.first_login_title') }}</h1>
        <p class="text-muted small mb-3">{{ __('messages.first_login_intro') }}</p>

        <div class="alert alert-warning border-0 small mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i>{{ __('messages.first_login_required_notice') }}
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('first-login.store') }}" id="first-login-form">
            @csrf

            @if ($mustAcceptPrivacy)
                <div class="first-login__privacy mb-4">
                    <h2 class="h6 fw-bold text-dark-blue mb-2">
                        <i class="fas fa-shield-alt me-1 text-primary"></i>{{ __('messages.first_login_privacy_title') }}
                    </h2>
                    <div class="first-login__privacy-box small">
                        <p class="fw-semibold mb-2">{{ __('messages.first_login_privacy_lead') }}</p>
                        <p class="mb-2">{{ __('messages.first_login_privacy_p1') }}</p>
                        <p class="mb-1 fw-semibold">{{ __('messages.first_login_privacy_shared_title') }}</p>
                        <ul class="mb-2 ps-3">
                            <li>{{ __('messages.first_login_privacy_item_storage') }}</li>
                            <li>{{ __('messages.first_login_privacy_item_shared') }}</li>
                            <li>{{ __('messages.first_login_privacy_item_purpose') }}</li>
                        </ul>
                        <p class="mb-1 fw-semibold">{{ __('messages.first_login_privacy_not_shared_title') }}</p>
                        <ul class="mb-2 ps-3">
                            <li>{{ __('messages.first_login_privacy_item_not_shared') }}</li>
                        </ul>
                        <p class="mb-0">{{ __('messages.first_login_privacy_p2') }}</p>
                    </div>

                    <div class="form-check mt-3 p-3 rounded border border-primary-subtle bg-primary-subtle">
                        <input
                            class="form-check-input @error('privacy_accepted') is-invalid @enderror"
                            type="checkbox"
                            name="privacy_accepted"
                            id="privacy_accepted"
                            value="1"
                            {{ old('privacy_accepted') ? 'checked' : '' }}
                            required
                        >
                        <label class="form-check-label fw-semibold" for="privacy_accepted">
                            {{ __('messages.first_login_privacy_accept') }}
                        </label>
                        @error('privacy_accepted')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="small text-muted mt-1">{{ __('messages.first_login_privacy_accept_hint') }}</div>
                    </div>
                </div>
            @endif

            @if ($mustChangePassword)
                <div class="first-login__password mb-4">
                    <h2 class="h6 fw-bold text-dark-blue mb-2">
                        <i class="fas fa-key me-1 text-primary"></i>{{ __('messages.first_login_password_title') }}
                    </h2>
                    <p class="small text-muted mb-3">{{ __('messages.first_login_password_hint') }}</p>

                    <div class="mb-3">
                        <label class="form-label" for="password">{{ __('messages.new_password') }}</label>
                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            required
                            autocomplete="new-password"
                        >
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label class="form-label" for="password_confirmation">{{ __('messages.password_confirmation') }}</label>
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required
                            autocomplete="new-password"
                        >
                    </div>
                </div>
            @endif

            <button type="submit" class="btn btn-primary w-100" id="first-login-submit">
                {{ __('messages.first_login_continue') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-3 text-center">
            @csrf
            <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">
                {{ __('messages.logout') }}
            </button>
        </form>
    </div>

    @if ($mustAcceptPrivacy)
    <script>
        (() => {
            const form = document.getElementById('first-login-form');
            const checkbox = document.getElementById('privacy_accepted');
            if (!form || !checkbox) return;

            form.addEventListener('submit', (event) => {
                if (!checkbox.checked) {
                    event.preventDefault();
                    checkbox.focus();
                    checkbox.classList.add('is-invalid');
                }
            });
        })();
    </script>
    @endif
</x-guest-layout>
