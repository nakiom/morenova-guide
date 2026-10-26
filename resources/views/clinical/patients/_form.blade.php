@php
    $locale = app()->getLocale();
    $phones = old('phones', $patient?->phones ?? []);
    if (! is_array($phones) || $phones === []) {
        $phones = [''];
    }
@endphp
<div class="row">
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="name">{{ __('messages.patient_name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $patient?->name) }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="external_id">{{ __('messages.patient_id') }}</label>
        <input type="text" name="external_id" id="external_id" class="form-control @error('external_id') is-invalid @enderror"
               value="{{ old('external_id', $patient?->external_id) }}">
        @error('external_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="age">{{ __('messages.age') }}</label>
        <input type="number" name="age" id="age" class="form-control @error('age') is-invalid @enderror"
               value="{{ old('age', $patient?->age) }}" min="0" max="150">
        @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="sex">{{ __('messages.sex') }}</label>
        <select name="sex" id="sex" class="form-select @error('sex') is-invalid @enderror">
            <option value="">{{ __('messages.sex_unspecified') }}</option>
            @foreach(\App\Models\Patient::SEXES as $sex)
                <option value="{{ $sex }}" @selected(old('sex', $patient?->sex) === $sex)>
                    {{ __('messages.sex_'.$sex) }}
                </option>
            @endforeach
        </select>
        @error('sex')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="pathology_id">{{ __('messages.protocol') }} <span class="text-danger">*</span></label>
        <select name="pathology_id" id="pathology_id" class="form-select @error('pathology_id') is-invalid @enderror" required>
            <option value="">{{ __('messages.select_protocol') }}</option>
            @foreach($pathologies as $pathology)
            <option value="{{ $pathology->id }}" @selected(old('pathology_id', $patient?->pathology_id) == $pathology->id)>
                {{ $pathology->translation($locale)?->name ?? $pathology->slug }}
            </option>
            @endforeach
        </select>
        @error('pathology_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12"><hr class="my-2"></div>
    <div class="col-12 mb-2">
        <h3 class="h6 fw-bold text-dark-blue mb-0">{{ __('messages.contact_data') }}</h3>
        <p class="text-muted small mb-0">{{ __('messages.contact_data_hint') }}</p>
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="email">{{ __('messages.email') }}</label>
        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $patient?->email) }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label">{{ __('messages.phones') }}</label>
        <div id="phones-list" class="d-flex flex-column gap-2">
            @foreach($phones as $phone)
            <input type="text" name="phones[]" class="form-control" value="{{ $phone }}"
                   placeholder="{{ __('messages.phone_placeholder') }}">
            @endforeach
        </div>
        <button type="button" class="btn btn-link btn-sm px-0 mt-1" id="add-phone-btn">
            <i class="fas fa-plus me-1"></i>{{ __('messages.add_phone') }}
        </button>
        @error('phones')<div class="text-danger small">{{ $message }}</div>@enderror
        @error('phones.*')<div class="text-danger small">{{ $message }}</div>@enderror
    </div>

    <div class="col-12"><hr class="my-2"></div>
    <div class="col-12 col-md-6 mb-3">
        <label class="form-label" for="expected_next_visit_at">{{ __('messages.expected_next_visit') }}</label>
        <input type="date" name="expected_next_visit_at" id="expected_next_visit_at"
               class="form-control @error('expected_next_visit_at') is-invalid @enderror"
               value="{{ old('expected_next_visit_at', $patient?->expected_next_visit_at?->toDateString()) }}">
        <small class="text-muted">{{ __('messages.expected_next_visit_hint') }}</small>
        @error('expected_next_visit_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12 mb-3">
        <div class="form-check form-switch mb-2">
            <input type="hidden" name="visit_reminder_enabled" value="0">
            <input type="checkbox" name="visit_reminder_enabled" value="1" id="visit_reminder_enabled"
                   class="form-check-input @error('visit_reminder_enabled') is-invalid @enderror"
                   @checked(old('visit_reminder_enabled', $patient?->visit_reminder_enabled))>
            <label class="form-check-label fw-semibold" for="visit_reminder_enabled">
                {{ __('messages.visit_reminder_enabled') }}
            </label>
        </div>
        <p class="text-muted small mb-2">{{ __('messages.visit_reminder_enabled_hint') }}</p>
        <div id="visit-reminder-options" class="row g-2 {{ old('visit_reminder_enabled', $patient?->visit_reminder_enabled) ? '' : 'd-none' }}">
            <div class="col-12 col-md-6">
                <label class="form-label" for="visit_reminder_days_before">{{ __('messages.visit_reminder_days_before') }}</label>
                <select name="visit_reminder_days_before" id="visit_reminder_days_before"
                        class="form-select @error('visit_reminder_days_before') is-invalid @enderror">
                    <option value="">{{ __('messages.select_placeholder') }}</option>
                    @foreach(\App\Models\Patient::REMINDER_DAYS_OPTIONS as $days)
                        <option value="{{ $days }}" @selected((int) old('visit_reminder_days_before', $patient?->visit_reminder_days_before) === $days)>
                            {{ trans_choice('messages.visit_reminder_days_option', $days, ['count' => $days]) }}
                        </option>
                    @endforeach
                </select>
                @error('visit_reminder_days_before')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>
    <div class="col-12 mb-3">
        <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $patient?->notes) }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<script>
(function () {
    const btn = document.getElementById('add-phone-btn');
    if (btn && !btn.dataset.bound) {
        btn.dataset.bound = '1';
        btn.addEventListener('click', function () {
            const list = document.getElementById('phones-list');
            if (!list) return;
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'phones[]';
            input.className = 'form-control';
            input.placeholder = @json(__('messages.phone_placeholder'));
            list.appendChild(input);
            input.focus();
        });
    }

    const reminderToggle = document.getElementById('visit_reminder_enabled');
    const reminderOptions = document.getElementById('visit-reminder-options');
    if (reminderToggle && reminderOptions) {
        reminderToggle.addEventListener('change', function () {
            reminderOptions.classList.toggle('d-none', !this.checked);
        });
    }
})();
</script>
