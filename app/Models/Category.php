<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'slug',
        'icon',
        'background_image',
        'order',
    ];

    public function pathologies(): HasMany
    {
        return $this->hasMany(Pathology::class)->orderBy('order');
    }
}
