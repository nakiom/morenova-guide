<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\TreatmentSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreatmentSessionController extends Controller
{
    public function create(Patient $patient): View
    {
        $this->authorize('update', $patient);

        $patient->load('pathology');
        $pathology = $patient->pathology;
        $nextNumber = $patient->treatmentSessions()->count() + 1;
        $defaultShocks = (int) ($pathology?->shots_per_session ?? 0);
        $defaultEnergy = $pathology?->energy_kv;
        $defaultFrequency = $pathology?->frequency_hz;

        return view('clinical.sessions.create', compact(
            'patient',
            'pathology',
            'nextNumber',
            'defaultShocks',
            'defaultEnergy',
            'defaultFrequency',
        ));
    }

    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'session_date' => 'required|date',
            'shocks' => 'nullable|integer|min:0',
            'energy_kv' => 'nullable|numeric|min:0|max:99.99',
            'frequency_hz' => 'nullable|numeric|min:0|max:999.99',
            'notes' => 'nullable|string',
            'expected_next_visit_at' => 'nullable|date',
        ]);

        $patient->loadMissing('pathology');
        $pathology = $patient->pathology;

        $shocks = array_key_exists('shocks', $data) && $data['shocks'] !== null
            ? (int) $data['shocks']
            : (int) ($pathology?->shots_per_session ?? 0);

        $energy = array_key_exists('energy_kv', $data) && $data['energy_kv'] !== null && $data['energy_kv'] !== ''
            ? (float) $data['energy_kv']
            : ($pathology?->energy_kv !== null ? (float) $pathology->energy_kv : null);

        $frequency = array_key_exists('frequency_hz', $data) && $data['frequency_hz'] !== null && $data['frequency_hz'] !== ''
            ? (float) $data['frequency_hz']
            : ($pathology?->frequency_hz !== null ? (float) $pathology->frequency_hz : null);

        TreatmentSession::create([
            'patient_id' => $patient->id,
            'session_number' => $patient->treatmentSessions()->count() + 1,
            'session_date' => $data['session_date'],
            'shocks' => $shocks,
            'energy_kv' => $energy,
            'frequency_hz' => $frequency,
            'notes' => $data['notes'] ?? null,
        ]);

        $patient->renumberSessions();

        if (array_key_exists('expected_next_visit_at', $data)) {
            $newVisit = $data['expected_next_visit_at'] ?: null;
            $updates = ['expected_next_visit_at' => $newVisit];

            $oldVisit = $patient->expected_next_visit_at?->toDateString();
            $newVisitStr = $newVisit ? \Illuminate\Support\Carbon::parse($newVisit)->toDateString() : null;

            if ($oldVisit !== $newVisitStr) {
                $updates['visit_reminder_sent_for'] = null;
            }

            $patient->update($updates);
        }

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.session_created'))
            ->with('prompt_questionnaire', true);
    }

    public function edit(Patient $patient, TreatmentSession $session): View
    {
        $this->authorize('update', $patient);
        abort_unless($session->patient_id === $patient->id, 404);

        $patient->load('pathology');
        $pathology = $patient->pathology;

        return view('clinical.sessions.edit', compact('patient', 'session', 'pathology'));
    }

    public function update(Request $request, Patient $patient, TreatmentSession $session): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($session->patient_id === $patient->id, 404);

        $data = $request->validate([
            'session_date' => 'required|date',
            'shocks' => 'required|integer|min:0',
            'energy_kv' => 'nullable|numeric|min:0|max:99.99',
            'frequency_hz' => 'nullable|numeric|min:0|max:999.99',
            'notes' => 'nullable|string',
        ]);

        $session->update([
            'session_date' => $data['session_date'],
            'shocks' => $data['shocks'],
            'energy_kv' => $data['energy_kv'] ?? null,
            'frequency_hz' => $data['frequency_hz'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        $patient->renumberSessions();

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.session_updated'));
    }

    public function destroy(Patient $patient, TreatmentSession $session): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($session->patient_id === $patient->id, 404);

        $session->delete();
        $patient->renumberSessions();

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.session_deleted'));
    }
}
