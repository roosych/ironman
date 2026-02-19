<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Enums\RaceType;
use App\Http\Controllers\Controller;
use App\Http\Resources\RaceResource;
use App\Models\Race;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RaceController extends Controller
{
    use ApiResponse;

    /**
     * Получить список активных гонок с фильтрацией.
     */
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'size:2'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        // Проверить валидность enum type
        if (!empty($validated['type'])) {
            $validTypes = array_column(RaceType::cases(), 'value');
            if (!in_array($validated['type'], $validTypes, true)) {
                return $this->errorResponse([
                    'type' => ['Invalid race type. Valid types are: ' . implode(', ', $validTypes)],
                ], 422);
            }
        }

        $query = Race::where('is_active', true);

        // Исключить гонки, которые уже есть у пользователя в upcoming_races
        $user = $request->user();
        if ($user && $user->profile) {
            $userRaceIds = $user->profile->upcomingRaces()
                ->where('is_active', true)
                ->pluck('race_id')
                ->toArray();

            if (!empty($userRaceIds)) {
                $query->whereNotIn('id', $userRaceIds);
            }
        }

        // Применить фильтры
        if (!empty($validated['search'])) {
            $query->where('location', 'like', '%' . $validated['search'] . '%');
        }

        if (!empty($validated['type'])) {
            $query->where('type', $validated['type']);
        }

        if (!empty($validated['country'])) {
            $query->where('country_iso', strtoupper($validated['country']));
        }

        if (!empty($validated['date_from'])) {
            $query->where('date', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->where('date', '<=', $validated['date_to']);
        }

        // Сортировка по дате
        $races = $query->orderBy('date')->get();

        return $this->successResponse([
            'data' => RaceResource::collection($races),
        ]);
    }
}
