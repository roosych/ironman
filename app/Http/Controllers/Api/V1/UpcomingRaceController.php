<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpcomingRace\StoreUpcomingRaceRequest;
use App\Models\UpcomingRace;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UpcomingRaceController extends Controller
{
    use ApiResponse;

    /**
     * Get list of upcoming races with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UpcomingRace::with(['profile.user.avatar'])
            ->where('is_active', true)
            ->orderBy('race_date');

        // Отсекаем записи без профиля заранее, чтобы не ловить null в маппере
        $query->whereHas('profile');

        // Filter by only_future parameter (default: true for backward compatibility)
        $onlyFuture = $request->query('only_future', 'true');
        if ($onlyFuture !== 'false') {
            $query->where('race_date', '>=', now()->toDateString());
        }

        if ($request->filled('user_profile_id')) {
            $query->where('user_profile_id', $request->query('user_profile_id'));
        }

        if ($request->filled('race_type')) {
            $query->where('race_type', $request->query('race_type'));
        }

        $races = $query->get();

        $data = $races->filter(fn (UpcomingRace $race) => $race->profile !== null)
            ->map(fn (UpcomingRace $race) => [
                'id' => $race->id,
                'race_type' => $race->race_type->value,
                'race_type_label' => $race->race_type->label(),
                'location' => $race->location,
                'race_date' => $race->race_date->toDateString(),
                'is_active' => $race->is_active,
                'created_by' => [
                    'id' => $race->profile->id,
                    'name' => $race->profile->user?->name ?? $race->profile->admin_full_name,
                    'avatar' => $race->profile->user?->avatar?->url,
                ],
            ]);

        return $this->successResponse(['data' => $data]);
    }

    /**
     * Create a new upcoming race.
     */
    public function store(StoreUpcomingRaceRequest $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return $this->errorResponse(['profile' => ['Профиль не найден.']], 403);
        }

        $race = $profile->upcomingRaces()->create($request->validated());
        $race->load('profile.user.avatar');

        return $this->successResponse([
            'data' => [
                'id' => $race->id,
                'race_type' => $race->race_type->value,
                'race_type_label' => $race->race_type->label(),
                'location' => $race->location,
                'race_date' => $race->race_date->toDateString(),
                'is_active' => $race->is_active,
                'created_by' => [
                    'id' => $race->profile->id,
                    'name' => $race->profile->user?->name ?? $race->profile->admin_full_name,
                    'avatar' => $race->profile->user?->avatar?->url,
                ],
            ],
        ], 201);
    }
}
