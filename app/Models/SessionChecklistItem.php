<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionChecklistItem extends Model
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
        return $this->hasMany(SessionChecklistItemTranslation::class);
    }

    public function translation(?string $locale = null): ?SessionChecklistItemTranslation
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations()->where('locale', $locale)->first();
    }
}
