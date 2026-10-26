<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Questionnaire;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

final class QuestionnaireResponseValidator
{
    /**
     * @return array{score: float|null, responses: array<string, mixed>, notes: ?string, patient_message: ?string}
     */
    public static function validate(
        Request $request,
        Questionnaire $questionnaire,
        bool $includeNotes = true,
        bool $includePatientMessage = true,
    ): array {
        $rules = [
            'responses' => 'nullable|array',
        ];

        if ($includeNotes) {
            $rules['notes'] = 'nullable|string|max:5000';
        }

        if ($includePatientMessage) {
            $rules['patient_message'] = 'nullable|string|max:5000';
        }

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
            $score = 0.0;
        }

        $patientMessage = null;
        if ($includePatientMessage && array_key_exists('patient_message', $data)) {
            $trimmed = trim((string) $data['patient_message']);
            $patientMessage = $trimmed !== '' ? $trimmed : null;
        }

        return [
            'score' => $score,
            'responses' => $responses,
            'notes' => $includeNotes ? ($data['notes'] ?? null) : null,
            'patient_message' => $patientMessage,
        ];
    }

    /**
     * @return array{questionnaire_id:int,phase:string,score:float|null,responses:array<string,mixed>,scored_at:string,notes:?string,patient_message:?string}
     */
    public static function validateClinicalPayload(Request $request, Questionnaire $questionnaire): array
    {
        $request->validate([
            'phase' => ['required', Rule::in(\App\Models\QuestionnaireScore::PHASES)],
            'scored_at' => 'required|date',
        ]);

        $validated = self::validate($request, $questionnaire, true, true);
        $patientMessage = $validated['patient_message'];
        if ($patientMessage === '') {
            $patientMessage = null;
        }

        return [
            'questionnaire_id' => $questionnaire->id,
            'phase' => (string) $request->input('phase'),
            'score' => $validated['score'],
            'responses' => $validated['responses'],
            'scored_at' => (string) $request->input('scored_at'),
            'notes' => $validated['notes'],
            'patient_message' => $patientMessage,
        ];
    }
}
