<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\WebImageOptimizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('order')->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:categories,slug',
            'icon' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only(['slug', 'icon', 'order']);
        
        if ($request->hasFile('background_image')) {
            $data['background_image'] = WebImageOptimizer::storeOptimized(
                $request->file('background_image'),
                'categories',
                1400,
                80,
            );
        }
        
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Categoría creada exitosamente');
    }

    public function show(Category $category): View
    {
        $category->load('pathologies');
        return view('admin.categories.show', compact('category'));
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $request->validate([
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'icon' => 'nullable|string|max:255',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'order' => 'nullable|integer',
        ]);

        $data = $request->only(['slug', 'icon', 'order']);
        
        if ($request->hasFile('background_image')) {
            // Eliminar imagen anterior si existe
            if ($category->background_image) {
                Storage::disk('public')->delete($category->background_image);
            }
            $data['background_image'] = WebImageOptimizer::storeOptimized(
                $request->file('background_image'),
                'categories',
                1400,
                80,
            );
        }
        
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Categoría eliminada exitosamente');
    }
}
