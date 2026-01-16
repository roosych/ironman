<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\RaceType;
use App\Http\Controllers\Controller;
use App\Models\RaceResult;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    use ApiResponse;

    private const ALLOWED_DISCIPLINES = ['swim', 'bike', 'run', 'total'];

    /**
     * Get rankings by race type and discipline.
     */
    public function index(Request $request): JsonResponse
    {
        $raceType = $request->query('race_type');
        $discipline = $request->query('discipline');

        // Validate race_type
        $raceTypeEnum = $this->validateRaceType($raceType);
        if ($raceTypeEnum === null) {
            return $this->errorResponse([
                'race_type' => ['Неверный тип гонки. Допустимые: ironman, ironman_70_3, 5150'],
            ], 422);
        }

        // Validate discipline
        if (!in_array($discipline, self::ALLOWED_DISCIPLINES, true)) {
            return $this->errorResponse([
                'discipline' => ['Неверная дисциплина. Допустимые: swim, bike, run, total'],
            ], 422);
        }

        $rankings = $this->getRankings($raceTypeEnum, $discipline);

        return $this->successResponse([
            'data' => $rankings,
            'meta' => [
                'race_type' => $raceType,
                'discipline' => $discipline,
                'total' => count($rankings),
            ],
        ]);
    }

    /**
     * Validate and convert race type string to enum.
     */
    private function validateRaceType(?string $raceType): ?RaceType
    {
        if ($raceType === null) {
            return null;
        }

        return match ($raceType) {
            'ironman' => RaceType::Ironman,
            'ironman_70_3' => RaceType::Ironman703,
            '5150' => RaceType::Sprint5150,
            default => null,
        };
    }

    /**
     * Get rankings for a specific race type and discipline.
     */
    private function getRankings(RaceType $raceType, string $discipline): array
    {
        $disciplineColumn = $discipline . '_time';

        // Get best time per athlete for the discipline
        $results = DB::table('race_results')
            ->join('user_profiles', 'race_results.user_profile_id', '=', 'user_profiles.id')
            ->leftJoin('users', 'user_profiles.user_id', '=', 'users.id')
            ->leftJoin('user_photos', function ($join) {
                $join->on('users.id', '=', 'user_photos.user_id')
                    ->where('user_photos.is_avatar', '=', true);
            })
            ->select([
                'user_profiles.id as athlete_id',
                DB::raw('COALESCE(users.name, user_profiles.admin_full_name) as name'),
                'user_photos.path as avatar_path',
                DB::raw("MIN({$disciplineColumn}) as best_time"),
            ])
            ->where('race_results.race_type', $raceType->value)
            ->where($disciplineColumn, '>', 0)
            ->where('user_profiles.role', 'athlete')
            ->groupBy('user_profiles.id', 'users.name', 'user_profiles.admin_full_name', 'user_photos.path')
            ->orderBy('best_time')
            ->get();

        // Format results with position and get race details for best time
        $rankings = [];
        $position = 1;

        foreach ($results as $result) {
            // Get the race details for this best time
            $raceDetails = RaceResult::where('user_profile_id', $result->athlete_id)
                ->where('race_type', $raceType)
                ->where($disciplineColumn, $result->best_time)
                ->first();

            $rankings[] = [
                'position' => $position,
                'athlete_id' => $result->athlete_id,
                'name' => $result->name,
                'avatar' => $result->avatar_path
                    ? url('storage/' . $result->avatar_path)
                    : null,
                'time' => RaceResult::formatTime($result->best_time),
                'seconds' => $result->best_time,
                'race_date' => $raceDetails?->race_date->format('Y-m-d'),
                'location' => $raceDetails?->location,
            ];

            $position++;
        }

        return $rankings;
    }
}
