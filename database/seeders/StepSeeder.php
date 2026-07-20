<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Step;
use App\Models\StepTranslation;
use Database\Seeders\Concerns\LoadsPathologyFixture;
use Illuminate\Database\Seeder;

class StepSeeder extends Seeder
{
    use LoadsPathologyFixture;

    public function run(): void
    {
        foreach ($this->loadPathologyFixture('steps') as $slug => $items) {
            $pathology = $this->pathologyBySlug($slug);
            if (!$pathology) {
                continue;
            }

            $keptOrders = [];

            foreach ($items as $order => $translations) {
                $itemOrder = $order + 1;
                $keptOrders[] = $itemOrder;

                $step = Step::updateOrCreate(
                    ['pathology_id' => $pathology->id, 'order' => $itemOrder]
                );

                foreach ($translations as $locale => $data) {
                    StepTranslation::updateOrCreate(
                        ['step_id' => $step->id, 'locale' => $locale],
                        [
                            'title' => $data['title'],
                            'description' => $data['description'],
                        ]
                    );
                }
            }

            $this->prunePathologyOrders(Step::class, $pathology->id, $keptOrders);
        }
    }
}
