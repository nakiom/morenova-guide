<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('admin.crm-users.show', $user) }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
        </a>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 40rem;">
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="feedback-icon-badge mb-0 flex-shrink-0">
                        <i class="fas fa-key"></i>
                    </span>
                    <div>
                        <h1 class="h3 fw-bold text-dark-blue mb-1">{{ __('messages.change_password') }}</h1>
                        <p class="text-muted mb-0">
                            {{ $user->name }} · {{ $user->email }}
                        </p>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-shield-halved me-2"></i>
                    {{ __('messages.admin_password_reset_hint') }}
                </div>

                <form method="POST" action="{{ route('admin.crm-users.password.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('messages.new_password') }}</label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control @error('password') is-invalid @enderror"
                                autocomplete="new-password"
                                autofocus
                                required
                            >
                        </div>
                        @error('password')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            {{ __('messages.password_confirmation') }}
                        </label>
                        <div class="input-icon-wrap">
                            <i class="fas fa-lock"></i>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control"
                                autocomplete="new-password"
                                required
                            >
                        </div>
                    </div>

                    <div class="d-flex flex-wrap justify-content-end gap-2">
                        <a href="{{ route('admin.crm-users.show', $user) }}" class="btn btn-outline-secondary">
                            {{ __('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>{{ __('messages.update_password') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
