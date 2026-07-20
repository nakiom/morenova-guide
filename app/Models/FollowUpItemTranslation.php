<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpItemTranslation extends Model
{
    protected $fillable = [
        'follow_up_item_id',
        'locale',
        'title',
        'description',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(FollowUpItem::class, 'follow_up_item_id');
    }
}
