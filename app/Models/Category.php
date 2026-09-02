<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Category extends Model
{
    protected $fillable = [
        'slug',
        'icon',
        'background_image',
        'order',
    ];

    protected static function booted(): void
    {
        static::saved(static fn () => Cache::forget('home.categories'));
        static::deleted(static fn () => Cache::forget('home.categories'));
    }

    public function pathologies(): HasMany
    {
        return $this->hasMany(Pathology::class)->orderBy('order');
    }
}
