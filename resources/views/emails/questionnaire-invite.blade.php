<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light" />
    <title>{{ __('messages.questionnaire_invite_mail_subject') }}</title>
    <!--[if mso]>
    <style type="text/css">
        body, table, td { font-family: Arial, Helvetica, sans-serif !important; }
    </style>
    <![endif]-->
</head>
<body style="margin:0;padding:0;background-color:#F9FBFF;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;">
    <div style="display:none;font-size:1px;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;mso-hide:all;">
        {{ __('messages.questionnaire_invite_mail_preheader', ['doctor' => $doctorName]) }}
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#F9FBFF;margin:0;padding:0;width:100%;">
        <tr>
            <td align="center" style="padding:28px 16px;">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" border="0" style="width:560px;max-width:100%;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid rgba(18,76,120,0.08);box-shadow:0 10px 30px rgba(18,76,120,0.08);">
                    {{-- Brand bar --}}
                    <tr>
                        <td style="height:6px;line-height:6px;font-size:0;background:linear-gradient(267deg,#124C78 9%,#52E0D7 95%);background-color:#124C78;">
                            &nbsp;
                        </td>
                    </tr>

                    {{-- Logo (embedded CID so it works outside localhost) --}}
                    <tr>
                        <td align="center" style="padding:28px 32px 8px 32px;">
                            @if($brandLogoPath)
                                <img
                                    src="{{ $message->embed($brandLogoPath) }}"
                                    alt="{{ $brandLogoAlt }}"
                                    width="{{ $isCenterLogo ? 180 : 200 }}"
                                    style="display:block;margin:0 auto;max-width:{{ $isCenterLogo ? '180px' : '200px' }};max-height:{{ $isCenterLogo ? '72px' : '48px' }};width:auto;height:auto;border:0;outline:none;text-decoration:none;"
                                />
                            @endif
                            @if($centerName)
                                <p style="margin:12px 0 0 0;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;font-size:13px;line-height:1.4;color:#7A7A7A;letter-spacing:0.02em;">
                                    {{ $centerName }}
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding:20px 32px 0 32px;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;color:#124C78;">
                            <h1 style="margin:0 0 12px 0;font-size:22px;line-height:1.3;font-weight:700;color:#124C78;">
                                {{ __('messages.questionnaire_invite_mail_greeting') }}{{ $patientName ? ', '.$patientName : '' }}
                            </h1>
                            <p style="margin:0;font-size:16px;line-height:1.55;color:#54595F;">
                                {{ __('messages.questionnaire_invite_mail_intro', ['doctor' => $doctorName]) }}
                            </p>
                        </td>
                    </tr>

                    {{-- MoreNova treatment info --}}
                    <tr>
                        <td style="padding:24px 32px 0 32px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;background-color:#eef5fa;border-radius:12px;border:1px solid rgba(82,224,215,0.35);">
                                <tr>
                                    <td style="padding:18px 20px;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">
                                        <p style="margin:0 0 6px 0;font-size:12px;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#52E0D7;">
                                            {{ __('messages.questionnaire_invite_mail_treatment_eyebrow') }}
                                        </p>
                                        <p style="margin:0 0 8px 0;font-size:16px;font-weight:700;line-height:1.35;color:#124C78;">
                                            {{ __('messages.questionnaire_invite_mail_treatment_title') }}
                                        </p>
                                        <p style="margin:0;font-size:14px;line-height:1.55;color:#54595F;">
                                            {{ __('messages.questionnaire_invite_mail_treatment_body') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Questionnaires --}}
                    <tr>
                        <td style="padding:24px 32px 0 32px;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">
                            <p style="margin:0 0 10px 0;font-size:13px;font-weight:600;color:#124C78;">
                                {{ __('messages.phase') }}: <span style="color:#54595F;font-weight:500;">{{ $phaseLabel }}</span>
                            </p>
                            <p style="margin:0 0 8px 0;font-size:13px;font-weight:600;color:#124C78;">
                                {{ __('messages.questionnaire_invite_mail_list_title') }}
                            </p>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;">
                                @foreach($questionnaires as $name)
                                <tr>
                                    <td style="padding:6px 0;font-size:15px;line-height:1.4;color:#54595F;">
                                        <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background-color:#52E0D7;margin-right:10px;vertical-align:middle;"></span>
                                        <span style="vertical-align:middle;">{{ $name }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    {{-- CTA --}}
                    <tr>
                        <td align="center" style="padding:28px 32px 8px 32px;">
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td align="center" bgcolor="#124C78" style="border-radius:10px;background-color:#124C78;">
                                        <a href="{{ $url }}" target="_blank" rel="noopener"
                                           style="display:inline-block;padding:14px 28px;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;font-size:15px;font-weight:700;line-height:1.2;color:#ffffff;text-decoration:none;border-radius:10px;">
                                            {{ __('messages.questionnaire_invite_mail_cta') }}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Expiry + footer note --}}
                    <tr>
                        <td style="padding:16px 32px 8px 32px;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">
                            <p style="margin:0 0 12px 0;font-size:13px;line-height:1.5;color:#7A7A7A;text-align:center;">
                                {{ __('messages.questionnaire_invite_mail_expires', ['date' => $expiresAt->timezone(config('app.timezone'))->format('d/m/Y H:i')]) }}
                            </p>
                            <p style="margin:0;font-size:12px;line-height:1.5;color:#A0A4A8;text-align:center;">
                                {{ __('messages.questionnaire_invite_mail_footer') }}
                            </p>
                        </td>
                    </tr>

                    {{-- Center social links --}}
                    @if(count($socialLinks) > 0)
                    <tr>
                        <td align="center" style="padding:20px 32px 28px 32px;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">
                            <p style="margin:0 0 12px 0;font-size:12px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:#124C78;">
                                {{ __('messages.questionnaire_invite_mail_social_title') }}
                            </p>
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0" align="center">
                                <tr>
                                    @foreach($socialLinks as $index => $social)
                                        <td align="center" style="padding:0 {{ $index < count($socialLinks) - 1 ? '6px' : '0' }} 0 {{ $index > 0 ? '6px' : '0' }};">
                                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                               style="display:inline-block;padding:8px 14px;font-size:13px;font-weight:600;line-height:1.2;color:#124C78;text-decoration:none;border:1px solid rgba(18,76,120,0.18);border-radius:999px;background-color:#F9FBFF;">
                                                {{ $social['label'] }}
                                            </a>
                                        </td>
                                    @endforeach
                                </tr>
                            </table>
                        </td>
                    </tr>
                    @else
                    <tr>
                        <td style="padding:0 0 20px 0;font-size:0;line-height:0;">&nbsp;</td>
                    </tr>
                    @endif

                    {{-- Bottom stripe --}}
                    <tr>
                        <td style="background-color:#124C78;padding:16px 24px;text-align:center;font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;">
                            <p style="margin:0;font-size:12px;line-height:1.4;color:rgba(255,255,255,0.85);">
                                MoreNova · Direx
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
