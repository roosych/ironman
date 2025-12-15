<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\RaceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RaceResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'race_date',
        'location',
        'race_type',
        'swim_time',
        't1_time',
        'bike_time',
        't2_time',
        'run_time',
        'total_time',
        'age_group',
        'overall_position',
        'age_group_position',
    ];

    protected $casts = [
        'race_date' => 'date',
        'race_type' => RaceType::class,
        'swim_time' => 'integer',
        't1_time' => 'integer',
        'bike_time' => 'integer',
        't2_time' => 'integer',
        'run_time' => 'integer',
        'total_time' => 'integer',
        'overall_position' => 'integer',
        'age_group_position' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function formatTime(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
    }
}
