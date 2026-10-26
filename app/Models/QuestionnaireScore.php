<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionnaireScore extends Model
{
    public const PHASE_PRE = 'pre';
    public const PHASE_POST = 'post';
    public const PHASE_CONTROL = 'control';

    public const PHASES = [
        self::PHASE_PRE,
        self::PHASE_POST,
        self::PHASE_CONTROL,
    ];

    protected $fillable = [
        'patient_id',
        'questionnaire_id',
        'user_id',
        'phase',
        'score',
        'responses',
        'scored_at',
        'notes',
        'patient_message',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'responses' => 'array',
            'scored_at' => 'date',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
