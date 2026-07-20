<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FollowUpItem;
use App\Models\FollowUpItemTranslation;
use App\Models\Pathology;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $pathologyId = $request->get('pathology_id');
        $query = FollowUpItem::with('pathology');

        if ($pathologyId) {
            $query->where('pathology_id', $pathologyId);
        }

        $items = $query->orderBy('order')->get();
        $pathologies = Pathology::all();

        return view('admin.follow-up.index', compact('items', 'pathologies', 'pathologyId'));
    }

    public function create(Request $request): View
    {
        $pathologies = Pathology::all();
        $pathologyId = $request->get('pathology_id');

        return view('admin.follow-up.create', compact('pathologies', 'pathologyId'));
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
            $maxOrder = FollowUpItem::where('pathology_id', $request->pathology_id)->max('order') ?? 0;
            $order = $maxOrder + 1;
        }

        $item = FollowUpItem::create([
            'pathology_id' => $request->pathology_id,
            'order' => $order,
        ]);

        foreach ($request->translations as $translation) {
            if (!empty($translation['title'])) {
                FollowUpItemTranslation::create([
                    'follow_up_item_id' => $item->id,
                    'locale' => $translation['locale'],
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? '',
                ]);
            }
        }

        return redirect()->route('admin.follow-up.index', ['pathology_id' => $request->pathology_id])
            ->with('success', 'Ítem de seguimiento creado exitosamente');
    }

    public function edit(FollowUpItem $followUp): View
    {
        $pathologies = Pathology::all();
        $followUp->load('translations');

        return view('admin.follow-up.edit', compact('followUp', 'pathologies'));
    }

    public function update(Request $request, FollowUpItem $followUp): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'order' => 'nullable|integer|min:1',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'nullable|string',
        ]);

        $followUp->update([
            'pathology_id' => $request->pathology_id,
            'order' => $request->order ?? $followUp->order,
        ]);

        $followUp->translations()->delete();

        foreach ($request->translations as $translation) {
            if (!empty($translation['title'])) {
                FollowUpItemTranslation::create([
                    'follow_up_item_id' => $followUp->id,
                    'locale' => $translation['locale'],
                    'title' => $translation['title'],
                    'description' => $translation['description'] ?? '',
                ]);
            }
        }

        return redirect()->route('admin.follow-up.index', ['pathology_id' => $request->pathology_id])
            ->with('success', 'Ítem de seguimiento actualizado exitosamente');
    }

    public function destroy(FollowUpItem $followUp): RedirectResponse
    {
        $pathologyId = $followUp->pathology_id;
        $followUp->delete();

        return redirect()->route('admin.follow-up.index', ['pathology_id' => $pathologyId])
            ->with('success', 'Ítem de seguimiento eliminado exitosamente');
    }
}
