<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SessionChecklistItem;
use App\Models\SessionChecklistItemTranslation;
use Database\Seeders\Concerns\LoadsPathologyFixture;
use Illuminate\Database\Seeder;

class SessionChecklistSeeder extends Seeder
{
    use LoadsPathologyFixture;

    public function run(): void
    {
        foreach ($this->loadPathologyFixture('checklist') as $slug => $items) {
            $pathology = $this->pathologyBySlug($slug);
            if (!$pathology) {
                continue;
            }

            $keptOrders = [];

            foreach ($items as $order => $texts) {
                $itemOrder = $order + 1;
                $keptOrders[] = $itemOrder;

                $item = SessionChecklistItem::updateOrCreate(
                    ['pathology_id' => $pathology->id, 'order' => $itemOrder]
                );

                foreach ($texts as $locale => $text) {
                    SessionChecklistItemTranslation::updateOrCreate(
                        ['session_checklist_item_id' => $item->id, 'locale' => $locale],
                        ['text' => $text]
                    );
                }
            }

            $this->prunePathologyOrders(SessionChecklistItem::class, $pathology->id, $keptOrders);
        }
    }
}
