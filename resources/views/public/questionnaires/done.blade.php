<x-public-questionnaire-layout
    :title="__('messages.questionnaire_public_thanks_title')"
    :center-logo-url="$centerLogoUrl ?? null"
    :center-name="$centerName ?? null"
>
    <div class="text-center">
        <div class="mb-3 text-success" style="font-size:2.5rem">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="h3 fw-bold text-dark-blue mb-2">{{ __('messages.questionnaire_public_thanks_title') }}</h1>
        <p class="text-muted mb-4">{{ __('messages.questionnaire_public_thanks_body') }}</p>

        @if($nextVisit)
        <div class="alert alert-primary border-0 text-start">
            <div class="fw-semibold mb-1">
                <i class="fas fa-calendar-alt me-1"></i>{{ __('messages.questionnaire_public_next_visit_title') }}
            </div>
            <div class="fs-5">{{ $nextVisit->format('d/m/Y') }}</div>
            <div class="small mt-1 mb-0">{{ __('messages.questionnaire_public_next_visit_hint') }}</div>
        </div>
        @else
        <p class="text-muted small mb-0">{{ __('messages.questionnaire_public_no_next_visit') }}</p>
        @endif
    </div>
</x-public-questionnaire-layout>
