<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\ResultTransferRequest
 */
class ResultTransferRequestResource extends JsonResource
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
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->whenLoaded('user', fn () => $this->user->email),
            ],
            'source_athlete' => [
                'id' => $this->sourceAthlete->id,
                'name' => $this->sourceAthlete->admin_full_name,
                'ironman_number' => $this->sourceAthlete->ironman_number,
                'country_iso' => $this->sourceAthlete->country_iso,
                'race_counts' => $this->when(
                    $this->relationLoaded('sourceAthlete') && $this->sourceAthlete->relationLoaded('raceResults'),
                    function () {
                        $counts = $this->sourceAthlete->raceResults
                            ->groupBy(fn ($result) => $result->race_type->value)
                            ->map(fn ($items) => $items->count());

                        return [
                            'ironman' => $counts->get('ironman', 0),
                            'ironman_70_3' => $counts->get('ironman_70_3', 0),
                            '5150' => $counts->get('5150', 0),
                            'total' => $this->sourceAthlete->raceResults->count(),
                        ];
                    }
                ),
            ],
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'comment' => $this->comment,
            'reviewed_by' => $this->when($this->reviewedBy, [
                'id' => $this->reviewedBy?->id,
                'name' => $this->reviewedBy?->name,
            ]),
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'can_approve' => $this->canBeApproved(),
            'can_reject' => $this->canBeRejected(),
        ];
    }
}