<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\InclusionCriteria;
use App\Models\InclusionCriteriaTranslation;
use Database\Seeders\Concerns\LoadsPathologyFixture;
use Illuminate\Database\Seeder;

class InclusionCriteriaSeeder extends Seeder
{
    use LoadsPathologyFixture;

    public function run(): void
    {
        foreach ($this->loadPathologyFixture('inclusion') as $slug => $items) {
            $pathology = $this->pathologyBySlug($slug);
            if (!$pathology) {
                continue;
            }

            $keptOrders = [];

            foreach ($items as $order => $texts) {
                $itemOrder = $order + 1;
                $keptOrders[] = $itemOrder;

                $item = InclusionCriteria::updateOrCreate(
                    ['pathology_id' => $pathology->id, 'order' => $itemOrder]
                );

                foreach ($texts as $locale => $text) {
                    InclusionCriteriaTranslation::updateOrCreate(
                        ['inclusion_criteria_id' => $item->id, 'locale' => $locale],
                        ['text' => $text]
                    );
                }
            }

            $this->prunePathologyOrders(InclusionCriteria::class, $pathology->id, $keptOrders);
        }
    }
}
