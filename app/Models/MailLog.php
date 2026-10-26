<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailLog extends Model
{
    public const STATUS_SENT = 'sent';
    public const STATUS_FAILED = 'failed';

    public const TYPE_QUESTIONNAIRE_INVITE = 'questionnaire_invite';
    public const TYPE_VISIT_REMINDER = 'visit_reminder';
    public const TYPE_TEST = 'test';

    protected $fillable = [
        'mailable',
        'type',
        'to_email',
        'subject',
        'status',
        'mailer',
        'user_id',
        'patient_id',
        'questionnaire_invite_id',
        'error',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function invite(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireInvite::class, 'questionnaire_invite_id');
    }

    public function wasSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_QUESTIONNAIRE_INVITE => __('messages.mail_type_questionnaire_invite'),
            self::TYPE_VISIT_REMINDER => __('messages.mail_type_visit_reminder'),
            self::TYPE_TEST => __('messages.mail_type_test'),
            default => $this->type,
        };
    }
}
