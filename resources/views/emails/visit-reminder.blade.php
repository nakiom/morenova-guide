<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ __('messages.visit_reminder_mail_subject', ['date' => $visitDate?->format('d/m/Y') ?? '']) }}</title>
</head>
<body style="margin:0;padding:0;background-color:#F9FBFF;">
    @php
        $logoSrc = null;
        if ($hasCenterLogo) {
            if (! empty($isPreview) && ! empty($brandLogoUrl)) {
                $logoSrc = $brandLogoUrl;
            } elseif (! empty($brandLogoPath) && isset($message)) {
                $logoSrc = $message->embed($brandLogoPath);
            } elseif (! empty($brandLogoUrl)) {
                $logoSrc = $brandLogoUrl;
            }
        }
    @endphp
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F9FBFF;">
        <tr>
            <td align="center" style="padding:28px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="width:560px;max-width:100%;background-color:#ffffff;border-radius:16px;border:1px solid rgba(18,76,120,0.08);">
                    <tr>
                        <td style="height:6px;background-color:#124C78;">&nbsp;</td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:28px 32px 8px;font-family:Arial,sans-serif;">
                            @if($logoSrc)
                                <img src="{{ $logoSrc }}" alt="{{ $brandLogoAlt }}" width="180"
                                     style="display:block;margin:0 auto;max-width:180px;max-height:72px;width:auto;height:auto;border:0;" />
                                @if($centerName)
                                    <p style="margin:12px 0 0;font-size:13px;color:#7A7A7A;">{{ $centerName }}</p>
                                @endif
                            @else
                                @if($centerName)
                                    <p style="margin:0 0 6px;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#52E0D7;">
                                        {{ __('messages.center') }}
                                    </p>
                                    <h2 style="margin:0 0 10px;font-size:22px;font-weight:700;color:#124C78;line-height:1.3;">
                                        {{ $centerName }}
                                    </h2>
                                @endif
                                <p style="margin:0;font-size:15px;color:#54595F;">
                                    {{ __('messages.visit_reminder_mail_doctor_line', ['doctor' => $doctorName]) }}
                                </p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px;font-family:Arial,sans-serif;color:#124C78;">
                            <h1 style="margin:0 0 12px;font-size:22px;font-weight:700;">
                                {{ __('messages.visit_reminder_mail_greeting') }}{{ $patientName ? ', '.$patientName : '' }}
                            </h1>
                            <p style="margin:0 0 16px;font-size:16px;line-height:1.55;color:#54595F;">
                                @if($hasCenterLogo && $centerName)
                                    {{ __('messages.visit_reminder_mail_intro_with_center', ['center' => $centerName, 'doctor' => $doctorName]) }}
                                @else
                                    {{ __('messages.visit_reminder_mail_intro', ['doctor' => $doctorName]) }}
                                @endif
                            </p>
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="width:100%;background:#eef5fa;border-radius:12px;border:1px solid rgba(82,224,215,0.35);">
                                <tr>
                                    <td style="padding:16px 18px;">
                                        <p style="margin:0 0 4px;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:#52E0D7;">
                                            {{ __('messages.expected_next_visit') }}
                                        </p>
                                        <p style="margin:0;font-size:20px;font-weight:700;color:#124C78;">
                                            {{ $visitDate?->format('d/m/Y') ?? '—' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 28px;font-family:Arial,sans-serif;font-size:14px;line-height:1.55;color:#54595F;">
                            {{ __('messages.visit_reminder_mail_footer') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
