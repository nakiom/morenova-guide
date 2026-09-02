<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Cache::remember('home.categories', 3600, static fn () => Category::query()
            ->orderBy('order')
            ->get(['id', 'slug', 'icon', 'background_image', 'order']));

        return view('home.index', compact('categories'));
    }
}
