<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'center_id',
        'parent_user_id',
        'phone',
        'location',
        'job_title',
        'photo_path',
        'photo_url',
        'is_active',
        'email_verified_at',
        'must_change_password',
        'privacy_accepted_at',
        'preferred_locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'privacy_accepted_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_active' => 'boolean',
            'must_change_password' => 'boolean',
        ];
    }

    public function needsOnboarding(): bool
    {
        return $this->must_change_password || $this->privacy_accepted_at === null;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isSubUser(): bool
    {
        return $this->parent_user_id !== null;
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function subUsers(): HasMany
    {
        return $this->hasMany(User::class, 'parent_user_id');
    }

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function photoUrl(): ?string
    {
        if ($this->photo_url) {
            return $this->photo_url;
        }

        return $this->photo_path
            ? url(Storage::disk('public')->url($this->photo_path))
            : null;
    }
}
