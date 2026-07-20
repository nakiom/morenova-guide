<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Pathology;
use App\Models\PathologyTranslation;
use Database\Seeders\Concerns\LoadsPathologyFixture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PathologySeeder extends Seeder
{
    use LoadsPathologyFixture;

    public function run(): void
    {
        $data = $this->loadPathologyFixture('pathologies');

        foreach ($data as $categorySlug => $pathologies) {
            $category = Category::where('slug', $categorySlug)->first();
            if (!$category) {
                continue;
            }

            foreach ($pathologies as $pathologyData) {
                $attributes = [
                    'icon' => $pathologyData['icon'],
                    'order' => $pathologyData['order'],
                ];

                $backgroundImage = $this->seedBackgroundImage($pathologyData['slug']);
                if ($backgroundImage) {
                    $attributes['background_image'] = $backgroundImage;
                }

                if (!empty($pathologyData['parameters'])) {
                    $attributes = array_merge($attributes, $pathologyData['parameters']);
                }

                $pathology = Pathology::updateOrCreate(
                    ['slug' => $pathologyData['slug'], 'category_id' => $category->id],
                    $attributes
                );

                foreach ($pathologyData['translations'] as $locale => $translation) {
                    PathologyTranslation::updateOrCreate(
                        ['pathology_id' => $pathology->id, 'locale' => $locale],
                        $translation
                    );
                }
            }
        }

        $this->removeDeprecatedPathologies();
    }

    private function seedBackgroundImage(string $slug): ?string
    {
        $assetPath = database_path("seeders/assets/pathologies/{$slug}.jpg");
        if (!File::exists($assetPath)) {
            return $this->findLegacyBackgroundImage($slug);
        }

        $storagePath = "pathologies/{$slug}.jpg";
        $disk = Storage::disk('public');

        if (!$disk->exists('pathologies')) {
            $disk->makeDirectory('pathologies');
        }

        $disk->put($storagePath, File::get($assetPath));

        return $storagePath;
    }

    private function findLegacyBackgroundImage(string $slug): ?string
    {
        $disk = Storage::disk('public');

        if (!$disk->exists('pathologies')) {
            return null;
        }

        $newest = null;
        $newestSuffix = -1;

        foreach ($disk->files('pathologies') as $path) {
            $filename = basename($path);
            if (!str_starts_with($filename, $slug.'_') || !str_ends_with($filename, '.jpg')) {
                continue;
            }

            $suffix = (int) str_replace([$slug.'_', '.jpg'], '', $filename);
            if ($suffix >= $newestSuffix) {
                $newestSuffix = $suffix;
                $newest = $path;
            }
        }

        return $newest;
    }

    private function removeDeprecatedPathologies(): void
    {
        Pathology::where('slug', 'cp')->delete();
    }
}
