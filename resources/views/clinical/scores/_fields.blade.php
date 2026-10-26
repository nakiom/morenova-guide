@php
    $questionnaire = $questionnaire ?? $selected;
    $responses = old('responses', $responses ?? []);
    $showNotes = $showNotes ?? true;
    $showPatientMessage = $showPatientMessage ?? true;
    $score = $score ?? null;
@endphp

@if($questionnaire && count($questionnaire->fields()) > 0)
<div class="form-section mt-4">
    <div class="form-section-head">
        <span class="form-section-icon"><i class="fas fa-clipboard-list"></i></span>
        <div>
            <h2 class="form-section-title">{{ __('messages.questionnaire_form') }}</h2>
            @if(!empty($formHint))
            <p class="form-section-hint">{{ $formHint }}</p>
            @else
            <p class="form-section-hint">{{ __('messages.questionnaire_form_hint') }}</p>
            @endif
        </div>
    </div>

    <div class="row g-3">
        @foreach($questionnaire->fields() as $field)
        @php
            $key = $field['key'];
            $type = $field['type'] ?? 'text';
            $value = $responses[$key] ?? '';
            $label = $questionnaire->fieldLabel($field);
            $required = ($field['required'] ?? false) === true;
        @endphp
        <div class="col-12 @if(in_array($type, ['number','scale','select'], true)) col-md-6 @endif">
            <label class="form-label" for="response_{{ $key }}">
                {{ $label }}
                @if($required)<span class="text-danger">*</span>@endif
            </label>

            @if($type === 'select')
                <select name="responses[{{ $key }}]" id="response_{{ $key }}" class="form-select" @required($required)>
                    <option value="">—</option>
                    @foreach(($field['options'] ?? []) as $option)
                    <option value="{{ $option['value'] }}" @selected((string) $value === (string) $option['value'])>
                        {{ $questionnaire->optionLabel($option) }}
                    </option>
                    @endforeach
                </select>
            @elseif($type === 'textarea')
                <textarea name="responses[{{ $key }}]" id="response_{{ $key }}" rows="3" class="form-control" @required($required)>{{ $value }}</textarea>
            @elseif($type === 'scale')
                <div class="d-flex align-items-center gap-3">
                    <input type="range"
                           name="responses[{{ $key }}]"
                           id="response_{{ $key }}"
                           class="form-range flex-grow-1"
                           min="{{ $field['min'] ?? 0 }}"
                           max="{{ $field['max'] ?? 10 }}"
                           step="{{ $field['step'] ?? 1 }}"
                           value="{{ $value !== '' ? $value : ($field['min'] ?? 0) }}"
                           oninput="document.getElementById('response_{{ $key }}_val').textContent = this.value"
                           @required($required)>
                    <span id="response_{{ $key }}_val" class="badge bg-primary" style="min-width:2.5rem">{{ $value !== '' ? $value : ($field['min'] ?? 0) }}</span>
                </div>
                <small class="text-muted">{{ $field['min'] ?? 0 }} – {{ $field['max'] ?? 10 }}</small>
            @elseif($type === 'text')
                <input type="text"
                       name="responses[{{ $key }}]"
                       id="response_{{ $key }}"
                       class="form-control"
                       value="{{ $value }}"
                       @required($required)>
            @else
                <input type="number"
                       name="responses[{{ $key }}]"
                       id="response_{{ $key }}"
                       class="form-control"
                       step="{{ $field['step'] ?? 'any' }}"
                       @if(isset($field['min'])) min="{{ $field['min'] }}" @endif
                       @if(isset($field['max'])) max="{{ $field['max'] }}" @endif
                       value="{{ $value }}"
                       @required($required)>
            @endif
            @error('responses.'.$key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        </div>
        @endforeach
    </div>
</div>
@else
<div class="row g-3 mt-2">
    <div class="col-md-6">
        <label class="form-label" for="score">{{ __('messages.score') }} <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="score" id="score" class="form-control"
               value="{{ old('score', $score?->score) }}" required>
    </div>
</div>
@endif

@if($showNotes)
<div class="row mt-3">
    <div class="col-12">
        <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="{{ __('messages.questionnaire_notes_placeholder') }}">{{ old('notes', $score?->notes) }}</textarea>
    </div>
</div>
@endif

@if($showPatientMessage)
<div class="row mt-3">
    <div class="col-12">
        <label class="form-label" for="patient_message">{{ __('messages.questionnaire_patient_message') }}</label>
        <textarea name="patient_message" id="patient_message" rows="3"
                  class="form-control @error('patient_message') is-invalid @enderror"
                  placeholder="{{ __('messages.questionnaire_patient_message_placeholder') }}">{{ old('patient_message', $score?->patient_message) }}</textarea>
        <small class="form-text text-muted">{{ __('messages.questionnaire_patient_message_hint') }}</small>
        @error('patient_message')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>
</div>
@endif
