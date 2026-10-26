<x-public-questionnaire-layout
    :title="__('messages.questionnaire_public_unavailable_title')"
    :center-logo-url="$centerLogoUrl ?? null"
    :center-name="$centerName ?? null"
>
    <div class="text-center">
        <div class="mb-3 text-muted" style="font-size:2.5rem">
            <i class="fas fa-link-slash"></i>
        </div>
        <h1 class="h4 fw-bold text-dark-blue mb-2">{{ __('messages.questionnaire_public_unavailable_title') }}</h1>

        @if(($reason ?? '') === 'expired')
            <p class="text-muted mb-0">{{ __('messages.questionnaire_invite_expired') }}</p>
        @elseif(($reason ?? '') === 'incomplete')
            <p class="text-muted mb-3">{{ __('messages.questionnaire_public_incomplete') }}</p>
            @if(!empty($token))
            <a href="{{ route('public.questionnaires.show', $token) }}" class="btn btn-primary">
                {{ __('messages.questionnaire_public_continue') }}
            </a>
            @endif
        @else
            <p class="text-muted mb-0">{{ __('messages.questionnaire_public_unavailable_body') }}</p>
        @endif
    </div>
</x-public-questionnaire-layout>
