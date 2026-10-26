<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\MailSettingsTestMail;
use App\Models\MailLog;
use App\Models\QuestionnaireInvite;
use App\Support\MailLogger;
use App\Support\MailSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MailSettingsController extends Controller
{
    public function edit(): View
    {
        $settings = MailSettings::current();

        $stats = [
            'sent' => MailLog::query()->where('status', MailLog::STATUS_SENT)->count(),
            'failed' => MailLog::query()->where('status', MailLog::STATUS_FAILED)->count(),
            'invites' => MailLog::query()->where('type', MailLog::TYPE_QUESTIONNAIRE_INVITE)->where('status', MailLog::STATUS_SENT)->count(),
            'reminders' => MailLog::query()->where('type', MailLog::TYPE_VISIT_REMINDER)->where('status', MailLog::STATUS_SENT)->count(),
            'reminders_failed' => MailLog::query()->where('type', MailLog::TYPE_VISIT_REMINDER)->where('status', MailLog::STATUS_FAILED)->count(),
            'visited' => QuestionnaireInvite::query()->whereNotNull('visited_at')->count(),
            'completed' => QuestionnaireInvite::query()->whereNotNull('completed_at')->count(),
            'pending' => QuestionnaireInvite::query()
                ->whereNull('completed_at')
                ->where('expires_at', '>', now())
                ->count(),
        ];

        $recentLogs = MailLog::query()
            ->with(['user:id,name', 'patient:id,name', 'invite:id,token,visited_at,completed_at'])
            ->latest('sent_at')
            ->latest('id')
            ->limit(30)
            ->get();

        $reminderLogs = MailLog::query()
            ->where('type', MailLog::TYPE_VISIT_REMINDER)
            ->with([
                'user:id,name',
                'patient:id,name,expected_next_visit_at,visit_reminder_sent_for',
            ])
            ->latest('sent_at')
            ->latest('id')
            ->limit(50)
            ->get();

        $recentInvites = QuestionnaireInvite::query()
            ->with(['patient:id,name', 'user:id,name'])
            ->latest('id')
            ->limit(20)
            ->get();

        return view('admin.mail.edit', [
            'settings' => $settings,
            'stats' => $stats,
            'recentLogs' => $recentLogs,
            'reminderLogs' => $reminderLogs,
            'recentInvites' => $recentInvites,
            'mailers' => MailSettings::MAILERS,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'mailer' => ['required', Rule::in(MailSettings::MAILERS)],
            'from_address' => 'required|email|max:255',
            'from_name' => 'required|string|max:255',
            'smtp_host' => 'nullable|string|max:255',
            'smtp_port' => 'nullable|string|max:10',
            'smtp_username' => 'nullable|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'smtp_encryption' => 'nullable|in:tls,ssl',
            'mailgun_domain' => 'nullable|string|max:255',
            'mailgun_secret' => 'nullable|string|max:255',
            'mailgun_endpoint' => 'nullable|string|max:255',
        ]);

        if ($data['mailer'] === 'smtp') {
            $request->validate([
                'smtp_host' => 'required|string|max:255',
                'smtp_port' => 'required|string|max:10',
            ]);
        }

        if ($data['mailer'] === 'mailgun') {
            $request->validate([
                'mailgun_domain' => 'required|string|max:255',
                'mailgun_secret' => 'nullable|string|max:255',
            ]);
        }

        MailSettings::save($data);

        try {
            Artisan::call('config:clear');
        } catch (\Throwable) {
            // ignore if config cache isn't available
        }

        return redirect()
            ->route('admin.mail.edit')
            ->with('success', __('messages.mail_settings_saved'));
    }

    public function sendTest(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        MailSettings::apply();

        $log = MailLogger::send(
            $data['test_email'],
            new MailSettingsTestMail(),
            [
                'type' => MailLog::TYPE_TEST,
                'user_id' => $request->user()->id,
            ]
        );

        if ($log->wasSent()) {
            return redirect()
                ->route('admin.mail.edit')
                ->with('success', __('messages.mail_test_sent'));
        }

        return redirect()
            ->route('admin.mail.edit')
            ->with('error', __('messages.mail_test_failed', ['error' => $log->error ?: '—']));
    }
}
