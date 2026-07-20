<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ApplicationArea;
use App\Models\ApplicationAreaTranslation;
use Database\Seeders\Concerns\LoadsPathologyFixture;
use Illuminate\Database\Seeder;

class ApplicationAreaSeeder extends Seeder
{
    use LoadsPathologyFixture;

    public function run(): void
    {
        foreach ($this->loadPathologyFixture('application_areas') as $slug => $items) {
            $pathology = $this->pathologyBySlug($slug);
            if (!$pathology) {
                continue;
            }

            $keptOrders = [];

            foreach ($items as $order => $translations) {
                $itemOrder = $order + 1;
                $keptOrders[] = $itemOrder;

                $area = ApplicationArea::updateOrCreate(
                    ['pathology_id' => $pathology->id, 'order' => $itemOrder]
                );

                foreach ($translations as $locale => $data) {
                    ApplicationAreaTranslation::updateOrCreate(
                        ['application_area_id' => $area->id, 'locale' => $locale],
                        [
                            'title' => $data['title'],
                            'description' => $data['description'] ?? '',
                        ]
                    );
                }
            }

            $this->prunePathologyOrders(ApplicationArea::class, $pathology->id, $keptOrders);
        }
    }
}
