<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Center;
use App\Models\Patient;
use App\Models\QuestionnaireScore;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CenterCasesReportController extends Controller
{
    public function __invoke(Request $request): View
    {
        $locale = app()->getLocale();
        $centers = Center::query()->orderBy('name')->get();
        $centerId = $request->integer('center_id') ?: null;
        $status = $request->string('status')->toString();

        if (! in_array($status, ['active', 'finished'], true)) {
            $status = '';
        }

        $patients = Patient::query()
            ->with([
                'user.center',
                'pathology.translations',
                'treatmentSessions',
                'questionnaireScores.questionnaire',
            ])
            ->whereHas('user', function ($query) use ($centerId) {
                $query->whereNotNull('center_id');

                if ($centerId) {
                    $query->where('center_id', $centerId);
                }
            })
            ->get();

        $blocks = $patients
            ->groupBy(fn (Patient $patient) => (int) $patient->user?->center_id)
            ->map(function (Collection $centerPatients) use ($status, $locale) {
                $center = $centerPatients->first()?->user?->center;
                if (! $center instanceof Center) {
                    return null;
                }

                $allRows = $centerPatients
                    ->map(fn (Patient $patient) => $this->row($patient, $locale))
                    ->sortBy([
                        ['status_order', 'asc'],
                        ['started_at', 'desc'],
                    ])
                    ->values();

                $rows = $status === ''
                    ? $allRows
                    : $allRows->where('status', $status)->values();

                return [
                    'center' => $center,
                    'active' => $allRows->where('status', 'active')->count(),
                    'finished' => $allRows->where('status', 'finished')->count(),
                    'sessions' => (int) $allRows->sum('sessions'),
                    'rows' => $rows,
                ];
            })
            ->filter()
            ->sortBy(fn (array $block) => mb_strtolower($block['center']->name))
            ->values();

        if ($centerId && ! $blocks->contains(fn (array $block) => $block['center']->id === $centerId)) {
            $center = $centers->firstWhere('id', $centerId);
            if ($center) {
                $blocks->push([
                    'center' => $center,
                    'active' => 0,
                    'finished' => 0,
                    'sessions' => 0,
                    'rows' => collect(),
                ]);
            }
        }

        $totals = [
            'active' => (int) $blocks->sum('active'),
            'finished' => (int) $blocks->sum('finished'),
            'sessions' => (int) $blocks->sum('sessions'),
        ];

        return view('admin.reports.center-cases', compact(
            'centers',
            'centerId',
            'status',
            'blocks',
            'totals',
            'locale',
        ));
    }

    /**
     * @return array{
     *     patient: Patient,
     *     protocol: ?string,
     *     started_at: \Illuminate\Support\Carbon,
     *     status: string,
     *     status_order: int,
     *     sessions: int,
     *     scores: list<array{name: string, initial: ?string, final: ?string}>
     * }
     */
    private function row(Patient $patient, string $locale): array
    {
        $active = $patient->isTreatmentActive();

        $scores = $patient->questionnaireScores
            ->sortBy(fn (QuestionnaireScore $score) => [
                $score->scored_at?->timestamp ?? 0,
                $score->id,
            ])
            ->groupBy('questionnaire_id')
            ->map(function (Collection $group) {
                /** @var QuestionnaireScore|null $first */
                $first = $group->first();
                /** @var QuestionnaireScore|null $last */
                $last = $group->count() > 1 ? $group->last() : null;

                return [
                    'name' => $first?->questionnaire?->name ?? '—',
                    'initial' => $this->formatScore($first?->score),
                    'final' => $this->formatScore($last?->score),
                ];
            })
            ->values()
            ->all();

        return [
            'patient' => $patient,
            'protocol' => $patient->pathology?->translation($locale)?->name ?? $patient->pathology?->slug,
            'started_at' => $patient->treatmentStartedAt(),
            'status' => $active ? 'active' : 'finished',
            'status_order' => $active ? 0 : 1,
            'sessions' => $patient->treatmentSessions->count(),
            'scores' => $scores,
        ];
    }

    private function formatScore(mixed $score): ?string
    {
        if ($score === null || $score === '') {
            return null;
        }

        return rtrim(rtrim(number_format((float) $score, 2, '.', ''), '0'), '.');
    }
}
