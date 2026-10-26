<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Mail\VisitReminderMail;
use App\Models\MailLog;
use App\Models\Patient;
use App\Support\MailLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class VisitReminderController extends Controller
{
    public function preview(Patient $patient): Response
    {
        $this->authorize('view', $patient);
        abort_unless($patient->expected_next_visit_at !== null, 404);

        $patient->loadMissing(['user.center', 'pathology.translations']);

        $locale = $patient->user?->preferred_locale ?: config('app.locale', 'es');
        App::setLocale($locale);

        $html = (new VisitReminderMail($patient, preview: true))->render();

        return response($html);
    }

    public function send(Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        if (! filled($patient->email)) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.visit_reminder_needs_email'));
        }

        if ($patient->expected_next_visit_at === null) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.visit_reminder_needs_visit_date'));
        }

        $patient->loadMissing(['user.center', 'pathology.translations']);

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

        if (! $log->wasSent()) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.visit_reminder_send_failed', [
                    'error' => $log->error ?: '—',
                ]));
        }

        $patient->update([
            'visit_reminder_sent_for' => $patient->expected_next_visit_at,
        ]);

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.visit_reminder_sent'));
    }
}
