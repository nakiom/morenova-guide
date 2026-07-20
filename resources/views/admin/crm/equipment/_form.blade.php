<div class="form-stack">
    <section class="form-section">
        <div class="form-section-head">
            <span class="form-section-icon"><i class="fas fa-hdd"></i></span>
            <div>
                <h2 class="form-section-title">{{ __('messages.equipment_section_device') }}</h2>
                <p class="form-section-hint">{{ __('messages.equipment_section_device_hint') }}</p>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="serial_number">{{ __('messages.serial_number') }} <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-barcode"></i>
                    <input type="text" name="serial_number" id="serial_number" class="form-control"
                           value="{{ old('serial_number', $equipment->serial_number ?? '') }}"
                           placeholder="{{ __('messages.serial_placeholder') }}"
                           data-mask="serial" required>
                </div>
                <small class="form-text text-muted">{{ __('messages.serial_hint') }}</small>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="model">{{ __('messages.equipment_model') }} <span class="text-danger">*</span></label>
                <div class="input-icon-wrap">
                    <i class="fas fa-cube"></i>
                    <input type="text" name="model" id="model" class="form-control"
                           value="{{ old('model', $equipment->model ?? '') }}"
                           placeholder="{{ __('messages.model_placeholder') }}" required>
                </div>
                <small class="form-text text-muted">{{ __('messages.model_hint') }}</small>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="center_id">{{ __('messages.center') }} <span class="text-danger">*</span></label>
                <select name="center_id" id="center_id" class="form-select" required>
                    <option value="">{{ __('messages.select_center') }}</option>
                    @foreach($centers as $center)
                    <option value="{{ $center->id }}" @selected(old('center_id', $equipment->center_id ?? $centerId ?? null) == $center->id)>
                        {{ $center->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="user_id">{{ __('messages.assigned_user') }}</label>
                <select name="user_id" id="user_id" class="form-select">
                    <option value="">—</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $equipment->user_id ?? null) == $user->id)>
                        {{ $user->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="location">{{ __('messages.location') }}</label>
                <div class="input-icon-wrap">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" id="location" class="form-control"
                           value="{{ old('location', $equipment->location ?? '') }}"
                           placeholder="{{ __('messages.equipment_location_placeholder') }}">
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <div class="form-switch-card w-100">
                    <div class="form-check form-switch mb-0">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               @checked(old('is_active', $equipment->is_active ?? true))>
                        <label class="form-check-label fw-semibold" for="is_active">{{ __('messages.active') }}</label>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
                <textarea name="notes" id="notes" rows="3" class="form-control"
                          placeholder="{{ __('messages.equipment_notes_placeholder') }}">{{ old('notes', $equipment->notes ?? '') }}</textarea>
            </div>
        </div>
    </section>
</div>
