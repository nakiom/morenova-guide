<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePathologyRequest;
use App\Http\Requests\UpdatePathologyRequest;
use App\Models\Category;
use App\Models\Pathology;
use App\Models\PathologyTranslation;
use App\Support\WebImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PathologyController extends Controller
{
    public function index(): View
    {
        $pathologies = Pathology::with('category')->orderBy('order')->get();
        return view('admin.pathologies.index', compact('pathologies'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.pathologies.create', compact('categories'));
    }

    public function store(StorePathologyRequest $request): RedirectResponse
    {
        $data = $request->only([
            'category_id', 'slug', 'icon', 'order',
            'transducer', 'sessions_count', 'sessions_frequency',
            'session_duration_minutes', 'applications_per_session',
            'shots_per_application', 'shots_per_session',
            'energy_kv', 'frequency_hz',
        ]);

        if ($request->hasFile('background_image')) {
            $data['background_image'] = WebImageOptimizer::storeOptimized(
                $request->file('background_image'),
                'pathologies',
                1600,
                80,
            );
        }
        
        $pathology = Pathology::create($data);

        if ($request->translations) {
            foreach ($request->translations as $translation) {
                if (!empty($translation['name'])) {
                    PathologyTranslation::create([
                        'pathology_id' => $pathology->id,
                        'locale' => $translation['locale'],
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.pathologies.index')->with('success', 'Patología creada exitosamente');
    }

    public function show(Pathology $pathology): View
    {
        $pathology->load(['category', 'steps', 'inclusionCriteria', 'exclusionCriteria']);
        return view('admin.pathologies.show', compact('pathology'));
    }

    public function edit(Pathology $pathology): View
    {
        $categories = Category::all();
        $pathology->load(['translations', 'inclusionCriteria.translations', 'exclusionCriteria.translations']);
        return view('admin.pathologies.edit', compact('pathology', 'categories'));
    }

    public function update(UpdatePathologyRequest $request, Pathology $pathology): RedirectResponse
    {
        $data = $request->only([
            'category_id', 'slug', 'icon', 'order',
            'transducer', 'sessions_count', 'sessions_frequency',
            'session_duration_minutes', 'applications_per_session',
            'shots_per_application', 'shots_per_session',
            'energy_kv', 'frequency_hz',
        ]);

        if ($request->hasFile('background_image')) {
            // Eliminar imagen anterior si existe
            if ($pathology->background_image) {
                Storage::disk('public')->delete($pathology->background_image);
            }
            $data['background_image'] = WebImageOptimizer::storeOptimized(
                $request->file('background_image'),
                'pathologies',
                1600,
                80,
            );
        }
        
        $pathology->update($data);

        $pathology->translations()->delete();

        if ($request->translations) {
            foreach ($request->translations as $translation) {
                if (!empty($translation['name'])) {
                    PathologyTranslation::create([
                        'pathology_id' => $pathology->id,
                        'locale' => $translation['locale'],
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('admin.pathologies.index')->with('success', 'Patología actualizada exitosamente');
    }

    public function destroy(Pathology $pathology): RedirectResponse
    {
        $pathology->delete();
        return redirect()->route('admin.pathologies.index')->with('success', 'Patología eliminada exitosamente');
    }
}
