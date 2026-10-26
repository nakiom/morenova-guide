<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientTag extends Model
{
    public const TYPE_NICKNAME = 'nickname';

    public const TYPE_INTEREST = 'interest';

    public const TYPE_DATA = 'data';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_NICKNAME,
        self::TYPE_INTEREST,
        self::TYPE_DATA,
    ];

    protected $fillable = [
        'patient_id',
        'user_id',
        'type',
        'content',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function typeLabel(string $type): string
    {
        return __('messages.patient_tag_type_'.$type);
    }
}
