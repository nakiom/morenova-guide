<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Center extends Model
{
    protected $fillable = [
        'name',
        'logo_path',
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

    public function logoUrl(): ?string
    {
        if (! $this->logo_path || ! Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function logoAbsolutePath(): ?string
    {
        if (! $this->logo_path || ! Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        $path = Storage::disk('public')->path($this->logo_path);

        return is_file($path) ? $path : null;
    }

    /**
     * @return list<array{label: string, url: string}>
     */
    public function socialLinks(): array
    {
        $links = [
            ['label' => 'Instagram', 'url' => $this->instagram_url],
            ['label' => 'YouTube', 'url' => $this->youtube_url],
            ['label' => 'Facebook', 'url' => $this->facebook_url],
            ['label' => 'X', 'url' => $this->x_url],
        ];

        return array_values(array_filter(
            $links,
            static fn (array $link): bool => filled($link['url'])
        ));
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
