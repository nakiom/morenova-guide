<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class QuestionnaireInvite extends Model
{
    public const DEFAULT_EXPIRY_DAYS = 14;

    protected $fillable = [
        'patient_id',
        'user_id',
        'token',
        'phase',
        'questionnaire_ids',
        'email',
        'expires_at',
        'completed_at',
        'visited_at',
        'visit_count',
        'sent_count',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'questionnaire_ids' => 'array',
            'expires_at' => 'datetime',
            'completed_at' => 'datetime',
            'visited_at' => 'datetime',
            'visit_count' => 'integer',
            'sent_count' => 'integer',
            'last_sent_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuestionnaireInviteItem::class)->orderBy('order');
    }

    public function center(): ?Center
    {
        $this->loadMissing('user.center');

        return $this->user?->center;
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function publicUrl(): string
    {
        return url('/q/'.$this->token);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function isOpen(): bool
    {
        return ! $this->isCompleted() && ! $this->isExpired();
    }

    public function nextPendingItem(): ?QuestionnaireInviteItem
    {
        return $this->items()->whereNull('completed_at')->orderBy('order')->first();
    }

    public function pendingCount(): int
    {
        return $this->items()->whereNull('completed_at')->count();
    }

    public function completedCount(): int
    {
        return $this->items()->whereNotNull('completed_at')->count();
    }

    public function markCompletedIfDone(): void
    {
        if ($this->pendingCount() === 0 && ! $this->isCompleted()) {
            $this->update(['completed_at' => now()]);
        }
    }

    public function statusKey(): string
    {
        if ($this->isCompleted()) {
            return 'completed';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if ($this->wasVisited()) {
            return 'visited';
        }

        return 'pending';
    }

    public function wasVisited(): bool
    {
        return $this->visited_at !== null;
    }

    public function markVisited(): void
    {
        if ($this->visited_at === null) {
            $this->forceFill([
                'visited_at' => now(),
                'visit_count' => max(1, (int) $this->visit_count),
            ])->save();

            return;
        }

        $this->increment('visit_count');
    }
}
