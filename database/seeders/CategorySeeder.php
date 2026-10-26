<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'slug' => 'womens-health',
                'icon' => 'fas fa-venus',
                'order' => 1,
            ],
            [
                'slug' => 'mens-health',
                'icon' => 'fas fa-mars',
                'order' => 2,
            ],
            [
                'slug' => 'general-health',
                'icon' => 'fas fa-heartbeat',
                'order' => 3,
            ],
        ];

        foreach ($categories as $data) {
            $data['background_image'] = $this->seedBackgroundImage($data['slug']);

            Category::updateOrCreate(
                ['slug' => $data['slug']],
                array_filter($data, fn ($value) => $value !== null)
            );
        }
    }

    private function seedBackgroundImage(string $slug): ?string
    {
        $assetPath = database_path("seeders/assets/categories/{$slug}.jpg");
        if (!File::exists($assetPath)) {
            return $this->findLegacyBackgroundImage($slug);
        }

        $storagePath = "categories/{$slug}.jpg";
        $disk = Storage::disk('public');

        if (!$disk->exists('categories')) {
            $disk->makeDirectory('categories');
        }

        $disk->put($storagePath, File::get($assetPath));

        return $storagePath;
    }

    private function findLegacyBackgroundImage(string $slug): ?string
    {
        $disk = Storage::disk('public');

        if (!$disk->exists('categories')) {
            return null;
        }

        foreach ($disk->files('categories') as $path) {
            if (str_starts_with(basename($path), $slug)) {
                return $path;
            }
        }

        return null;
    }
}
