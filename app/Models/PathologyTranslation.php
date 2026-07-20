<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PathologyTranslation extends Model
{
    protected $fillable = [
        'pathology_id',
        'locale',
        'name',
        'description',
    ];

    public function pathology(): BelongsTo
    {
        return $this->belongsTo(Pathology::class);
    }
}
