<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\PatientTag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientTagController extends Controller
{
    public function store(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $data = $this->validated($request);

        $patient->tags()->create([
            ...$data,
            'user_id' => $request->user()->id,
        ]);

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.patient_tag_created'));
    }

    public function update(Request $request, Patient $patient, PatientTag $tag): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($tag->patient_id === $patient->id, 404);

        $tag->update($this->validated($request));

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.patient_tag_updated'));
    }

    public function destroy(Patient $patient, PatientTag $tag): RedirectResponse
    {
        $this->authorize('update', $patient);
        abort_unless($tag->patient_id === $patient->id, 404);

        $tag->delete();

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.patient_tag_deleted'));
    }

    /**
     * @return array{type: string, content: string}
     */
    private function validated(Request $request): array
    {
        /** @var array{type: string, content: string} */
        return $request->validate([
            'type' => ['required', Rule::in(PatientTag::TYPES)],
            'content' => ['required', 'string', 'max:500'],
        ]);
    }
}
