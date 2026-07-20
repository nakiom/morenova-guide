<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ExclusionCriteria;
use App\Models\ExclusionCriteriaTranslation;
use Database\Seeders\Concerns\LoadsPathologyFixture;
use Illuminate\Database\Seeder;

class ExclusionCriteriaSeeder extends Seeder
{
    use LoadsPathologyFixture;

    public function run(): void
    {
        foreach ($this->loadPathologyFixture('exclusion') as $slug => $items) {
            $pathology = $this->pathologyBySlug($slug);
            if (!$pathology) {
                continue;
            }

            $keptOrders = [];

            foreach ($items as $order => $texts) {
                $itemOrder = $order + 1;
                $keptOrders[] = $itemOrder;

                $item = ExclusionCriteria::updateOrCreate(
                    ['pathology_id' => $pathology->id, 'order' => $itemOrder]
                );

                foreach ($texts as $locale => $text) {
                    ExclusionCriteriaTranslation::updateOrCreate(
                        ['exclusion_criteria_id' => $item->id, 'locale' => $locale],
                        ['text' => $text]
                    );
                }
            }

            $this->prunePathologyOrders(ExclusionCriteria::class, $pathology->id, $keptOrders);
        }
    }
}
