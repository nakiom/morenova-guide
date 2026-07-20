<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Pathology;
use App\Support\SuggestionCaptcha;
use Illuminate\Http\Request;

class PathologyViewController extends Controller
{
    public function index(string $categorySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $pathologies = $category->pathologies()->with(['steps', 'sessionChecklistItems'])->get();
        
        $locale = app()->getLocale();
        
        return view('pathologies.index', compact('category', 'pathologies', 'locale'));
    }

    public function show(string $categorySlug, string $pathologySlug)
    {
        $category = Category::where('slug', $categorySlug)->firstOrFail();
        $pathology = Pathology::where('slug', $pathologySlug)
            ->where('category_id', $category->id)
            ->with([
                'category',
                'steps.translations',
                'sessionChecklistItems.translations',
                'followUpItems.translations',
                'applicationAreas.translations',
                'inclusionCriteria.translations',
                'exclusionCriteria.translations',
            ])
            ->firstOrFail();
        
        $locale = app()->getLocale();

        if (! auth()->check()) {
            SuggestionCaptcha::ensure();
        }

        return view('pathologies.show', compact('category', 'pathology', 'locale'));
    }
}
