@php
    $score = $score ?? null;
    $selected = $selected ?? $questionnaires->first();
    $responses = old('responses', $score?->responses ?? []);
    $reloadOnChange = $reloadOnChange ?? ($score === null);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="questionnaire_id">{{ __('messages.questionnaire') }} <span class="text-danger">*</span></label>
        <select name="questionnaire_id" id="questionnaire_id" class="form-select" required
                @if($reloadOnChange)
                onchange="window.location.href='{{ route('clinical.scores.create', $patient) }}?questionnaire_id=' + this.value"
                @endif>
            @foreach($questionnaires as $questionnaire)
            <option value="{{ $questionnaire->id }}" @selected(($selected?->id ?? null) == $questionnaire->id)>
                {{ $questionnaire->name }}@if($questionnaire->max_score) (max {{ rtrim(rtrim(number_format((float) $questionnaire->max_score, 2, '.', ''), '0'), '.') }})@endif
            </option>
            @endforeach
        </select>
        @if($selected?->description)
        <small class="form-text text-muted">{{ $selected->description }}</small>
        @endif
    </div>
    <div class="col-md-3">
        <label class="form-label" for="phase">{{ __('messages.phase') }} <span class="text-danger">*</span></label>
        <select name="phase" id="phase" class="form-select" required>
            @foreach($phases as $phase)
            <option value="{{ $phase }}" @selected(old('phase', $score?->phase) === $phase)>{{ __('messages.phase_'.$phase) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label" for="scored_at">{{ __('messages.scored_at') }} <span class="text-danger">*</span></label>
        <input type="date" name="scored_at" id="scored_at" class="form-control"
               value="{{ old('scored_at', $score?->scored_at?->toDateString() ?? now()->toDateString()) }}" required>
    </div>
</div>

@if($selected && count($selected->fields()) > 0)
<div class="form-section mt-4">
    <div class="form-section-head">
        <span class="form-section-icon"><i class="fas fa-clipboard-list"></i></span>
        <div>
            <h2 class="form-section-title">{{ __('messages.questionnaire_form') }}</h2>
            <p class="form-section-hint">{{ __('messages.questionnaire_form_hint') }}</p>
        </div>
    </div>

    <div class="row g-3">
        @foreach($selected->fields() as $field)
        @php
            $key = $field['key'];
            $type = $field['type'] ?? 'text';
            $value = $responses[$key] ?? '';
            $label = $selected->fieldLabel($field);
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
                        {{ $selected->optionLabel($option) }}
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

<div class="row mt-3">
    <div class="col-12">
        <label class="form-label" for="notes">{{ __('messages.notes') }}</label>
        <textarea name="notes" id="notes" rows="2" class="form-control" placeholder="{{ __('messages.questionnaire_notes_placeholder') }}">{{ old('notes', $score?->notes) }}</textarea>
    </div>
</div>
