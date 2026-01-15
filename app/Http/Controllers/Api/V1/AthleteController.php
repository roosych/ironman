<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\RaceType;
use App\Http\Controllers\Controller;
use App\Models\RaceResult;
use App\Models\UserProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AthleteController extends Controller
{
    use ApiResponse;

    /**
     * Get list of athletes with avatar and race counts by type.
     */
    public function index(): JsonResponse
    {
        $profiles = UserProfile::with([
            'user.avatar',
            'raceResults:id,user_profile_id,race_type',
        ])
            ->where('role', 'athlete')
            ->get();

        $data = $profiles->map(fn (UserProfile $profile) => $this->formatAthlete($profile));

        return $this->successResponse(['data' => $data]);
    }

    /**
     * Get a single athlete by ID.
     */
    public function show(int $id): JsonResponse
    {
        $profile = UserProfile::with([
            'user.avatar',
            'raceResults:id,user_profile_id,race_type',
        ])
            ->where('id', $id)
            ->where('role', 'athlete')
            ->first();

        if (!$profile) {
            return $this->errorResponse(['athlete' => ['Атлет не найден.']], 404);
        }

        return $this->successResponse(['data' => $this->formatAthlete($profile, detailed: true)]);
    }

    /**
     * Format athlete data for response.
     */
    private function formatAthlete(UserProfile $profile, bool $detailed = false): array
    {
        $counts = $profile->raceResults
            ->groupBy(fn ($result) => $result->race_type->value)
            ->map(fn ($items) => $items->count());

        $data = [
            'id' => $profile->id,
            'name' => $profile->user?->name ?? $profile->admin_full_name,
            'avatar' => $profile->user?->avatar?->url,
            'race_counts' => [
                'ironman' => $counts->get('ironman', 0),
                'ironman_70_3' => $counts->get('ironman_70_3', 0),
                '5150' => $counts->get('5150', 0),
            ],
        ];

        if ($detailed) {
            $data['ironman_number'] = $profile->ironman_number;
            $data['bio'] = $profile->bio;
            $data['social_links'] = $profile->social_links;
            $data['ranking'] = $this->getAthleteRanking($profile->id);
        }

        return $data;
    }

    /**
     * Get athlete ranking by best time for each race type.
     */
    private function getAthleteRanking(int $profileId): array
    {
        $ranking = [];

        foreach ([RaceType::Ironman, RaceType::Ironman703] as $raceType) {
            $ranking[$raceType->value] = $this->getRankingForRaceType($profileId, $raceType);
        }

        return $ranking;
    }

    /**
     * Get athlete's position for a specific race type based on best time.
     */
    private function getRankingForRaceType(int $profileId, RaceType $raceType): ?array
    {
        // Get athlete's best time for this race type (only complete results)
        $athleteBestTime = RaceResult::where('user_profile_id', $profileId)
            ->where('race_type', $raceType)
            ->where(fn ($q) => $this->applyCompleteResultsFilter($q))
            ->min('total_time');

        if ($athleteBestTime === null) {
            return null;
        }

        // Base query for complete results
        $baseQuery = fn () => DB::table('race_results')
            ->where('race_type', $raceType->value)
            ->where('swim_time', '>', 0)
            ->where('t1_time', '>', 0)
            ->where('bike_time', '>', 0)
            ->where('t2_time', '>', 0)
            ->where('run_time', '>', 0)
            ->where('total_time', '>', 0);

        // Count athletes with better (lower) best time
        $betterAthletes = $baseQuery()
            ->select('user_profile_id')
            ->groupBy('user_profile_id')
            ->havingRaw('MIN(total_time) < ?', [$athleteBestTime])
            ->count();

        // Count total athletes with complete results in this race type
        $totalAthletes = $baseQuery()
            ->distinct('user_profile_id')
            ->count('user_profile_id');

        return [
            'position' => $betterAthletes + 1,
            'total' => $totalAthletes,
        ];
    }

    /**
     * Apply filter for complete race results (all times > 0).
     */
    private function applyCompleteResultsFilter($query): void
    {
        $query->where('swim_time', '>', 0)
            ->where('t1_time', '>', 0)
            ->where('bike_time', '>', 0)
            ->where('t2_time', '>', 0)
            ->where('run_time', '>', 0)
            ->where('total_time', '>', 0);
    }

    /**
     * Get athlete's personal records by discipline for each race type.
     */
    public function records(int $id): JsonResponse
    {
        $profile = UserProfile::where('id', $id)
            ->where('role', 'athlete')
            ->first();

        if (!$profile) {
            return $this->errorResponse(['athlete' => ['Атлет не найден.']], 404);
        }

        $records = [];
        $disciplines = ['swim_time', 'bike_time', 'run_time', 'total_time'];

        foreach (RaceType::cases() as $raceType) {
            $raceTypeRecords = [];

            foreach ($disciplines as $discipline) {
                $record = $this->getBestTimeForDiscipline($profile->id, $raceType, $discipline);
                $raceTypeRecords[$this->formatDisciplineName($discipline)] = $record;
            }

            $records[$raceType->value] = $raceTypeRecords;
        }

        return $this->successResponse(['data' => $records]);
    }

    /**
     * Get the best time for a specific discipline in a race type.
     */
    private function getBestTimeForDiscipline(int $profileId, RaceType $raceType, string $discipline): ?array
    {
        $result = RaceResult::where('user_profile_id', $profileId)
            ->where('race_type', $raceType)
            ->where($discipline, '>', 0)
            ->orderBy($discipline)
            ->first();

        if (!$result) {
            return null;
        }

        return [
            'time' => RaceResult::formatTime($result->$discipline),
            'seconds' => $result->$discipline,
            'race_date' => $result->race_date->format('Y-m-d'),
            'location' => $result->location,
        ];
    }

    /**
     * Format discipline name for response (remove _time suffix).
     */
    private function formatDisciplineName(string $discipline): string
    {
        return str_replace('_time', '', $discipline);
    }
}
