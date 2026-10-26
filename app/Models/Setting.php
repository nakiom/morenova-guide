<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $all = self::cached();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function setValue(string $key, mixed $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => is_string($value) || $value === null ? $value : json_encode($value)]
        );

        Cache::forget('app.settings');
    }

    /**
     * @param  array<string, mixed>  $pairs
     */
    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $key => $value) {
            self::updateOrCreate(
                ['key' => $key],
                ['value' => is_string($value) || $value === null ? $value : json_encode($value)]
            );
        }

        Cache::forget('app.settings');
    }

    /**
     * @return array<string, mixed>
     */
    public static function cached(): array
    {
        return Cache::remember('app.settings', 300, function () {
            return self::query()->pluck('value', 'key')->all();
        });
    }
}
