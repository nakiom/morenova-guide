<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Pathology;
use App\Models\Patient;
use App\Models\Questionnaire;
use App\Models\QuestionnaireScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $pathologies = Pathology::orderBy('order')->get();
        $pathologyId = $request->get('pathology_id');

        $patientQuery = Patient::forUser($user);
        if ($pathologyId) {
            $patientQuery->where('pathology_id', $pathologyId);
        }
        $visiblePatientIds = $patientQuery->pluck('id');

        $protocolStats = [];

        if ($visiblePatientIds->isEmpty()) {
            $patients = collect();
            $pathologies = Pathology::orderBy('order')->get();

            return view('clinical.reports.index', compact(
                'pathologies',
                'pathologyId',
                'protocolStats',
                'patients',
                'user'
            ));
        }

        $questionnaires = Questionnaire::active()
            ->when($pathologyId, fn ($q) => $q->where('pathology_id', $pathologyId))
            ->with('pathology')
            ->orderBy('name')
            ->get();

        foreach ($questionnaires as $questionnaire) {
            $rows = QuestionnaireScore::query()
                ->where('questionnaire_id', $questionnaire->id)
                ->whereIn('patient_id', $visiblePatientIds)
                ->select('phase', DB::raw('AVG(score) as avg_score'), DB::raw('COUNT(*) as n'))
                ->groupBy('phase')
                ->get()
                ->keyBy('phase');

            $pre = $rows->get(QuestionnaireScore::PHASE_PRE);
            $post = $rows->get(QuestionnaireScore::PHASE_POST);
            $control = $rows->get(QuestionnaireScore::PHASE_CONTROL);

            $patientsWithBoth = QuestionnaireScore::query()
                ->where('questionnaire_id', $questionnaire->id)
                ->whereIn('patient_id', $visiblePatientIds)
                ->whereIn('phase', [QuestionnaireScore::PHASE_PRE, QuestionnaireScore::PHASE_POST])
                ->select('patient_id', 'phase', DB::raw('AVG(score) as avg_score'))
                ->groupBy('patient_id', 'phase')
                ->get()
                ->groupBy('patient_id')
                ->filter(fn ($group) => $group->count() >= 2);

            $deltas = [];
            foreach ($patientsWithBoth as $group) {
                $preScore = $group->firstWhere('phase', QuestionnaireScore::PHASE_PRE)?->avg_score;
                $postScore = $group->firstWhere('phase', QuestionnaireScore::PHASE_POST)?->avg_score;
                if ($preScore !== null && $postScore !== null) {
                    $deltas[] = (float) $postScore - (float) $preScore;
                }
            }

            $protocolStats[] = [
                'questionnaire' => $questionnaire,
                'pre_avg' => $pre ? round((float) $pre->avg_score, 2) : null,
                'pre_n' => $pre ? (int) $pre->n : 0,
                'post_avg' => $post ? round((float) $post->avg_score, 2) : null,
                'post_n' => $post ? (int) $post->n : 0,
                'control_avg' => $control ? round((float) $control->avg_score, 2) : null,
                'control_n' => $control ? (int) $control->n : 0,
                'delta_avg' => $deltas !== [] ? round(array_sum($deltas) / count($deltas), 2) : null,
                'delta_n' => count($deltas),
            ];
        }

        $patients = Patient::forUser($user)
            ->with('pathology')
            ->orderBy('name')
            ->get();

        return view('clinical.reports.index', compact(
            'pathologies',
            'pathologyId',
            'protocolStats',
            'patients',
            'user'
        ));
    }

    public function patient(Request $request, Patient $patient): View
    {
        $this->authorize('view', $patient);

        $patient->load(['pathology.translations', 'questionnaireScores.questionnaire']);

        $scoresByQuestionnaire = $patient->questionnaireScores
            ->sortBy('scored_at')
            ->groupBy('questionnaire_id');

        $locale = app()->getLocale();

        return view('clinical.reports.patient', compact('patient', 'scoresByQuestionnaire', 'locale'));
    }
}
