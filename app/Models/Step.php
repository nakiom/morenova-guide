<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Step extends Model
{
    protected $fillable = [
        'pathology_id',
        'order',
        'image_path',
    ];

    public function pathology(): BelongsTo
    {
        return $this->belongsTo(Pathology::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(StepTranslation::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }

    public function translation(string $locale = null): ?StepTranslation
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }
}
