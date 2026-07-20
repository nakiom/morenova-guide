<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pathology extends Model
{
    protected $fillable = [
        'category_id',
        'slug',
        'icon',
        'background_image',
        'order',
        'transducer',
        'sessions_count',
        'sessions_frequency',
        'session_duration_minutes',
        'applications_per_session',
        'shots_per_application',
        'shots_per_session',
        'energy_kv',
        'frequency_hz',
    ];

    protected $casts = [
        'session_duration_minutes' => 'integer',
        'applications_per_session' => 'integer',
        'shots_per_application' => 'integer',
        'shots_per_session' => 'integer',
        'energy_kv' => 'decimal:2',
        'frequency_hz' => 'decimal:2',
    ];

    public function hasTreatmentParameters(): bool
    {
        return $this->transducer
            || $this->sessions_count
            || $this->sessions_frequency
            || $this->session_duration_minutes
            || $this->applications_per_session
            || $this->shots_per_application
            || $this->shots_per_session
            || $this->energy_kv
            || $this->frequency_hz;
    }

    /**
     * Image for this pathology's transducer under public/storage/transducers
     * (filenames: Model-Pathologies.png).
     */
    public function transducerImageUrl(): ?string
    {
        if (!$this->transducer) {
            return null;
        }

        $dir = public_path('storage/transducers');
        if (!is_dir($dir)) {
            return null;
        }

        $model = trim($this->transducer);
        $files = scandir($dir) ?: [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            // Match "Li50F-...", "C15-PD.png", "C30- CPPS-..." (optional space after dash)
            if (preg_match('/^'.preg_quote($model, '/').'\s*-/i', $file)) {
                return asset('storage/transducers/'.$file);
            }
        }

        return null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PathologyTranslation::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class)->orderBy('order');
    }

    public function inclusionCriteria(): HasMany
    {
        return $this->hasMany(InclusionCriteria::class)->orderBy('order');
    }

    public function exclusionCriteria(): HasMany
    {
        return $this->hasMany(ExclusionCriteria::class)->orderBy('order');
    }

    public function sessionChecklistItems(): HasMany
    {
        return $this->hasMany(SessionChecklistItem::class)->orderBy('order');
    }

    public function followUpItems(): HasMany
    {
        return $this->hasMany(FollowUpItem::class)->orderBy('order');
    }

    public function applicationAreas(): HasMany
    {
        return $this->hasMany(ApplicationArea::class)->orderBy('order');
    }

    public function questionnaires(): HasMany
    {
        return $this->hasMany(Questionnaire::class);
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    /**
     * PNG icon under public/img/patologies/{fem|men}/ by slug.
     */
    public function iconAssetUrl(): ?string
    {
        $this->loadMissing('category');

        $folder = match ($this->category?->slug) {
            'womens-health' => 'fem',
            'mens-health' => 'men',
            default => null,
        };

        if (!$folder) {
            return null;
        }

        $aliases = [
            'sui' => 'siu',
            'cpp' => 'cpps',
        ];

        $filename = $aliases[$this->slug] ?? $this->slug;
        $relative = "img/patologies/{$folder}/{$filename}.png";

        if (!is_file(public_path($relative))) {
            return null;
        }

        return asset($relative);
    }

    public function translation(?string $locale = null): ?PathologyTranslation
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations()->where('locale', $locale)->first();
    }
}
