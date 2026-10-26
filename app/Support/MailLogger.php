<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\MailLog;
use App\Models\QuestionnaireInvite;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class MailLogger
{
    /**
     * @param  array{type?:string,user_id?:?int,patient_id?:?int,questionnaire_invite_id?:?int}  $context
     */
    public static function send(string $to, Mailable $mailable, array $context = []): MailLog
    {
        $type = $context['type'] ?? MailLog::TYPE_TEST;
        $subject = method_exists($mailable, 'envelope')
            ? (string) ($mailable->envelope()->subject ?? class_basename($mailable))
            : class_basename($mailable);

        try {
            Mail::to($to)->send($mailable);

            $log = MailLog::create([
                'mailable' => $mailable::class,
                'type' => $type,
                'to_email' => $to,
                'subject' => $subject,
                'status' => MailLog::STATUS_SENT,
                'mailer' => (string) config('mail.default'),
                'user_id' => $context['user_id'] ?? null,
                'patient_id' => $context['patient_id'] ?? null,
                'questionnaire_invite_id' => $context['questionnaire_invite_id'] ?? null,
                'sent_at' => now(),
            ]);

            if (! empty($context['questionnaire_invite_id'])) {
                $invite = QuestionnaireInvite::query()->find($context['questionnaire_invite_id']);
                if ($invite) {
                    $invite->increment('sent_count');
                    $invite->update(['last_sent_at' => now()]);
                }
            }

            return $log;
        } catch (Throwable $e) {
            return MailLog::create([
                'mailable' => $mailable::class,
                'type' => $type,
                'to_email' => $to,
                'subject' => $subject,
                'status' => MailLog::STATUS_FAILED,
                'mailer' => (string) config('mail.default'),
                'user_id' => $context['user_id'] ?? null,
                'patient_id' => $context['patient_id'] ?? null,
                'questionnaire_invite_id' => $context['questionnaire_invite_id'] ?? null,
                'error' => $e->getMessage(),
                'sent_at' => now(),
            ]);
        }
    }
}
