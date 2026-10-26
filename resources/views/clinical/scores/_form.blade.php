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

@include('clinical.scores._fields', [
    'questionnaire' => $selected,
    'responses' => $responses,
    'score' => $score,
    'showNotes' => true,
    'showPatientMessage' => true,
])
