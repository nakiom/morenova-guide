<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FollowUpItem extends Model
{
    protected $fillable = [
        'pathology_id',
        'order',
    ];

    public function pathology(): BelongsTo
    {
        return $this->belongsTo(Pathology::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(FollowUpItemTranslation::class);
    }

    public function translation(?string $locale = null): ?FollowUpItemTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()->where('locale', $locale)->first();
    }
}
