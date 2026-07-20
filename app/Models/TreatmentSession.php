<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreatmentSession extends Model
{
    protected $fillable = [
        'patient_id',
        'session_number',
        'session_date',
        'shocks',
        'energy_kv',
        'frequency_hz',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'session_date' => 'date',
            'session_number' => 'integer',
            'shocks' => 'integer',
            'energy_kv' => 'decimal:2',
            'frequency_hz' => 'decimal:2',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * True si energía o frecuencia difieren del protocolo actual del paciente.
     */
    public function differsFromProtocol(?Pathology $pathology = null): bool
    {
        $pathology ??= $this->patient?->pathology;
        if (! $pathology) {
            return false;
        }

        return $this->energyDiffersFrom($pathology) || $this->frequencyDiffersFrom($pathology);
    }

    public function energyDiffersFrom(?Pathology $pathology): bool
    {
        if (! $pathology || $pathology->energy_kv === null || $this->energy_kv === null) {
            return false;
        }

        return abs((float) $this->energy_kv - (float) $pathology->energy_kv) > 0.001;
    }

    public function frequencyDiffersFrom(?Pathology $pathology): bool
    {
        if (! $pathology || $pathology->frequency_hz === null || $this->frequency_hz === null) {
            return false;
        }

        return abs((float) $this->frequency_hz - (float) $pathology->frequency_hz) > 0.001;
    }
}
