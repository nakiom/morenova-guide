<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepTranslation extends Model
{
    protected $fillable = [
        'step_id',
        'locale',
        'title',
        'description',
    ];

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}
