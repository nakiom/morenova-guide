<?php

declare(strict_types=1);

namespace Database\Seeders\Concerns;

use App\Models\Pathology;
use RuntimeException;

trait LoadsPathologyFixture
{
    /**
     * @return array<string, list<mixed>>
     */
    protected function loadPathologyFixture(string $name): array
    {
        $path = database_path("seeders/data/{$name}.json");

        if (!is_file($path)) {
            throw new RuntimeException("Fixture no encontrado: {$path}");
        }

        /** @var array<string, list<mixed>> $data */
        $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        return $data;
    }

    protected function pathologyBySlug(string $slug): ?Pathology
    {
        return Pathology::where('slug', $slug)->first();
    }

    /**
     * @param  class-string  $modelClass
     * @param  list<int>  $keptOrders
     */
    protected function prunePathologyOrders(string $modelClass, int $pathologyId, array $keptOrders): void
    {
        $query = $modelClass::query()->where('pathology_id', $pathologyId);

        if ($keptOrders === []) {
            $query->delete();

            return;
        }

        $query->whereNotIn('order', $keptOrders)->delete();
    }
}
