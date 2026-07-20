<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suggestion extends Model
{
    protected $fillable = [
        'step_id',
        'pathology_id',
        'user_name',
        'user_email',
        'doctor_name',
        'treatment',
        'message',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    public function pathology(): BelongsTo
    {
        return $this->belongsTo(Pathology::class);
    }
}
