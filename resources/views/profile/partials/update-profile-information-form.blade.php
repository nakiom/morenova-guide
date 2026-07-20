<section>
    <div class="d-flex align-items-start gap-3 mb-4">
        <span class="profile-section-icon">
            <i class="fas fa-id-card"></i>
        </span>
        <div>
            <h2 class="h5 fw-bold text-dark-blue mb-1">{{ __('messages.profile_information') }}</h2>
            <p class="text-muted small mb-0">{{ __('messages.profile_information_hint') }}</p>
        </div>
    </div>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="row g-3">
            <div class="col-12">
                <label for="name" class="form-label">{{ __('messages.name') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
                </div>
                @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12">
                <label for="email" class="form-label">{{ __('messages.email') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required autocomplete="username">
                </div>
                @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-3 mb-0 py-2">
                    <small>
                        {{ __('messages.email_unverified') }}
                        <button form="send-verification" type="submit" class="btn btn-link btn-sm p-0 align-baseline">
                            {{ __('messages.resend_verification') }}
                        </button>
                    </small>
                    @if (session('status') === 'verification-link-sent')
                    <div class="small text-success mt-1">{{ __('messages.verification_sent') }}</div>
                    @endif
                </div>
                @endif
            </div>

            <div class="col-12 col-md-6">
                <label for="photo" class="form-label">{{ __('messages.profile_photo') }}</label>
                <input type="file" name="photo" id="photo"
                       class="form-control @error('photo') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/jpg,image/webp">
                <small class="text-muted">{{ __('messages.profile_photo_file_hint') }}</small>
                @error('photo')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-12 col-md-6">
                <label for="photo_url" class="form-label">{{ __('messages.profile_photo_url') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-link"></i>
                    <input type="url" name="photo_url" id="photo_url"
                           class="form-control @error('photo_url') is-invalid @enderror"
                           value="{{ old('photo_url', $user->photo_url) }}"
                           placeholder="https://ejemplo.com/foto.jpg">
                </div>
                <small class="text-muted">{{ __('messages.profile_photo_url_hint') }}</small>
                @error('photo_url')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-3 mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>{{ __('messages.save') }}
            </button>

            @if (session('status') === 'profile-updated')
            <span class="text-success small fw-semibold" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2500)">
                <i class="fas fa-check-circle me-1"></i>{{ __('messages.profile_saved') }}
            </span>
            @endif
        </div>
    </form>
</section>
