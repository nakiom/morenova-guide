<x-app-layout>
    <div class="container py-5">
        <a href="{{ route('center.credentials.index') }}" class="btn btn-outline-primary mb-3">
            <i class="fas fa-arrow-left me-1"></i>{{ __('messages.back') }}
        </a>

        <div class="card border-0 shadow-sm mx-auto" style="max-width: 40rem;">
            <div class="card-body p-4">
                <h1 class="h3 fw-bold text-dark-blue mb-1">{{ __('messages.change_password') }}</h1>
                <p class="text-muted mb-4">{{ $user->name }} · {{ $user->email }}</p>

                <form method="POST" action="{{ route('center.credentials.update', $user) }}">
                    @csrf
                    @method('PUT')

                    @if($user->is($manager))
                    <div class="mb-3">
                        <label for="current_password" class="form-label">{{ __('messages.current_password') }}</label>
                        <input type="password" name="current_password" id="current_password"
                               class="form-control @error('current_password') is-invalid @enderror"
                               autocomplete="current-password" required>
                        @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="password" class="form-label">{{ __('messages.new_password') }}</label>
                        <input type="password" name="password" id="password"
                               class="form-control @error('password') is-invalid @enderror"
                               autocomplete="new-password" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">{{ __('messages.password_confirmation') }}</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                               class="form-control" autocomplete="new-password" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>{{ __('messages.save') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
