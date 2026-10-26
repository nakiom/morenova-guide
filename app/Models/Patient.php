<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Patient extends Model
{
    use SoftDeletes;

    public const ACTIVE_TREATMENT_MONTHS = 3;

    public const SEX_MALE = 'male';

    public const SEX_FEMALE = 'female';

    public const SEX_OTHER = 'other';

    /** @var list<string> */
    public const SEXES = [
        self::SEX_MALE,
        self::SEX_FEMALE,
        self::SEX_OTHER,
    ];

    /** @var list<int> */
    public const REMINDER_DAYS_OPTIONS = [1, 2, 3, 5, 7, 14];

    protected $fillable = [
        'user_id',
        'pathology_id',
        'name',
        'external_id',
        'age',
        'sex',
        'email',
        'phones',
        'notes',
        'expected_next_visit_at',
        'visit_reminder_enabled',
        'visit_reminder_days_before',
        'visit_reminder_sent_for',
        'sessions_count',
        'total_shocks',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'phones' => 'array',
            'expected_next_visit_at' => 'date',
            'visit_reminder_enabled' => 'boolean',
            'visit_reminder_days_before' => 'integer',
            'visit_reminder_sent_for' => 'date',
            'sessions_count' => 'integer',
            'total_shocks' => 'integer',
        ];
    }

    /**
     * Inicio del caso: primera sesión, o la fecha de alta si todavía no hay sesiones.
     */
    public function treatmentStartedAt(): Carbon
    {
        $firstSession = $this->relationLoaded('treatmentSessions')
            ? $this->treatmentSessions->min('session_date')
            : $this->treatmentSessions()->orderBy('session_date')->value('session_date');

        if ($firstSession instanceof \DateTimeInterface) {
            return Carbon::parse($firstSession)->startOfDay();
        }

        if (is_string($firstSession) && $firstSession !== '') {
            return Carbon::parse($firstSession)->startOfDay();
        }

        return $this->created_at->copy()->startOfDay();
    }

    /**
     * Activo durante los 3 meses desde el inicio. Después queda finalizado.
     */
    public function isTreatmentActive(): bool
    {
        return $this->treatmentStartedAt()->greaterThanOrEqualTo(
            now()->subMonths(self::ACTIVE_TREATMENT_MONTHS)->startOfDay()
        );
    }

    public function sexLabel(): ?string
    {
        if (! $this->sex) {
            return null;
        }

        return __('messages.sex_'.$this->sex);
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

    public function questionnaireInvites(): HasMany
    {
        return $this->hasMany(QuestionnaireInvite::class)->latest();
    }

    public function tags(): HasMany
    {
        return $this->hasMany(PatientTag::class)->latest();
    }

    /**
     * Admin: todos. Usuarios de un centro: pacientes compartidos del centro. Sin centro: solo propios.
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->center_id) {
            return $query->whereHas('user', fn (Builder $q) => $q->where('center_id', $user->center_id));
        }

        return $query->where('user_id', $user->id);
    }

    public function scopeWithUpcomingVisit(Builder $query): Builder
    {
        return $query->whereNotNull('expected_next_visit_at');
    }

    public function visitReminderActive(): bool
    {
        return $this->visit_reminder_enabled
            && filled($this->email)
            && $this->expected_next_visit_at !== null
            && $this->visit_reminder_days_before !== null;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function prepareVisitReminderAttributes(array $data, ?Patient $existing = null): array
    {
        $enabled = (bool) ($data['visit_reminder_enabled'] ?? false);
        $daysBefore = $enabled ? ($data['visit_reminder_days_before'] ?? null) : null;
        $newVisit = $data['expected_next_visit_at'] ?? null;

        $attributes = [
            'visit_reminder_enabled' => $enabled,
            'visit_reminder_days_before' => $daysBefore !== null && $daysBefore !== ''
                ? (int) $daysBefore
                : null,
        ];

        if ($existing !== null && $newVisit !== null) {
            $oldVisit = $existing->expected_next_visit_at?->toDateString();
            $newVisitStr = $newVisit instanceof \DateTimeInterface
                ? $newVisit->format('Y-m-d')
                : (string) $newVisit;

            if ($oldVisit !== $newVisitStr) {
                $attributes['visit_reminder_sent_for'] = null;
            }
        }

        if ($existing === null || ! array_key_exists('expected_next_visit_at', $data)) {
            return $attributes;
        }

        if (($data['expected_next_visit_at'] ?? null) === null) {
            $attributes['visit_reminder_sent_for'] = null;
        }

        return $attributes;
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
