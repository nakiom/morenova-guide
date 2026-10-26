{{ __('messages.visit_reminder_mail_greeting') }}{{ $patientName ? ', '.$patientName : '' }}

@if($centerName && !$hasCenterLogo)
{{ __('messages.center') }}: {{ $centerName }}
@endif
{{ __('messages.visit_reminder_mail_doctor_line', ['doctor' => $doctorName]) }}

@if($hasCenterLogo && $centerName)
{{ __('messages.visit_reminder_mail_intro_with_center', ['center' => $centerName, 'doctor' => $doctorName]) }}
@else
{{ __('messages.visit_reminder_mail_intro', ['doctor' => $doctorName]) }}
@endif

{{ __('messages.expected_next_visit') }}: {{ $visitDate?->format('d/m/Y') ?? '—' }}

{{ __('messages.visit_reminder_mail_footer') }}
