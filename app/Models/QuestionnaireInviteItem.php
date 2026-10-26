<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionnaireInviteItem extends Model
{
    protected $fillable = [
        'questionnaire_invite_id',
        'questionnaire_id',
        'questionnaire_score_id',
        'order',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
            'completed_at' => 'datetime',
        ];
    }

    public function invite(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireInvite::class, 'questionnaire_invite_id');
    }

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function score(): BelongsTo
    {
        return $this->belongsTo(QuestionnaireScore::class, 'questionnaire_score_id');
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }
}
