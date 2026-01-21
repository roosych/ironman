<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\RaceResultStatisticsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\UserProfile
 */
class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->relationLoaded('user') ? $this->user : null;

        // Вычисляем статистику если загружены результаты гонок
        $stats = null;
        if ($this->relationLoaded('raceResults')) {
            $statisticsService = app(RaceResultStatisticsService::class);
            $stats = $statisticsService->calculatePersonalStats($this->resource);
        }

        // Подсчитываем количество гонок типа Ironman (только одобренные)
        $ironmanRacesCount = 0;
        if ($this->relationLoaded('raceResults')) {
            $ironmanRacesCount = $this->raceResults
                ->where('race_type', \App\Enums\RaceType::Ironman->value)
                ->where('is_approved', true)
                ->count();
        }

        $data = [
            'id' => $this->id,
            'role' => $this->role,
            'ironman_number' => $this->ironman_number,
            'ironman_races_count' => $ironmanRacesCount,
            'bio' => $this->bio,
            'social_links' => $this->social_links ?? [
                'strava' => null,
                'instagram' => null,
                'facebook' => null,
            ],
            'sync_requested' => $this->sync_requested,
            'synced_existing_profile' => $this->synced_existing_profile,
        ];

        // Photos - всегда объект (пустой {} или с данными)
        if ($user && $user->relationLoaded('photos')) {
            $photos = UserPhotoResource::collection($user->photos);
            $data['photos'] = $photos->isEmpty() ? new \stdClass() : $photos;
        } else {
            $data['photos'] = new \stdClass();
        }

        // Race results - только если не исключены и загружены
        if ($this->relationLoaded('raceResults') && ! $request->boolean('exclude_race_results')) {
            $data['race_results'] = RaceResultResource::collection($this->raceResults)
                ->map(fn (RaceResultResource $r) => $r->withoutProfileId());
        }

        // Stats - только если вычислены
        if ($stats !== null) {
            $data['stats'] = $stats;
        }

        return $data;
    }
}
