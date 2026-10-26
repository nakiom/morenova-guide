<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Mail\QuestionnaireInviteMail;
use App\Models\MailLog;
use App\Models\Patient;
use App\Models\Questionnaire;
use App\Models\QuestionnaireInvite;
use App\Models\QuestionnaireInviteItem;
use App\Models\QuestionnaireScore;
use App\Support\MailLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuestionnaireInviteController extends Controller
{
    public function create(Patient $patient): View|RedirectResponse
    {
        $this->authorize('update', $patient);

        if (! $patient->email) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.questionnaire_invite_needs_email'));
        }

        $questionnaires = Questionnaire::active()
            ->where('pathology_id', $patient->pathology_id)
            ->orderBy('name')
            ->get();

        return view('clinical.invites.create', [
            'patient' => $patient,
            'questionnaires' => $questionnaires,
            'phases' => QuestionnaireScore::PHASES,
        ]);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        if (! $patient->email) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.questionnaire_invite_needs_email'));
        }

        $data = $request->validate([
            'phase' => ['required', Rule::in(QuestionnaireScore::PHASES)],
            'questionnaire_ids' => ['required', 'array', 'min:1'],
            'questionnaire_ids.*' => [
                'integer',
                Rule::exists('questionnaires', 'id')->where(function ($q) use ($patient) {
                    $q->where('pathology_id', $patient->pathology_id)->where('is_active', true);
                }),
            ],
        ]);

        $questionnaireIds = array_values(array_unique(array_map('intval', $data['questionnaire_ids'])));

        $invite = DB::transaction(function () use ($patient, $request, $data, $questionnaireIds) {
            $invite = QuestionnaireInvite::create([
                'patient_id' => $patient->id,
                'user_id' => $request->user()->id,
                'token' => QuestionnaireInvite::generateToken(),
                'phase' => $data['phase'],
                'questionnaire_ids' => $questionnaireIds,
                'email' => $patient->email,
                'expires_at' => now()->addDays(QuestionnaireInvite::DEFAULT_EXPIRY_DAYS),
            ]);

            foreach ($questionnaireIds as $index => $questionnaireId) {
                QuestionnaireInviteItem::create([
                    'questionnaire_invite_id' => $invite->id,
                    'questionnaire_id' => $questionnaireId,
                    'order' => $index + 1,
                ]);
            }

            return $invite;
        });

        $log = MailLogger::send(
            $invite->email,
            new QuestionnaireInviteMail($invite),
            [
                'type' => MailLog::TYPE_QUESTIONNAIRE_INVITE,
                'user_id' => $request->user()->id,
                'patient_id' => $patient->id,
                'questionnaire_invite_id' => $invite->id,
            ]
        );

        if (! $log->wasSent()) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.questionnaire_invite_send_failed', ['error' => $log->error ?: '—']));
        }

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.questionnaire_invite_sent'));
    }

    public function resend(Patient $patient, QuestionnaireInvite $invite): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($invite->patient_id === $patient->id, 404);

        if (! $invite->isOpen()) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.questionnaire_invite_cannot_resend'));
        }

        if (! $patient->email) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.questionnaire_invite_needs_email'));
        }

        if ($invite->email !== $patient->email) {
            $invite->update(['email' => $patient->email]);
        }

        $log = MailLogger::send(
            $invite->email,
            new QuestionnaireInviteMail($invite->fresh(['items.questionnaire', 'patient'])),
            [
                'type' => MailLog::TYPE_QUESTIONNAIRE_INVITE,
                'user_id' => request()->user()->id,
                'patient_id' => $patient->id,
                'questionnaire_invite_id' => $invite->id,
            ]
        );

        if (! $log->wasSent()) {
            return redirect()
                ->route('clinical.patients.show', $patient)
                ->with('error', __('messages.questionnaire_invite_send_failed', ['error' => $log->error ?: '—']));
        }

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.questionnaire_invite_resent'));
    }
}
