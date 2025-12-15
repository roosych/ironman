<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\RaceResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RaceResult
 */
class RaceResultResource extends JsonResource
{
    /**
     * Exclude user_id from response (for user-specific endpoints).
     */
    public function withoutUserId(): static
    {
        $this->excludeUserId = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->when(empty($this->excludeUserId), $this->user_id),
            'race_date' => $this->race_date->toDateString(),
            'location' => $this->location,
            'race_type' => $this->race_type->value,
            'race_type_label' => $this->race_type->label(),
            'swim_time' => RaceResult::formatTime($this->swim_time),
            't1_time' => RaceResult::formatTime($this->t1_time),
            'bike_time' => RaceResult::formatTime($this->bike_time),
            't2_time' => RaceResult::formatTime($this->t2_time),
            'run_time' => RaceResult::formatTime($this->run_time),
            'total_time' => RaceResult::formatTime($this->total_time),
            'age_group' => $this->age_group,
            'overall_position' => $this->overall_position,
            'age_group_position' => $this->age_group_position,
            'user' => $this->when(
                $this->relationLoaded('user'),
                fn () => UserResource::make($this->user)
            ),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
