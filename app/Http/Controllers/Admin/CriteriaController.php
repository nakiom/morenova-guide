<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExclusionCriteria;
use App\Models\ExclusionCriteriaTranslation;
use App\Models\InclusionCriteria;
use App\Models\InclusionCriteriaTranslation;
use App\Models\Pathology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CriteriaController extends Controller
{
    public function index(Request $request): View
    {
        $pathologyId = $request->get('pathology_id');
        $pathologies = Pathology::all();

        $inclusionCriteria = InclusionCriteria::with('pathology')
            ->when($pathologyId, fn($q) => $q->where('pathology_id', $pathologyId))
            ->orderBy('order')
            ->get();

        $exclusionCriteria = ExclusionCriteria::with('pathology')
            ->when($pathologyId, fn($q) => $q->where('pathology_id', $pathologyId))
            ->orderBy('order')
            ->get();

        return view('admin.criteria.index', compact('inclusionCriteria', 'exclusionCriteria', 'pathologies', 'pathologyId'));
    }

    public function create(Request $request): View
    {
        $pathologies = Pathology::all();
        $pathologyId = $request->get('pathology_id');
        $type = $request->get('type', 'inclusion');
        return view('admin.criteria.create', compact('pathologies', 'pathologyId', 'type'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'type' => 'required|in:inclusion,exclusion',
            'order' => 'nullable|integer',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required_with:translations.*|string|max:5',
            'translations.*.text' => 'nullable|string',
        ]);

        if ($request->type === 'inclusion') {
            $criteria = InclusionCriteria::create([
                'pathology_id' => $request->pathology_id,
                'order' => $request->order ?? 0,
            ]);

            if ($request->translations) {
                foreach ($request->translations as $translation) {
                    if (!empty($translation['text'])) {
                        InclusionCriteriaTranslation::create([
                            'inclusion_criteria_id' => $criteria->id,
                            'locale' => $translation['locale'],
                            'text' => $translation['text'],
                        ]);
                    }
                }
            }
        } else {
            $criteria = ExclusionCriteria::create([
                'pathology_id' => $request->pathology_id,
                'order' => $request->order ?? 0,
            ]);

            if ($request->translations) {
                foreach ($request->translations as $translation) {
                    if (!empty($translation['text'])) {
                        ExclusionCriteriaTranslation::create([
                            'exclusion_criteria_id' => $criteria->id,
                            'locale' => $translation['locale'],
                            'text' => $translation['text'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.pathologies.edit', $request->pathology_id)
            ->with('success', 'Criterio creado exitosamente');
    }

    public function show(string $id)
    {
        // Not implemented
    }

    public function edit(Request $request, string $id): View
    {
        $type = $request->get('type', 'inclusion');
        $pathologies = Pathology::all();

        if ($type === 'inclusion') {
            $criteria = InclusionCriteria::with('translations')->findOrFail($id);
        } else {
            $criteria = ExclusionCriteria::with('translations')->findOrFail($id);
        }

        return view('admin.criteria.edit', compact('criteria', 'pathologies', 'type'));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'type' => 'required|in:inclusion,exclusion',
            'order' => 'nullable|integer',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required_with:translations.*|string|max:5',
            'translations.*.text' => 'nullable|string',
        ]);

        if ($request->type === 'inclusion') {
            $criteria = InclusionCriteria::findOrFail($id);
            $criteria->update([
                'pathology_id' => $request->pathology_id,
                'order' => $request->order ?? $criteria->order,
            ]);
            $criteria->translations()->delete();
            if ($request->translations) {
                foreach ($request->translations as $translation) {
                    if (!empty($translation['text'])) {
                        InclusionCriteriaTranslation::create([
                            'inclusion_criteria_id' => $criteria->id,
                            'locale' => $translation['locale'],
                            'text' => $translation['text'],
                        ]);
                    }
                }
            }
        } else {
            $criteria = ExclusionCriteria::findOrFail($id);
            $criteria->update([
                'pathology_id' => $request->pathology_id,
                'order' => $request->order ?? $criteria->order,
            ]);
            $criteria->translations()->delete();
            if ($request->translations) {
                foreach ($request->translations as $translation) {
                    if (!empty($translation['text'])) {
                        ExclusionCriteriaTranslation::create([
                            'exclusion_criteria_id' => $criteria->id,
                            'locale' => $translation['locale'],
                            'text' => $translation['text'],
                        ]);
                    }
                }
            }
        }

        return redirect()->route('admin.pathologies.edit', $request->pathology_id)
            ->with('success', 'Criterio actualizado exitosamente');
    }

    public function destroy(Request $request, string $id): RedirectResponse
    {
        $type = $request->get('type', 'inclusion');
        
        if ($type === 'inclusion') {
            $criteria = InclusionCriteria::findOrFail($id);
            $pathologyId = $criteria->pathology_id;
            $criteria->delete();
        } else {
            $criteria = ExclusionCriteria::findOrFail($id);
            $pathologyId = $criteria->pathology_id;
            $criteria->delete();
        }

        return redirect()->route('admin.pathologies.edit', $pathologyId)
            ->with('success', 'Criterio eliminado exitosamente');
    }
}
