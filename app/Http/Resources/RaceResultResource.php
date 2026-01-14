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
     * Exclude user_profile_id from response (for user-specific endpoints).
     */
    public function withoutProfileId(): static
    {
        $this->excludeProfileId = true;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Получаем имя из профиля или пользователя
        $name = null;
        if ($this->relationLoaded('profile') && $this->profile) {
            if ($this->profile->relationLoaded('user') && $this->profile->user) {
                $name = $this->profile->user->name;
            } else {
                $name = $this->profile->admin_full_name;
            }
        }

        return [
            'id' => $this->id,
            'user_profile_id' => $this->when(empty($this->excludeProfileId), $this->user_profile_id),
            'name' => $name,
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
            'age_group' => $this->when($this->age_group !== null, $this->age_group),
            'overall_position' => $this->when($this->overall_position !== null, $this->overall_position),
            'age_group_position' => $this->when($this->age_group_position !== null, $this->age_group_position),
            // 'created_at' => $this->created_at->toISOString(),
        ];
    }
}
