<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UpcomingRaceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'race_type' => $this->race?->type?->value,
            'race_type_label' => $this->race?->type?->label(),
            'location' => $this->race?->location,
            'country_iso' => $this->race?->country_iso,
            'race_date' => $this->race?->date?->toISOString(),
            'is_active' => $this->is_active,
            'created_by' => [
                'id' => $this->userProfile?->id,
                'name' => $this->userProfile?->user?->name ?? $this->userProfile?->admin_full_name,
                'avatar' => $this->userProfile?->user?->avatar?->url,
            ],
        ];
    }
}
