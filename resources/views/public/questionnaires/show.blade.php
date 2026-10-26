<x-public-questionnaire-layout
    :title="$questionnaire->name"
    :center-logo-url="$centerLogoUrl ?? null"
    :center-name="$centerName ?? null"
>
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <p class="text-muted small mb-1">{{ __('messages.questionnaire_public_progress', ['current' => $current, 'total' => $total]) }}</p>
    <h1 class="h3 fw-bold text-dark-blue mb-1">{{ $questionnaire->name }}</h1>
    <p class="text-muted mb-4">
        {{ __('messages.phase') }}: {{ __('messages.phase_'.$invite->phase) }}
        @if($questionnaire->description)
            · {{ $questionnaire->description }}
        @endif
    </p>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('public.questionnaires.store', $invite->token) }}">
        @csrf
        @include('clinical.scores._fields', [
            'questionnaire' => $questionnaire,
            'responses' => old('responses', []),
            'score' => null,
            'showNotes' => false,
            'showPatientMessage' => true,
            'formHint' => __('messages.questionnaire_public_form_hint'),
        ])

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                @if($current < $total)
                    {{ __('messages.questionnaire_public_continue') }}
                @else
                    {{ __('messages.questionnaire_public_finish') }}
                @endif
            </button>
        </div>
    </form>
</x-public-questionnaire-layout>
