@php $user = $user ?? null; @endphp

<div class="form-stack">
    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-user"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.user_section_account') }}</h2>
                <p class="form-section-hint">{{ __('messages.user_section_account_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-user"></i>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name ?? '') }}"
                           placeholder="{{ __('messages.user_name_placeholder') }}" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="email">{{ __('messages.email') }} <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email ?? '') }}"
                           placeholder="usuario@centro.com" required>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password">{{ __('messages.password') }} @if(!$user)<span class="text-danger">*</span>@endif</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" class="form-control"
                           placeholder="••••••••" @if(!$user) required @endif autocomplete="new-password">
                </div>
                @if($user)<small class="form-text text-muted">{{ __('messages.password_leave_blank') }}</small>@endif
            </div>
            <div class="col-md-6">
                <label class="form-label" for="password_confirmation">{{ __('messages.password_confirmation') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                           placeholder="••••••••" autocomplete="new-password">
                </div>
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-sitemap"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.user_section_org') }}</h2>
                <p class="form-section-hint">{{ __('messages.user_section_org_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="center_id">{{ __('messages.center') }} <span class="text-danger">*</span></label>
                <select name="center_id" id="center_id" class="form-select" required>
                    <option value="">{{ __('messages.select_center') }}</option>
                    @foreach($centers as $center)
                    <option value="{{ $center->id }}" @selected(old('center_id', $user->center_id ?? $centerId ?? null) == $center->id)>
                        {{ $center->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="parent_user_id">{{ __('messages.parent_user') }}</label>
                <select name="parent_user_id" id="parent_user_id" class="form-select">
                    <option value="">{{ __('messages.primary_user') }}</option>
                    @foreach($primaryUsers as $primary)
                    <option value="{{ $primary->id }}" @selected(old('parent_user_id', $user->parent_user_id ?? null) == $primary->id)>
                        {{ $primary->name }}
                    </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">{{ __('messages.parent_user_hint') }}</small>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="phone">{{ __('messages.phone') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" id="phone" class="form-control"
                           value="{{ old('phone', $user->phone ?? '') }}"
                           placeholder="{{ __('messages.phone_placeholder') }}"
                           data-mask="phone" inputmode="tel">
                </div>
                <small class="form-text text-muted">{{ __('messages.phone_hint') }}</small>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="location">{{ __('messages.location') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" id="location" class="form-control"
                           value="{{ old('location', $user->location ?? '') }}"
                           placeholder="{{ __('messages.user_location_placeholder') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="job_title">{{ __('messages.job_title') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-briefcase"></i>
                    <input type="text" name="job_title" id="job_title" class="form-control"
                           value="{{ old('job_title', $user->job_title ?? '') }}"
                           placeholder="{{ __('messages.job_title_placeholder') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label" for="preferred_locale">{{ __('messages.preferred_locale') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-language"></i>
                    <select name="preferred_locale" id="preferred_locale" class="form-select">
                        <option value="">{{ __('messages.preferred_locale_auto') }}</option>
                        @foreach(\App\Support\Locales::labels() as $code => $label)
                            <option value="{{ $code }}" @selected(old('preferred_locale', $user->preferred_locale ?? null) === $code)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <small class="form-text text-muted">{{ __('messages.preferred_locale_hint') }}</small>
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-image"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.profile_photo') }}</h2>
                <p class="form-section-hint">{{ __('messages.profile_photo_file_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="photo">{{ __('messages.profile_photo') }}</label>
                <input type="file" name="photo" id="photo" class="form-control" accept="image/jpeg,image/png,image/jpg,image/webp">
                @error('photo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="photo_url">{{ __('messages.profile_photo_url') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-link"></i>
                    <input type="url" name="photo_url" id="photo_url"
                           class="form-control @error('photo_url') is-invalid @enderror"
                           value="{{ old('photo_url', $user->photo_url ?? '') }}"
                           placeholder="https://ejemplo.com/foto.jpg">
                </div>
                <small class="form-text text-muted">{{ __('messages.profile_photo_url_hint') }}</small>
                @error('photo_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            @if($user?->photoUrl())
            <div class="col-12">
                <div class="d-flex align-items-center gap-3 p-3 rounded form-switch-card">
                    <img src="{{ $user->photoUrl() }}" alt="{{ $user->name }}" class="rounded-circle" style="width:72px;height:72px;object-fit:cover;">
                    <div>
                        <div class="fw-semibold">{{ __('messages.current_profile_photo') }}</div>
                        <small class="text-muted">{{ $user->photo_url ? __('messages.photo_source_url') : __('messages.photo_source_file') }}</small>
                    </div>
                </div>
            </div>
            @endif
            <div class="col-12">
                <div class="form-switch-card">
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               @checked(old('is_active', $user->is_active ?? true))>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('messages.active') }}</label>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
