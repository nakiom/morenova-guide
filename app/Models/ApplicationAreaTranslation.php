<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationAreaTranslation extends Model
{
    protected $fillable = [
        'application_area_id',
        'locale',
        'title',
        'description',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(ApplicationArea::class, 'application_area_id');
    }
}
