<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\Pathology;
use App\Models\Questionnaire;
use App\Models\QuestionnaireScore;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CenterSuccessReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $locale = app()->getLocale();
        $pathologies = Pathology::with('translations')->orderBy('order')->get();
        $pathologyId = $request->integer('pathology_id') ?: null;

        $rankings = [];

        $pathologiesToRank = $pathologyId
            ? $pathologies->where('id', $pathologyId)->values()
            : $pathologies;

        foreach ($pathologiesToRank as $pathology) {
            $rankings[] = [
                'pathology' => $pathology,
                'rows' => $this->rankingForPathology($pathology),
            ];
        }

        return view('admin.reports.center-success', compact(
            'pathologies',
            'pathologyId',
            'rankings',
            'locale',
        ));
    }

    /**
     * @return list<array{
     *   center: Center,
     *   patients_total: int,
     *   patients_evaluated: int,
     *   patients_improved: int,
     *   success_rate: float|null,
     *   avg_delta: float|null
     * }>
     */
    private function rankingForPathology(Pathology $pathology): array
    {
        $questionnaires = Questionnaire::active()
            ->where('pathology_id', $pathology->id)
            ->get()
            ->keyBy('id');

        if ($questionnaires->isEmpty()) {
            return [];
        }

        $scores = QuestionnaireScore::query()
            ->whereIn('questionnaire_id', $questionnaires->keys())
            ->whereIn('phase', [QuestionnaireScore::PHASE_PRE, QuestionnaireScore::PHASE_POST])
            ->whereHas('patient', fn ($q) => $q->where('pathology_id', $pathology->id))
            ->with(['patient.user.center'])
            ->get();

        /** @var Collection<int, Collection<int, Collection<string, float>>> $byCenter */
        $byCenter = collect();

        foreach ($scores->groupBy(fn (QuestionnaireScore $s) => $s->patient?->user?->center_id) as $centerId => $centerScores) {
            if (! $centerId) {
                continue;
            }

            $patientOutcomes = [];

            foreach ($centerScores->groupBy('patient_id') as $patientId => $patientScores) {
                $improvements = 0;
                $pairs = 0;
                $deltas = [];

                foreach ($patientScores->groupBy('questionnaire_id') as $questionnaireId => $qScores) {
                    $questionnaire = $questionnaires->get($questionnaireId);
                    if (! $questionnaire) {
                        continue;
                    }

                    $pre = $qScores->where('phase', QuestionnaireScore::PHASE_PRE)->avg('score');
                    $post = $qScores->where('phase', QuestionnaireScore::PHASE_POST)->avg('score');

                    if ($pre === null || $post === null) {
                        continue;
                    }

                    $delta = (float) $post - (float) $pre;
                    $pairs++;
                    $deltas[] = $questionnaire->signedImprovement($delta);

                    if ($questionnaire->isImprovement($delta)) {
                        $improvements++;
                    }
                }

                if ($pairs === 0) {
                    continue;
                }

                $patientOutcomes[] = [
                    'improved' => $improvements >= max(1, (int) ceil($pairs / 2)),
                    'avg_signed_delta' => array_sum($deltas) / count($deltas),
                ];
            }

            if ($patientOutcomes === []) {
                continue;
            }

            $center = $centerScores->first()?->patient?->user?->center;
            if (! $center) {
                continue;
            }

            $evaluated = count($patientOutcomes);
            $improved = count(array_filter($patientOutcomes, fn (array $o) => $o['improved']));
            $avgDelta = array_sum(array_column($patientOutcomes, 'avg_signed_delta')) / $evaluated;

            $byCenter->put((int) $centerId, [
                'center' => $center,
                'patients_total' => $centerScores->pluck('patient_id')->unique()->count(),
                'patients_evaluated' => $evaluated,
                'patients_improved' => $improved,
                'success_rate' => round(($improved / $evaluated) * 100, 1),
                'avg_delta' => round($avgDelta, 2),
            ]);
        }

        return $byCenter
            ->sortByDesc(fn (array $row) => [
                $row['success_rate'] ?? 0,
                $row['avg_delta'] ?? PHP_FLOAT_MIN,
                $row['patients_evaluated'],
            ])
            ->values()
            ->all();
    }
}
