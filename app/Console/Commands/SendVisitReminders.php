<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\VisitReminderMail;
use App\Models\MailLog;
use App\Models\Patient;
use App\Support\MailLogger;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;

class SendVisitReminders extends Command
{
    protected $signature = 'visits:send-reminders';

    protected $description = 'Envía recordatorios por email de citas programadas según la preferencia de cada paciente';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $sent = 0;
        $skipped = 0;
        $failed = 0;

        $patients = Patient::query()
            ->with(['user.center', 'pathology.translations'])
            ->where('visit_reminder_enabled', true)
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->whereNotNull('expected_next_visit_at')
            ->whereNotNull('visit_reminder_days_before')
            ->whereDate('expected_next_visit_at', '>=', $today)
            ->get();

        foreach ($patients as $patient) {
            if (! $this->shouldSendToday($patient, $today)) {
                $skipped++;

                continue;
            }

            if ($this->alreadySentForVisit($patient)) {
                $skipped++;

                continue;
            }

            $locale = $patient->user?->preferred_locale ?: config('app.locale', 'es');
            App::setLocale($locale);

            $log = MailLogger::send(
                $patient->email,
                new VisitReminderMail($patient),
                [
                    'type' => MailLog::TYPE_VISIT_REMINDER,
                    'user_id' => $patient->user_id,
                    'patient_id' => $patient->id,
                ],
            );

            if ($log->wasSent()) {
                $patient->update([
                    'visit_reminder_sent_for' => $patient->expected_next_visit_at,
                ]);
                $sent++;
            } else {
                $failed++;
            }
        }

        $this->info("Recordatorios enviados: {$sent}. Omitidos: {$skipped}. Fallidos: {$failed}.");

        return self::SUCCESS;
    }

    private function shouldSendToday(Patient $patient, Carbon $today): bool
    {
        $visitDate = $patient->expected_next_visit_at?->copy()->startOfDay();
        $daysBefore = (int) $patient->visit_reminder_days_before;

        if (! $visitDate || $daysBefore < 1) {
            return false;
        }

        $reminderDate = $visitDate->copy()->subDays($daysBefore);

        return $today->equalTo($reminderDate);
    }

    private function alreadySentForVisit(Patient $patient): bool
    {
        if ($patient->visit_reminder_sent_for === null) {
            return false;
        }

        return $patient->visit_reminder_sent_for->toDateString()
            === $patient->expected_next_visit_at?->toDateString();
    }
}
