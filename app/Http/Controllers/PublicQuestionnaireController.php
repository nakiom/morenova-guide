<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\QuestionnaireInvite;
use App\Models\QuestionnaireScore;
use App\Support\QuestionnaireResponseValidator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PublicQuestionnaireController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $invite = $this->findInvite($token);

        if ($gone = $this->unavailableIfPatientGone($invite)) {
            return $gone;
        }

        $invite->markVisited();

        if ($invite->isCompleted()) {
            return redirect()->route('public.questionnaires.done', $token);
        }

        if ($invite->isExpired()) {
            return view('public.questionnaires.unavailable', [
                'reason' => 'expired',
                ...$this->centerBrand($invite),
            ]);
        }

        $item = $invite->nextPendingItem();
        if (! $item) {
            $invite->markCompletedIfDone();

            return redirect()->route('public.questionnaires.done', $token);
        }

        $item->load('questionnaire');
        $invite->load('items.questionnaire');

        return view('public.questionnaires.show', [
            'invite' => $invite,
            'item' => $item,
            'questionnaire' => $item->questionnaire,
            'total' => $invite->items->count(),
            'current' => $invite->completedCount() + 1,
            ...$this->centerBrand($invite),
        ]);
    }

    public function store(Request $request, string $token): View|RedirectResponse
    {
        $invite = $this->findInvite($token);

        if ($gone = $this->unavailableIfPatientGone($invite)) {
            return $gone;
        }

        if ($invite->isCompleted() || $invite->isExpired()) {
            return redirect()->route('public.questionnaires.show', $token);
        }

        $item = $invite->nextPendingItem();
        abort_unless($item !== null, 404);

        $questionnaire = $item->questionnaire;
        abort_unless($questionnaire !== null, 404);

        $validated = QuestionnaireResponseValidator::validate($request, $questionnaire, false, true);

        DB::transaction(function () use ($invite, $item, $questionnaire, $validated) {
            $score = QuestionnaireScore::create([
                'patient_id' => $invite->patient_id,
                'questionnaire_id' => $questionnaire->id,
                'user_id' => $invite->user_id,
                'phase' => $invite->phase,
                'score' => $validated['score'],
                'responses' => $validated['responses'],
                'scored_at' => now()->toDateString(),
                'notes' => __('messages.questionnaire_completed_via_email'),
                'patient_message' => $validated['patient_message'],
            ]);

            $item->update([
                'questionnaire_score_id' => $score->id,
                'completed_at' => now(),
            ]);

            $invite->markCompletedIfDone();
        });

        if ($invite->fresh()->isCompleted()) {
            return redirect()->route('public.questionnaires.done', $token);
        }

        return redirect()
            ->route('public.questionnaires.show', $token)
            ->with('success', __('messages.questionnaire_public_saved'));
    }

    public function done(string $token): View
    {
        $invite = $this->findInvite($token);

        if ($gone = $this->unavailableIfPatientGone($invite)) {
            return $gone;
        }

        $invite->markVisited();
        $invite->load('patient');
        $brand = $this->centerBrand($invite);

        if (! $invite->isCompleted() && ! $invite->isExpired()) {
            $pending = $invite->nextPendingItem();
            if ($pending) {
                return view('public.questionnaires.unavailable', [
                    'reason' => 'incomplete',
                    'token' => $token,
                    ...$brand,
                ]);
            }
            $invite->markCompletedIfDone();
        }

        if ($invite->isExpired() && ! $invite->isCompleted()) {
            return view('public.questionnaires.unavailable', [
                'reason' => 'expired',
                ...$brand,
            ]);
        }

        return view('public.questionnaires.done', [
            'invite' => $invite,
            'nextVisit' => $invite->patient?->expected_next_visit_at,
            ...$brand,
        ]);
    }

    private function unavailableIfPatientGone(QuestionnaireInvite $invite): ?View
    {
        if ($invite->patient !== null) {
            return null;
        }

        return view('public.questionnaires.unavailable', [
            'reason' => 'unavailable',
            ...$this->centerBrand($invite),
        ]);
    }

    private function findInvite(string $token): QuestionnaireInvite
    {
        return QuestionnaireInvite::query()
            ->where('token', $token)
            ->with(['items.questionnaire', 'patient', 'user.center'])
            ->firstOrFail();
    }

    /**
     * @return array{centerLogoUrl:?string,centerName:?string}
     */
    private function centerBrand(QuestionnaireInvite $invite): array
    {
        $center = $invite->center();

        return [
            'centerLogoUrl' => $center?->logoUrl(),
            'centerName' => $center?->name,
        ];
    }
}
