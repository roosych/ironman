<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'code',
        'code_used',
    ];

    protected $casts = [
        'ironman_number' => 'integer',
        'social_links' => 'array',
        'code_used' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    public function upcomingRaces(): HasMany
    {
        return $this->hasMany(UpcomingRace::class);
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

    /**
     * Hide profiles, связанные с тестовым пользователем-ревьюером.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('hide_reviewer_profiles', function (Builder $query) {
            // Показываем профили без привязанного пользователя (seed данные) и скрываем только те,
            // что связаны с ревьюером.
            $query->where(function (Builder $q) {
                $q->whereNull('user_id')
                    ->orWhereHas('user', fn (Builder $u) => $u->where('is_reviewer', false));
            });
        });
    }
}
