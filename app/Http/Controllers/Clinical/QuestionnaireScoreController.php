<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Questionnaire;
use App\Models\QuestionnaireScore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuestionnaireScoreController extends Controller
{
    public function create(Patient $patient, Request $request): View
    {
        $this->authorize('update', $patient);

        $questionnaires = $this->questionnairesFor($patient);
        $selectedId = (int) old('questionnaire_id', $request->integer('questionnaire_id') ?: $questionnaires->first()?->id);
        $selected = $questionnaires->firstWhere('id', $selectedId) ?? $questionnaires->first();

        return view('clinical.scores.create', [
            'patient' => $patient,
            'questionnaires' => $questionnaires,
            'selected' => $selected,
            'phases' => QuestionnaireScore::PHASES,
            'score' => null,
        ]);
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $questionnaire = $this->resolveQuestionnaire($request, $patient);
        $payload = $this->validatedPayload($request, $questionnaire);

        QuestionnaireScore::create([
            ...$payload,
            'patient_id' => $patient->id,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.score_created'));
    }

    public function edit(Patient $patient, QuestionnaireScore $score): View
    {
        $this->authorize('update', $patient);
        abort_unless($score->patient_id === $patient->id, 404);

        $questionnaires = $this->questionnairesFor($patient);
        if ($score->questionnaire && ! $questionnaires->contains('id', $score->questionnaire_id)) {
            $questionnaires = $questionnaires->prepend($score->questionnaire);
        }
        $selectedId = (int) old('questionnaire_id', $score->questionnaire_id);
        $selected = $questionnaires->firstWhere('id', $selectedId) ?? $score->questionnaire;

        return view('clinical.scores.edit', [
            'patient' => $patient,
            'score' => $score,
            'questionnaires' => $questionnaires,
            'selected' => $selected,
            'phases' => QuestionnaireScore::PHASES,
        ]);
    }

    public function update(Request $request, Patient $patient, QuestionnaireScore $score): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($score->patient_id === $patient->id, 404);

        $questionnaire = $this->resolveQuestionnaire($request, $patient, $score);
        $payload = $this->validatedPayload($request, $questionnaire);

        $score->update($payload);

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.score_updated'));
    }

    public function destroy(Patient $patient, QuestionnaireScore $score): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($score->patient_id === $patient->id, 404);

        $score->delete();

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.score_deleted'));
    }

    private function questionnairesFor(Patient $patient)
    {
        return Questionnaire::active()
            ->where('pathology_id', $patient->pathology_id)
            ->orderBy('name')
            ->get();
    }

    private function resolveQuestionnaire(Request $request, Patient $patient, ?QuestionnaireScore $current = null): Questionnaire
    {
        $request->validate([
            'questionnaire_id' => [
                'required',
                Rule::exists('questionnaires', 'id')->where(function ($q) use ($patient, $current) {
                    $q->where('pathology_id', $patient->pathology_id)
                        ->where(function ($inner) use ($current) {
                            $inner->where('is_active', true);
                            if ($current) {
                                $inner->orWhere('id', $current->questionnaire_id);
                            }
                        });
                }),
            ],
        ]);

        return Questionnaire::findOrFail((int) $request->input('questionnaire_id'));
    }

    /**
     * @return array{questionnaire_id:int,phase:string,score:float|null,responses:array<string,mixed>,scored_at:string,notes:?string}
     */
    private function validatedPayload(Request $request, Questionnaire $questionnaire): array
    {
        $rules = [
            'phase' => ['required', Rule::in(QuestionnaireScore::PHASES)],
            'scored_at' => 'required|date',
            'notes' => 'nullable|string',
            'responses' => 'nullable|array',
        ];

        foreach ($questionnaire->fields() as $field) {
            $key = 'responses.'.$field['key'];
            $fieldRules = [];

            if (($field['required'] ?? false) === true) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            $type = $field['type'] ?? 'text';
            if (in_array($type, ['number', 'scale', 'select'], true)) {
                $fieldRules[] = 'numeric';
                if (isset($field['min'])) {
                    $fieldRules[] = 'min:'.$field['min'];
                }
                if (isset($field['max'])) {
                    $fieldRules[] = 'max:'.$field['max'];
                }
            } else {
                $fieldRules[] = 'string';
            }

            $rules[$key] = $fieldRules;
        }

        if ($questionnaire->scoreMode() === 'manual' && ! $questionnaire->fields()) {
            $rules['score'] = 'required|numeric';
        }

        $data = $request->validate($rules);
        $responses = $data['responses'] ?? [];

        $score = $questionnaire->computeScore($responses);
        if ($score === null && isset($data['score'])) {
            $score = (float) $data['score'];
        }

        if ($score === null) {
            // Si no hay score computable, guardar 0 y dejar detalle en responses.
            $score = 0;
        }

        return [
            'questionnaire_id' => $questionnaire->id,
            'phase' => $data['phase'],
            'score' => $score,
            'responses' => $responses,
            'scored_at' => $data['scored_at'],
            'notes' => $data['notes'] ?? null,
        ];
    }
}
