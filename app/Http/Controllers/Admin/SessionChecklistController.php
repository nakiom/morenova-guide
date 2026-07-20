<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pathology;
use App\Models\SessionChecklistItem;
use App\Models\SessionChecklistItemTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SessionChecklistController extends Controller
{
    public function index(Request $request): View
    {
        $pathologyId = $request->get('pathology_id');
        $query = SessionChecklistItem::with('pathology');

        if ($pathologyId) {
            $query->where('pathology_id', $pathologyId);
        }

        $items = $query->orderBy('order')->get();
        $pathologies = Pathology::all();

        return view('admin.session-checklist.index', compact('items', 'pathologies', 'pathologyId'));
    }

    public function create(Request $request): View
    {
        $pathologies = Pathology::all();
        $pathologyId = $request->get('pathology_id');

        return view('admin.session-checklist.create', compact('pathologies', 'pathologyId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'order' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.text' => 'required|string',
        ]);

        $order = $request->order;
        if (!$order) {
            $maxOrder = SessionChecklistItem::where('pathology_id', $request->pathology_id)->max('order') ?? 0;
            $order = $maxOrder + 1;
        }

        $item = SessionChecklistItem::create([
            'pathology_id' => $request->pathology_id,
            'order' => $order,
        ]);

        foreach ($request->translations as $translation) {
            if (!empty($translation['text'])) {
                SessionChecklistItemTranslation::create([
                    'session_checklist_item_id' => $item->id,
                    'locale' => $translation['locale'],
                    'text' => $translation['text'],
                ]);
            }
        }

        return redirect()->route('admin.session-checklist.index', ['pathology_id' => $request->pathology_id])
            ->with('success', 'Ítem de verificación creado exitosamente');
    }

    public function edit(SessionChecklistItem $sessionChecklist): View
    {
        $pathologies = Pathology::all();
        $sessionChecklist->load('translations');

        return view('admin.session-checklist.edit', compact('sessionChecklist', 'pathologies'));
    }

    public function update(Request $request, SessionChecklistItem $sessionChecklist): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'order' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.text' => 'required|string',
        ]);

        $sessionChecklist->update([
            'pathology_id' => $request->pathology_id,
            'order' => $request->order ?? $sessionChecklist->order,
        ]);

        $sessionChecklist->translations()->delete();

        foreach ($request->translations as $translation) {
            if (!empty($translation['text'])) {
                SessionChecklistItemTranslation::create([
                    'session_checklist_item_id' => $sessionChecklist->id,
                    'locale' => $translation['locale'],
                    'text' => $translation['text'],
                ]);
            }
        }

        return redirect()->route('admin.session-checklist.index', ['pathology_id' => $request->pathology_id])
            ->with('success', 'Ítem de verificación actualizado exitosamente');
    }

    public function destroy(SessionChecklistItem $sessionChecklist): RedirectResponse
    {
        $pathologyId = $sessionChecklist->pathology_id;
        $sessionChecklist->delete();

        return redirect()->route('admin.session-checklist.index', ['pathology_id' => $pathologyId])
            ->with('success', 'Ítem de verificación eliminado exitosamente');
    }
}
