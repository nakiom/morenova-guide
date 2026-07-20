<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'user_id',
        'pathology_id',
        'name',
        'external_id',
        'age',
        'email',
        'phones',
        'notes',
        'expected_next_visit_at',
        'sessions_count',
        'total_shocks',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'phones' => 'array',
            'expected_next_visit_at' => 'date',
            'sessions_count' => 'integer',
            'total_shocks' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pathology(): BelongsTo
    {
        return $this->belongsTo(Pathology::class);
    }

    public function treatmentSessions(): HasMany
    {
        return $this->hasMany(TreatmentSession::class)->orderBy('session_number');
    }

    public function questionnaireScores(): HasMany
    {
        return $this->hasMany(QuestionnaireScore::class)->orderBy('scored_at');
    }

    /**
     * Admin: todos. Responsable de centro: pacientes del centro. Resto: solo propios.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->center_id && ! $user->isSubUser()) {
            return $query->whereHas('user', fn (Builder $q) => $q->where('center_id', $user->center_id));
        }

        return $query->where('user_id', $user->id);
    }

    public function scopeWithUpcomingVisit(Builder $query): Builder
    {
        return $query->whereNotNull('expected_next_visit_at');
    }

    public function recalculateSessionCounters(): void
    {
        $this->update([
            'sessions_count' => $this->treatmentSessions()->count(),
            'total_shocks' => (int) $this->treatmentSessions()->sum('shocks'),
        ]);
    }

    /**
     * Reasigna session_number 1..N por fecha (y id), sin huecos.
     */
    public function renumberSessions(): void
    {
        $sessions = $this->treatmentSessions()
            ->reorder()
            ->orderBy('session_date')
            ->orderBy('id')
            ->get();

        foreach ($sessions as $index => $session) {
            $number = $index + 1;
            if ((int) $session->session_number !== $number) {
                $session->update(['session_number' => $number]);
            }
        }

        $this->recalculateSessionCounters();
    }

    public function nextSessionNumber(): int
    {
        return ((int) $this->sessions_count) + 1;
    }

    /**
     * Identificador anónimo para reportes (sin datos personales).
     */
    public function anonymousCode(): string
    {
        if ($this->external_id) {
            return (string) $this->external_id;
        }

        return 'P-'.str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @return list<string>
     */
    public function phoneList(): array
    {
        $phones = $this->phones ?? [];

        return array_values(array_filter(
            array_map(static fn ($p) => trim((string) $p), is_array($phones) ? $phones : []),
            static fn (string $p) => $p !== ''
        ));
    }

    public function phonesDisplay(string $separator = ' · '): string
    {
        $phones = $this->phoneList();

        return $phones === [] ? '—' : implode($separator, $phones);
    }

    /**
     * @param  array<int, mixed>|null  $phones
     * @return list<string>
     */
    public static function normalizePhones(?array $phones): array
    {
        if ($phones === null) {
            return [];
        }

        return array_values(array_filter(
            array_map(static fn ($p) => trim((string) $p), $phones),
            static fn (string $p) => $p !== ''
        ));
    }

    public function belongsToSameCenter(User $user): bool
    {
        if (! $user->center_id) {
            return false;
        }

        $this->loadMissing('user');

        return $this->user?->center_id === $user->center_id;
    }
}
