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

        return match ($mode) {
            'average' => array_sum($values) / count($values),
            'single' => $values[0],
            default => array_sum($values),
        };
    }
}
