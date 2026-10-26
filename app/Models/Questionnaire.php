<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questionnaire extends Model
{
    protected $fillable = [
        'pathology_id',
        'code',
        'name',
        'description',
        'max_score',
        'schema',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_score' => 'decimal:2',
            'schema' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function pathology(): BelongsTo
    {
        return $this->belongsTo(Pathology::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(QuestionnaireScore::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function fields(): array
    {
        $fields = $this->schema['fields'] ?? [];

        return is_array($fields) ? array_values($fields) : [];
    }

    public function scoreMode(): string
    {
        return (string) ($this->schema['score_mode'] ?? 'manual');
    }

    public function fieldLabel(array $field, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $label = $field['label'] ?? '';

        if (is_array($label)) {
            return (string) ($label[$locale] ?? $label['es'] ?? $label['en'] ?? reset($label) ?: '');
        }

        return (string) $label;
    }

    public function optionLabel(array $option, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        $label = $option['label'] ?? $option['value'] ?? '';

        if (is_array($label)) {
            return (string) ($label[$locale] ?? $label['es'] ?? $label['en'] ?? reset($label) ?: '');
        }

        return (string) $label;
    }

    /**
     * @param  array<string, mixed>  $responses
     */
    public function computeScore(array $responses): ?float
    {
        $mode = $this->scoreMode();
        $fields = $this->fields();

        if ($mode === 'manual') {
            if (isset($responses['_score']) && is_numeric($responses['_score'])) {
                return (float) $responses['_score'];
            }

            // Diario sexual: usar encuentros satisfactorios como valor de seguimiento.
            if (isset($responses['satisfying']) && is_numeric($responses['satisfying'])) {
                return (float) $responses['satisfying'];
            }

            return null;
        }

        $values = [];
        foreach ($fields as $field) {
            $key = (string) ($field['key'] ?? '');
            if ($key === '' || ($field['score'] ?? true) === false) {
                continue;
            }
            if (!isset($responses[$key]) || !is_numeric($responses[$key])) {
                continue;
            }
            $values[] = (float) $responses[$key];
        }

        if ($values === []) {
            return null;
        }

        if ($mode === 'percent') {
            return $this->percentIndex($fields, $responses);
        }

        return match ($mode) {
            'average' => array_sum($values) / count($values),
            'single' => $values[0],
            default => array_sum($values),
        };
    }

    /**
     * 0–100 a partir del rango de cada ítem: ((valor − mín) / (máx − mín)), promediado.
     * QuickDASH (1–5), FFI (0–10) y NDI (0–5) usan esta escala.
     *
     * @param  list<array<string, mixed>>  $fields
     * @param  array<string, mixed>  $responses
     */
    private function percentIndex(array $fields, array $responses): ?float
    {
        $normalized = 0.0;
        $count = 0;

        foreach ($fields as $field) {
            if (($field['score'] ?? true) === false) {
                continue;
            }

            $key = (string) ($field['key'] ?? '');
            if ($key === '' || ! isset($responses[$key]) || ! is_numeric($responses[$key])) {
                continue;
            }

            $min = (float) ($field['min'] ?? 0);
            $max = (float) ($field['max'] ?? 0);
            $span = $max - $min;
            if ($span <= 0) {
                continue;
            }

            $normalized += ((float) $responses[$key] - $min) / $span;
            $count++;
        }

        if ($count === 0) {
            return null;
        }

        return round(($normalized / $count) * 100, 2);
    }

    /**
     * Whether a higher raw score means clinical improvement.
     * Pain/symptom scales usually improve when the score goes down.
     */
    public function higherIsBetter(): bool
    {
        $schema = $this->schema;
        if (is_array($schema) && array_key_exists('higher_is_better', $schema)) {
            return (bool) $schema['higher_is_better'];
        }

        $code = strtolower((string) $this->code);

        if (str_contains($code, 'satisfaction')) {
            return true;
        }

        $lowerBetterPrefixes = [
            'nih_cpsi',
            'ipss',
            'pd_vas',
            'pd_curvature',
            'pdq',
            'iciq',
            'iiq',
            'udi',
            'pad_test',
            'cotton_swab',
            'vas_pain',
            'vas_gsm',
            'vas_',
            'quickdash',
            'ffi',
            'ndi',
            'womac',
            'bpi',
        ];

        foreach ($lowerBetterPrefixes as $prefix) {
            if ($code === rtrim($prefix, '_') || str_starts_with($code, $prefix)) {
                return false;
            }
        }

        return true;
    }

    public function isImprovement(float $deltaPostMinusPre): bool
    {
        if (abs($deltaPostMinusPre) < 0.00001) {
            return false;
        }

        return $this->higherIsBetter()
            ? $deltaPostMinusPre > 0
            : $deltaPostMinusPre < 0;
    }

    /**
     * Signed delta where positive always means improvement.
     */
    public function signedImprovement(float $deltaPostMinusPre): float
    {
        return $this->higherIsBetter() ? $deltaPostMinusPre : -$deltaPostMinusPre;
    }
}
