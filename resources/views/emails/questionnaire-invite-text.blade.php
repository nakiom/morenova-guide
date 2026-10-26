{{ __('messages.questionnaire_invite_mail_greeting') }}{{ $patientName ? ', '.$patientName : '' }}

{{ __('messages.questionnaire_invite_mail_intro', ['doctor' => $doctorName]) }}

{{ __('messages.questionnaire_invite_mail_treatment_title') }}
{{ __('messages.questionnaire_invite_mail_treatment_body') }}

{{ __('messages.phase') }}: {{ $phaseLabel }}

{{ __('messages.questionnaire_invite_mail_list_title') }}
@foreach($questionnaires as $name)
- {{ $name }}
@endforeach

{{ __('messages.questionnaire_invite_mail_cta') }}:
{{ $url }}

{{ __('messages.questionnaire_invite_mail_expires', ['date' => $expiresAt->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}

{{ __('messages.questionnaire_invite_mail_footer') }}

@if(count($socialLinks) > 0)
{{ __('messages.questionnaire_invite_mail_social_title') }}
@foreach($socialLinks as $social)
{{ $social['label'] }}: {{ $social['url'] }}
@endforeach
@endif

MoreNova · Direx
