<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\FollowUpItem;
use App\Models\FollowUpItemTranslation;
use Database\Seeders\Concerns\LoadsPathologyFixture;
use Illuminate\Database\Seeder;

class FollowUpSeeder extends Seeder
{
    use LoadsPathologyFixture;

    public function run(): void
    {
        foreach ($this->loadPathologyFixture('follow_ups') as $slug => $items) {
            $pathology = $this->pathologyBySlug($slug);
            if (!$pathology) {
                continue;
            }

            $keptOrders = [];

            foreach ($items as $order => $translations) {
                $itemOrder = $order + 1;
                $keptOrders[] = $itemOrder;

                $item = FollowUpItem::updateOrCreate(
                    ['pathology_id' => $pathology->id, 'order' => $itemOrder]
                );

                foreach ($translations as $locale => $data) {
                    FollowUpItemTranslation::updateOrCreate(
                        ['follow_up_item_id' => $item->id, 'locale' => $locale],
                        [
                            'title' => $data['title'],
                            'description' => $data['description'],
                        ]
                    );
                }
            }

            $this->prunePathologyOrders(FollowUpItem::class, $pathology->id, $keptOrders);
        }
    }
}
