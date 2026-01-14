<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'external_id',
        'user_id',
        'admin_full_name',
        'role',
        'ironman_number',
        'bio',
        'social_links',
        'sync_requested',
        'synced_existing_profile',
    ];

    protected $casts = [
        'ironman_number' => 'integer',
        'social_links' => 'array',
        'sync_requested' => 'boolean',
        'synced_existing_profile' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    public function isAthlete(): bool
    {
        return $this->role === 'athlete';
    }

    public function isCoach(): bool
    {
        return $this->role === 'coach';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
