<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VisitReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Patient $patient,
        public bool $preview = false,
    ) {
        $this->patient->loadMissing(['user.center', 'pathology.translations']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('messages.visit_reminder_mail_subject', [
                'date' => $this->patient->expected_next_visit_at?->format('d/m/Y') ?? '',
            ]),
        );
    }

    public function content(): Content
    {
        $center = $this->patient->user?->center;
        $centerLogoPath = $center?->logoAbsolutePath();
        $hasCenterLogo = is_string($centerLogoPath) && $centerLogoPath !== '' && is_file($centerLogoPath);
        $doctorName = trim((string) ($this->patient->user?->name ?? ''));
        $patientName = trim((string) ($this->patient->name ?? ''));
        $centerName = trim((string) ($center?->name ?? ''));

        return new Content(
            html: 'emails.visit-reminder',
            text: 'emails.visit-reminder-text',
            with: [
                'patient' => $this->patient,
                'visitDate' => $this->patient->expected_next_visit_at,
                'centerName' => $centerName !== '' ? $centerName : null,
                'hasCenterLogo' => $hasCenterLogo,
                'brandLogoPath' => $hasCenterLogo ? $centerLogoPath : null,
                'brandLogoUrl' => $hasCenterLogo ? $center?->logoUrl() : null,
                'brandLogoAlt' => $centerName !== '' ? $centerName : __('messages.center'),
                'isPreview' => $this->preview,
                'showTextBranding' => ! $hasCenterLogo,
                'doctorName' => $doctorName !== '' ? $doctorName : __('messages.doctor'),
                'patientName' => $patientName !== '' ? $patientName : null,
            ],
        );
    }
}
