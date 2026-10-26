<?php

declare(strict_types=1);

namespace App\Http\Controllers\Clinical;

use App\Http\Controllers\Controller;
use App\Models\Pathology;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = Patient::with(['pathology.translations', 'user'])
            ->forUser($user)
            ->latest();

        if ($request->filled('pathology_id')) {
            $query->where('pathology_id', $request->pathology_id);
        }

        if ($request->filled('q')) {
            $q = $request->string('q')->toString();
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('external_id', 'like', "%{$q}%");
            });
        }

        $patients = $query->paginate(20)->withQueryString();
        $pathologies = Pathology::orderBy('order')->get();

        return view('clinical.patients.index', compact('patients', 'pathologies', 'user'));
    }

    public function create(): View
    {
        $pathologies = Pathology::with('translations')->orderBy('order')->get();

        return view('clinical.patients.create', compact('pathologies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'external_id' => 'nullable|string|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'sex' => 'nullable|in:'.implode(',', Patient::SEXES),
            'email' => 'nullable|email|max:255',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:50',
            'pathology_id' => 'required|exists:pathologies,id',
            'notes' => 'nullable|string',
            'expected_next_visit_at' => 'nullable|date',
            'visit_reminder_enabled' => 'nullable|boolean',
            'visit_reminder_days_before' => 'nullable|integer|in:'.implode(',', Patient::REMINDER_DAYS_OPTIONS),
        ]);

        $reminderRules = $request->boolean('visit_reminder_enabled');
        if ($reminderRules && ! $request->filled('visit_reminder_days_before')) {
            return back()->withInput()->withErrors([
                'visit_reminder_days_before' => __('messages.visit_reminder_days_required'),
            ]);
        }

        $patient = Patient::create(array_merge([
            'name' => $data['name'],
            'external_id' => $data['external_id'] ?? null,
            'age' => $data['age'] ?? null,
            'sex' => $data['sex'] ?? null,
            'email' => $data['email'] ?? null,
            'phones' => Patient::normalizePhones($data['phones'] ?? null),
            'pathology_id' => $data['pathology_id'],
            'notes' => $data['notes'] ?? null,
            'expected_next_visit_at' => $data['expected_next_visit_at'] ?? null,
            'user_id' => $request->user()->id,
        ], Patient::prepareVisitReminderAttributes(array_merge($data, [
            'visit_reminder_enabled' => $request->boolean('visit_reminder_enabled'),
        ]))));

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.patient_created'));
    }

    public function show(Request $request, Patient $patient): View
    {
        $this->authorize('view', $patient);

        $patient->load([
            'pathology.translations',
            'user.center',
            'questionnaireScores.questionnaire',
            'questionnaireInvites.items.questionnaire',
            'tags.user',
        ]);

        // Corrige huecos/números desordenados (p. ej. tras borrar o carga incorrecta).
        $patient->renumberSessions();
        $patient->load('treatmentSessions');

        $locale = app()->getLocale();
        $tagsByType = $patient->tags->groupBy('type');
        $canEditTags = $request->user()?->can('update', $patient) ?? false;

        $scoreCharts = $patient->questionnaireScores
            ->sortBy('scored_at')
            ->groupBy('questionnaire_id')
            ->map(function ($scores, $questionnaireId) {
                $questionnaire = $scores->first()?->questionnaire;

                return [
                    'id' => (int) $questionnaireId,
                    'name' => $questionnaire?->name ?? ('#'.$questionnaireId),
                    'points' => $scores->values()->map(fn ($score) => [
                        'date' => $score->scored_at?->timezone(config('app.timezone'))->format('Y-m-d') ?? '',
                        'score' => $score->score !== null ? (float) $score->score : null,
                        'phase' => $score->phase,
                        'phase_label' => __('messages.phase_'.$score->phase),
                    ])->filter(fn (array $point) => $point['date'] !== '' && $point['score'] !== null)->values()->all(),
                ];
            })
            ->filter(fn (array $chart) => $chart['points'] !== [])
            ->values()
            ->all();

        return view('clinical.patients.show', compact(
            'patient',
            'locale',
            'tagsByType',
            'canEditTags',
            'scoreCharts',
        ));
    }

    public function edit(Patient $patient): View
    {
        $this->authorize('update', $patient);

        $pathologies = Pathology::with('translations')->orderBy('order')->get();

        return view('clinical.patients.edit', compact('patient', 'pathologies'));
    }

    public function update(Request $request, Patient $patient): RedirectResponse
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'external_id' => 'nullable|string|max:100',
            'age' => 'nullable|integer|min:0|max:150',
            'sex' => 'nullable|in:'.implode(',', Patient::SEXES),
            'email' => 'nullable|email|max:255',
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:50',
            'pathology_id' => 'required|exists:pathologies,id',
            'notes' => 'nullable|string',
            'expected_next_visit_at' => 'nullable|date',
            'visit_reminder_enabled' => 'nullable|boolean',
            'visit_reminder_days_before' => 'nullable|integer|in:'.implode(',', Patient::REMINDER_DAYS_OPTIONS),
        ]);

        if ($request->boolean('visit_reminder_enabled') && ! $request->filled('visit_reminder_days_before')) {
            return back()->withInput()->withErrors([
                'visit_reminder_days_before' => __('messages.visit_reminder_days_required'),
            ]);
        }

        $patient->update(array_merge([
            'name' => $data['name'],
            'external_id' => $data['external_id'] ?? null,
            'age' => $data['age'] ?? null,
            'sex' => $data['sex'] ?? null,
            'email' => $data['email'] ?? null,
            'phones' => Patient::normalizePhones($data['phones'] ?? null),
            'pathology_id' => $data['pathology_id'],
            'notes' => $data['notes'] ?? null,
            'expected_next_visit_at' => $data['expected_next_visit_at'] ?? null,
        ], Patient::prepareVisitReminderAttributes(array_merge($data, [
            'visit_reminder_enabled' => $request->boolean('visit_reminder_enabled'),
        ]), $patient)));

        return redirect()
            ->route('clinical.patients.show', $patient)
            ->with('success', __('messages.patient_updated'));
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $this->authorize('delete', $patient);
        $patient->delete();

        return redirect()
            ->route('clinical.patients.index')
            ->with('success', __('messages.patient_deleted'));
    }
}
