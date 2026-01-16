<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RaceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpcomingRace extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_profile_id',
        'race_type',
        'location',
        'race_date',
        'is_active',
    ];

    protected $casts = [
        'race_type' => RaceType::class,
        'race_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }
}
