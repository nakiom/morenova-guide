<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\QuestionnaireInvite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuestionnaireInviteMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public QuestionnaireInvite $invite)
    {
        $this->invite->loadMissing(['items.questionnaire', 'patient', 'user.center']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('messages.questionnaire_invite_mail_subject'),
        );
    }

    public function content(): Content
    {
        $center = $this->invite->center();
        $centerLogoPath = $center?->logoAbsolutePath();
        $companyLogoPath = public_path('img/morenova-string.png');
        $doctorName = trim((string) ($this->invite->user?->name ?? ''));
        $patientName = trim((string) ($this->invite->patient?->name ?? ''));

        return new Content(
            html: 'emails.questionnaire-invite',
            text: 'emails.questionnaire-invite-text',
            with: [
                'invite' => $this->invite,
                'url' => $this->invite->publicUrl(),
                'questionnaires' => $this->invite->items->pluck('questionnaire.name')->filter()->values(),
                'expiresAt' => $this->invite->expires_at,
                'phaseLabel' => __('messages.phase_'.$this->invite->phase),
                'centerName' => $center?->name,
                'isCenterLogo' => $centerLogoPath !== null,
                'brandLogoPath' => $centerLogoPath ?? (is_file($companyLogoPath) ? $companyLogoPath : null),
                'brandLogoAlt' => $centerLogoPath !== null
                    ? ($center?->name ?: 'Centro')
                    : 'MoreNova',
                'doctorName' => $doctorName !== '' ? $doctorName : __('messages.doctor'),
                'patientName' => $patientName !== '' ? $patientName : null,
                'socialLinks' => $center?->socialLinks() ?? [],
            ],
        );
    }
}
