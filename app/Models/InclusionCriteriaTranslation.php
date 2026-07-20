<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InclusionCriteriaTranslation extends Model
{
    protected $table = 'inclusion_criteria_translations';

    protected $fillable = [
        'inclusion_criteria_id',
        'locale',
        'text',
    ];

    public function inclusionCriteria(): BelongsTo
    {
        return $this->belongsTo(InclusionCriteria::class);
    }
}
