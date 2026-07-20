<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\View\View;

class ProtocolSummaryController extends Controller
{
    public function __invoke(): View
    {
        $locale = app()->getLocale();

        $categories = Category::query()
            ->with(['pathologies' => fn ($q) => $q->orderBy('order')->with('translations')])
            ->orderBy('order')
            ->get();

        $calculatorProtocols = $categories
            ->flatMap(function (Category $category) use ($locale) {
                $catKey = str_replace('-', '_', $category->slug);

                return $category->pathologies->map(function ($pathology) use ($locale, $catKey) {
                    return [
                        'id' => $pathology->id,
                        'slug' => $pathology->slug,
                        'name' => $pathology->translations->firstWhere('locale', $locale)?->name
                            ?? $pathology->translations->first()?->name
                            ?? $pathology->slug,
                        'category' => __('messages.'.$catKey),
                        'transducer' => $pathology->transducer,
                        'sessions_label' => $pathology->sessions_count,
                        'sessions' => self::parseSessionsRange($pathology->sessions_count),
                        'shots_per_session' => $pathology->shots_per_session ? (int) $pathology->shots_per_session : null,
                    ];
                });
            })
            ->values()
            ->all();

        return view('admin.protocol-summary.index', compact(
            'categories',
            'locale',
            'calculatorProtocols',
        ));
    }

    /**
     * @return array{min: int, max: int}|null
     */
    private static function parseSessionsRange(?string $value): ?array
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        if (preg_match('/(\d+)\s*[-–]\s*(\d+)/u', $value, $matches)) {
            return [
                'min' => (int) $matches[1],
                'max' => (int) $matches[2],
            ];
        }

        if (preg_match('/(\d+)/', $value, $matches)) {
            $n = (int) $matches[1];

            return ['min' => $n, 'max' => $n];
        }

        return null;
    }
}
