<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RaceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'location',
        'type',
        'country_iso',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_active' => 'boolean',
            'type' => RaceType::class,
        ];
    }

    /**
     * Связь с предстоящими гонками пользователей.
     */
    public function upcomingRaces(): HasMany
    {
        return $this->hasMany(UpcomingRace::class);
    }
}
