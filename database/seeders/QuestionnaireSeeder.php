<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pathology;
use App\Models\Questionnaire;
use Illuminate\Database\Seeder;

class QuestionnaireSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/questionnaires.json');
        /** @var list<array<string, mixed>> $items */
        $items = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $activeCodes = [];

        foreach ($items as $item) {
            $pathology = Pathology::where('slug', $item['pathology'])->first();
            if (!$pathology) {
                continue;
            }

            $activeCodes[] = $item['code'];

            Questionnaire::updateOrCreate(
                ['code' => $item['code']],
                [
                    'pathology_id' => $pathology->id,
                    'name' => $item['name'],
                    'description' => $item['description'] ?? null,
                    'max_score' => $item['max_score'],
                    'schema' => $item['schema'] ?? null,
                    'is_active' => true,
                ]
            );
        }

        // Desactivar instrumentos viejos que ya no están en el catálogo.
        Questionnaire::query()
            ->when($activeCodes !== [], fn ($q) => $q->whereNotIn('code', $activeCodes))
            ->update(['is_active' => false]);
    }
}
