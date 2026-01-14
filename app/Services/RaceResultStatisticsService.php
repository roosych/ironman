<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\RaceType;
use App\Models\RaceResult;
use App\Models\UserProfile;

class RaceResultStatisticsService
{
    /**
     * Calculate personal statistics for user profile.
     *
     * @return array<string, mixed>
     */
    public function calculatePersonalStats(UserProfile $profile): array
    {
        $raceResults = $profile->raceResults()
            ->orderBy('race_date', 'desc')
            ->get();

        if ($raceResults->isEmpty()) {
            return [
                'summary' => [
                    'total_races' => 0,
                ],
                'personal_bests' => [],
            ];
        }

        return [
            'summary' => $this->calculateSummary($raceResults),
            'personal_bests' => $this->calculatePersonalBests($raceResults),
        ];
    }

    /**
     * Calculate summary statistics.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, RaceResult>  $raceResults
     * @return array<string, mixed>
     */
    private function calculateSummary($raceResults): array
    {
        $totalRaces = $raceResults->count();

        $bestTotalTime = $raceResults->min('total_time');
        $bestRace = $bestTotalTime ? $raceResults->where('total_time', $bestTotalTime)->first() : null;

        $averageTotalTime = (int) $raceResults->avg('total_time');

        return [
            'total_races' => $totalRaces,
            'best_total_time' => $bestTotalTime ? [
                'time' => RaceResult::formatTime($bestTotalTime),
                'race' => $bestRace ? $this->formatRaceInfo($bestRace) : null,
            ] : null,
            'average_total_time' => $averageTotalTime > 0 ? RaceResult::formatTime($averageTotalTime) : null,
        ];
    }

    /**
     * Calculate personal bests for each discipline grouped by race type.
     *
     * @param  \Illuminate\Database\Eloquent\Collection<int, RaceResult>  $raceResults
     * @return array<string, mixed>
     */
    private function calculatePersonalBests($raceResults): array
    {
        $personalBests = [];

        // Группируем результаты по типу гонки
        foreach (RaceType::cases() as $raceType) {
            $resultsByType = $raceResults->where('race_type', $raceType);

            if ($resultsByType->isEmpty()) {
                continue;
            }

            $typeKey = $raceType->value;
            $personalBests[$typeKey] = [];

            // Best swim time for this race type
            $bestSwim = $resultsByType->where('swim_time', '>', 0)->min('swim_time');
            if ($bestSwim !== null) {
                $bestSwimRace = $resultsByType->where('swim_time', $bestSwim)->first();
                $personalBests[$typeKey]['swim'] = [
                    'time' => RaceResult::formatTime($bestSwim),
                    'race' => $bestSwimRace ? $this->formatRaceInfo($bestSwimRace) : null,
                ];
            }

            // Best bike time for this race type
            $bestBike = $resultsByType->where('bike_time', '>', 0)->min('bike_time');
            if ($bestBike !== null) {
                $bestBikeRace = $resultsByType->where('bike_time', $bestBike)->first();
                $personalBests[$typeKey]['bike'] = [
                    'time' => RaceResult::formatTime($bestBike),
                    'race' => $bestBikeRace ? $this->formatRaceInfo($bestBikeRace) : null,
                ];
            }

            // Best run time for this race type
            $bestRun = $resultsByType->where('run_time', '>', 0)->min('run_time');
            if ($bestRun !== null) {
                $bestRunRace = $resultsByType->where('run_time', $bestRun)->first();
                $personalBests[$typeKey]['run'] = [
                    'time' => RaceResult::formatTime($bestRun),
                    'race' => $bestRunRace ? $this->formatRaceInfo($bestRunRace) : null,
                ];
            }
        }

        return $personalBests;
    }

    /**
     * Format race information for response.
     *
     * @return array<string, mixed>
     */
    private function formatRaceInfo(RaceResult $raceResult): array
    {
        return [
            'id' => $raceResult->id,
            'race_date' => $raceResult->race_date->toDateString(),
            'location' => $raceResult->location,
            'race_type' => $raceResult->race_type->value,
            'race_type_label' => $raceResult->race_type->label(),
        ];
    }
}

