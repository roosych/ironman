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
        'race_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userProfile(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }
}
