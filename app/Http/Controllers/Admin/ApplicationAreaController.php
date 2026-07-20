<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApplicationArea;
use App\Models\ApplicationAreaTranslation;
use App\Models\Pathology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApplicationAreaController extends Controller
{
    public function index(Request $request): View
    {
        $pathologyId = $request->get('pathology_id');
        $query = ApplicationArea::with('pathology');

        if ($pathologyId) {
            $query->where('pathology_id', $pathologyId);
        }

        $items = $query->orderBy('order')->get();
        $pathologies = Pathology::orderBy('order')->get();

        return view('admin.application-areas.index', compact('items', 'pathologies', 'pathologyId'));
    }

    public function create(Request $request): View
    {
        $pathologies = Pathology::orderBy('order')->get();
        $pathologyId = $request->get('pathology_id');

        return view('admin.application-areas.create', compact('pathologies', 'pathologyId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'order' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        $order = $request->order;
        if (!$order) {
            $maxOrder = ApplicationArea::where('pathology_id', $request->pathology_id)->max('order') ?? 0;
            $order = $maxOrder + 1;
        }

        $item = ApplicationArea::create([
            'pathology_id' => $request->pathology_id,
            'order' => $order,
        ]);

        foreach ($request->translations as $translation) {
            if (!empty($translation['title'])) {
                ApplicationAreaTranslation::create([
                    'application_area_id' => $item->id,
                    'locale' => $translation['locale'],
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? '',
                ]);
            }
        }

        return redirect()
            ->route('admin.application-areas.index', ['pathology_id' => $request->pathology_id])
            ->with('success', __('messages.application_area_created'));
    }

    public function edit(ApplicationArea $applicationArea): View
    {
        $pathologies = Pathology::orderBy('order')->get();
        $applicationArea->load('translations');

        return view('admin.application-areas.edit', compact('applicationArea', 'pathologies'));
    }

    public function update(Request $request, ApplicationArea $applicationArea): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'order' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        $applicationArea->update([
            'pathology_id' => $request->pathology_id,
            'order' => $request->order ?? $applicationArea->order,
        ]);

        $applicationArea->translations()->delete();

        foreach ($request->translations as $translation) {
            if (!empty($translation['title'])) {
                ApplicationAreaTranslation::create([
                    'application_area_id' => $applicationArea->id,
                    'locale' => $translation['locale'],
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? '',
                ]);
            }
        }

        return redirect()
            ->route('admin.application-areas.index', ['pathology_id' => $request->pathology_id])
            ->with('success', __('messages.application_area_updated'));
    }

    public function destroy(ApplicationArea $applicationArea): RedirectResponse
    {
        $pathologyId = $applicationArea->pathology_id;
        $applicationArea->delete();

        return redirect()
            ->route('admin.application-areas.index', ['pathology_id' => $pathologyId])
            ->with('success', __('messages.application_area_deleted'));
    }
}
