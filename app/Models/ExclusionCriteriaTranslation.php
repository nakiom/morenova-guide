<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExclusionCriteriaTranslation extends Model
{
    protected $table = 'exclusion_criteria_translations';

    protected $fillable = [
        'exclusion_criteria_id',
        'locale',
        'text',
    ];

    public function exclusionCriteria(): BelongsTo
    {
        return $this->belongsTo(ExclusionCriteria::class);
    }
}
