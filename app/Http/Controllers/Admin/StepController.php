<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStepRequest;
use App\Models\Pathology;
use App\Models\Step;
use App\Models\StepTranslation;
use App\Support\WebImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StepController extends Controller
{
    public function index(Request $request): View
    {
        $pathologyId = $request->get('pathology_id');
        $query = Step::with('pathology');

        if ($pathologyId) {
            $query->where('pathology_id', $pathologyId);
        }

        $steps = $query->orderBy('order')->get();
        $pathologies = Pathology::all();

        return view('admin.steps.index', compact('steps', 'pathologies', 'pathologyId'));
    }

    public function create(Request $request): View
    {
        $pathologies = Pathology::all();
        $pathologyId = $request->get('pathology_id');
        return view('admin.steps.create', compact('pathologies', 'pathologyId'));
    }

    public function store(StoreStepRequest $request): RedirectResponse
    {
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = WebImageOptimizer::storeOptimized(
                $request->file('image'),
                'steps-web',
                1200,
                82,
            );
        }

        // Calcular el orden automáticamente si no se proporciona
        $order = $request->order;
        if (!$order) {
            $maxOrder = Step::where('pathology_id', $request->pathology_id)
                ->max('order') ?? 0;
            $order = $maxOrder + 1;
        }

        $step = Step::create([
            'pathology_id' => $request->pathology_id,
            'order' => $order,
            'image_path' => $imagePath,
        ]);

        if ($request->translations) {
            foreach ($request->translations as $translation) {
                if (!empty($translation['title']) || !empty($translation['description'])) {
                    StepTranslation::create([
                        'step_id' => $step->id,
                        'locale' => $translation['locale'],
                        'title' => $translation['title'] ?? '',
                        'description' => $translation['description'] ?? '',
                    ]);
                }
            }
        }

        return redirect()->route('admin.steps.index', ['pathology_id' => $request->pathology_id])
            ->with('success', 'Paso creado exitosamente');
    }

    public function show(Step $step): View
    {
        $step->load(['pathology', 'translations']);
        return view('admin.steps.show', compact('step'));
    }

    public function edit(Step $step): View
    {
        $pathologies = Pathology::all();
        $step->load('translations');
        return view('admin.steps.edit', compact('step', 'pathologies'));
    }

    public function update(Request $request, Step $step): RedirectResponse
    {
        $request->validate([
            'pathology_id' => 'required|exists:pathologies,id',
            'order' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string|max:5',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.description' => 'required|string',
        ]);

        if ($request->hasFile('image')) {
            if ($step->image_path) {
                Storage::disk('public')->delete($step->image_path);
            }
            $step->image_path = WebImageOptimizer::storeOptimized(
                $request->file('image'),
                'steps-web',
                1200,
                82,
            );
        }

        $order = $request->order ?? $step->order;
        // Asegurar que el orden mínimo sea 1
        if ($order < 1) {
            $order = 1;
        }

        $step->update([
            'pathology_id' => $request->pathology_id,
            'order' => $order,
            'image_path' => $step->image_path,
        ]);

        $step->translations()->delete();

        if ($request->translations) {
            foreach ($request->translations as $translation) {
                if (!empty($translation['title']) || !empty($translation['description'])) {
                    StepTranslation::create([
                        'step_id' => $step->id,
                        'locale' => $translation['locale'],
                        'title' => $translation['title'] ?? '',
                        'description' => $translation['description'] ?? '',
                    ]);
                }
            }
        }

        return redirect()->route('admin.steps.index', ['pathology_id' => $request->pathology_id])
            ->with('success', 'Paso actualizado exitosamente');
    }

    public function destroy(Step $step): RedirectResponse
    {
        if ($step->image_path) {
            Storage::disk('public')->delete($step->image_path);
        }
        $pathologyId = $step->pathology_id;
        $step->delete();
        return redirect()->route('admin.steps.index', ['pathology_id' => $pathologyId])
            ->with('success', 'Paso eliminado exitosamente');
    }
}
