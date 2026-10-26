@php
    $pathologyName = $patient->pathology?->translation(app()->getLocale())?->name ?? $patient->pathology?->slug;
@endphp
<x-app-layout>
    <div class="container py-5">
        <div class="admin-form-shell">
            <div class="admin-form-hero mb-4">
                <p class="admin-form-eyebrow mb-1">{{ __('messages.questionnaire_invite') }}</p>
                <h1 class="h3 fw-bold text-dark-blue mb-1">{{ __('messages.send_questionnaires_by_email') }}</h1>
                <p class="text-muted mb-0">
                    {{ $patient->name }} · {{ $pathologyName }} · {{ $patient->email }}
                </p>
            </div>

            @if($questionnaires->isEmpty())
                <div class="alert alert-warning mb-0">{{ __('messages.no_questionnaires_for_protocol') }}</div>
                <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-outline-secondary mt-3">{{ __('messages.back') }}</a>
            @else
            <form method="POST" action="{{ route('clinical.invites.store', $patient) }}" class="card border-0 shadow-sm">
                @csrf
                <div class="card-body p-4">
                    <p class="text-muted mb-4">{{ __('messages.questionnaire_invite_hint') }}</p>

                    <div class="mb-4">
                        <label class="form-label" for="phase">{{ __('messages.phase') }} <span class="text-danger">*</span></label>
                        <select name="phase" id="phase" class="form-select" required style="max-width:20rem">
                            @foreach($phases as $phase)
                            <option value="{{ $phase }}" @selected(old('phase') === $phase)>{{ __('messages.phase_'.$phase) }}</option>
                            @endforeach
                        </select>
                        @error('phase')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-label">{{ __('messages.questionnaires_to_send') }} <span class="text-danger">*</span></div>
                        <div class="list-group">
                            @foreach($questionnaires as $questionnaire)
                            <label class="list-group-item list-group-item-action d-flex gap-3 align-items-start">
                                <input class="form-check-input flex-shrink-0 mt-1"
                                       type="checkbox"
                                       name="questionnaire_ids[]"
                                       value="{{ $questionnaire->id }}"
                                       @checked(in_array($questionnaire->id, old('questionnaire_ids', []), true))>
                                <span>
                                    <strong>{{ $questionnaire->name }}</strong>
                                    @if($questionnaire->description)
                                    <div class="small text-muted">{{ $questionnaire->description }}</div>
                                    @endif
                                </span>
                            </label>
                            @endforeach
                        </div>
                        @error('questionnaire_ids')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('questionnaire_ids.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-light border mb-0">
                        <i class="fas fa-envelope me-1 text-primary"></i>
                        {{ __('messages.questionnaire_invite_will_send_to', ['email' => $patient->email]) }}
                    </div>
                </div>
                <div class="card-footer bg-white border-0 d-flex flex-wrap gap-2 justify-content-between px-4 pb-4">
                    <a href="{{ route('clinical.patients.show', $patient) }}" class="btn btn-outline-secondary">{{ __('messages.cancel') }}</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-1"></i>{{ __('messages.send_questionnaires_by_email') }}
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>
