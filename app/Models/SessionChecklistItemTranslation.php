<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionChecklistItemTranslation extends Model
{
    protected $fillable = [
        'session_checklist_item_id',
        'locale',
        'text',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(SessionChecklistItem::class, 'session_checklist_item_id');
    }
}
