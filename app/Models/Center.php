<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Center extends Model
{
    protected $fillable = [
        'name',
        'location',
        'address',
        'phone',
        'email',
        'contact_name',
        'instagram_url',
        'youtube_url',
        'facebook_url',
        'x_url',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function primaryUsers(): HasMany
    {
        return $this->hasMany(User::class)->whereNull('parent_user_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }
}
