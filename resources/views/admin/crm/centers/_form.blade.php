@php
    $center = $center ?? null;
@endphp

<div class="form-stack">
    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-building"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.center_section_identity') }}</h2>
                <p class="form-section-hint">{{ __('messages.center_section_identity_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="name">{{ __('messages.name') }} <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-hospital"></i>
                    <input type="text" name="name" id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $center->name ?? '') }}"
                           placeholder="{{ __('messages.center_name_placeholder') }}"
                           required>
                </div>
                <small class="form-text text-muted">{{ __('messages.center_name_hint') }}</small>
                @error('name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="logo">{{ __('messages.center_logo') }}</label>
                @if($center?->logoUrl())
                <div class="mb-2">
                    <img src="{{ $center->logoUrl() }}" alt="{{ $center->name }}"
                         class="rounded border bg-white p-2"
                         style="max-height: 72px; max-width: 180px; object-fit: contain;">
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="remove_logo" id="remove_logo" value="1"
                           @checked(old('remove_logo'))>
                    <label class="form-check-label" for="remove_logo">{{ __('messages.center_logo_remove') }}</label>
                </div>
                @endif
                <input type="file" name="logo" id="logo" accept="image/*"
                       class="form-control @error('logo') is-invalid @enderror">
                <small class="form-text text-muted">{{ __('messages.center_logo_hint') }}</small>
                @error('logo')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="location">{{ __('messages.location') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" id="location"
                           class="form-control @error('location') is-invalid @enderror"
                           value="{{ old('location', $center->location ?? '') }}"
                           placeholder="{{ __('messages.center_location_placeholder') }}">
                </div>
                <small class="form-text text-muted">{{ __('messages.center_location_hint') }}</small>
                @error('location')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label" for="address">{{ __('messages.address') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-road"></i>
                    <input type="text" name="address" id="address"
                           class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address', $center->address ?? '') }}"
                           placeholder="{{ __('messages.center_address_placeholder') }}">
                </div>
                <small class="form-text text-muted">{{ __('messages.center_address_hint') }}</small>
                @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-share-nodes"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.center_section_social') }}</h2>
                <p class="form-section-hint">{{ __('messages.center_section_social_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="instagram_url">Instagram</label>
                <div class="input-icon-wrap">
                    <i class="fab fa-instagram"></i>
                    <input type="url" name="instagram_url" id="instagram_url"
                           class="form-control @error('instagram_url') is-invalid @enderror"
                           value="{{ old('instagram_url', $center->instagram_url ?? '') }}"
                           placeholder="https://instagram.com/centro">
                </div>
                @error('instagram_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="youtube_url">YouTube</label>
                <div class="input-icon-wrap">
                    <i class="fab fa-youtube"></i>
                    <input type="url" name="youtube_url" id="youtube_url"
                           class="form-control @error('youtube_url') is-invalid @enderror"
                           value="{{ old('youtube_url', $center->youtube_url ?? '') }}"
                           placeholder="https://youtube.com/@centro">
                </div>
                @error('youtube_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="facebook_url">Facebook</label>
                <div class="input-icon-wrap">
                    <i class="fab fa-facebook-f"></i>
                    <input type="url" name="facebook_url" id="facebook_url"
                           class="form-control @error('facebook_url') is-invalid @enderror"
                           value="{{ old('facebook_url', $center->facebook_url ?? '') }}"
                           placeholder="https://facebook.com/centro">
                </div>
                @error('facebook_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label" for="x_url">X</label>
                <div class="input-icon-wrap">
                    <i class="fab fa-x-twitter"></i>
                    <input type="url" name="x_url" id="x_url"
                           class="form-control @error('x_url') is-invalid @enderror"
                           value="{{ old('x_url', $center->x_url ?? '') }}"
                           placeholder="https://x.com/centro">
                </div>
                @error('x_url')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-address-book"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.center_section_contact') }}</h2>
                <p class="form-section-hint">{{ __('messages.center_section_contact_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label" for="contact_name">{{ __('messages.contact_name') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-user-tie"></i>
                    <input type="text" name="contact_name" id="contact_name"
                           class="form-control @error('contact_name') is-invalid @enderror"
                           value="{{ old('contact_name', $center->contact_name ?? '') }}"
                           placeholder="{{ __('messages.center_contact_placeholder') }}">
                </div>
                <small class="form-text text-muted">{{ __('messages.center_contact_hint') }}</small>
                @error('contact_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="phone">{{ __('messages.phone') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="phone" id="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $center->phone ?? '') }}"
                           placeholder="{{ __('messages.phone_placeholder') }}"
                           data-mask="phone"
                           inputmode="tel"
                           autocomplete="tel">
                </div>
                <small class="form-text text-muted">{{ __('messages.phone_hint') }}</small>
                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label" for="email">{{ __('messages.email') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $center->email ?? '') }}"
                           placeholder="{{ __('messages.center_email_placeholder') }}"
                           autocomplete="email">
                </div>
                <small class="form-text text-muted">{{ __('messages.center_email_hint') }}</small>
                @error('email')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </section>

    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-sticky-note"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.center_section_notes') }}</h2>
                <p class="form-section-hint">{{ __('messages.center_section_notes_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-12">
                <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
                <textarea name="notes" id="notes" rows="3"
                          class="form-control @error('notes') is-invalid @enderror"
                          placeholder="{{ __('messages.center_notes_placeholder') }}">{{ old('notes', $center->notes ?? '') }}</textarea>
                <small class="form-text text-muted">{{ __('messages.center_notes_hint') }}</small>
                @error('notes')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <div class="form-switch-card">
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               @checked(old('is_active', $center->is_active ?? true))>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('messages.active') }}</label>
                    </div>
                    <small class="text-muted">{{ __('messages.center_active_hint') }}</small>
                </div>
            </div>
        </div>
    </section>
</div>
