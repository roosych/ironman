<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RaceResult\DeleteRaceResultRequest;
use App\Http\Requests\RaceResult\StoreRaceResultRequest;
use App\Http\Requests\RaceResult\UpdateRaceResultRequest;
use App\Http\Resources\RaceResultResource;
use App\Models\RaceResult;
use App\Models\UserProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RaceResultController extends Controller
{
    use ApiResponse;

    /** Get all race results (paginated) - only approved */
    public function index(): JsonResponse
    {
        $results = RaceResult::with(['profile.user'])
            ->where('is_approved', true)
            ->orderByDesc('race_date')
            ->paginate(15);

        return $this->successResponse([
            'data' => RaceResultResource::collection($results),
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
            ],
        ]);
    }

    /** Get race results for a specific profile - only approved */
    public function profileResults(UserProfile $userProfile): JsonResponse
    {
        $results = $userProfile->raceResults()
            ->where('is_approved', true)
            ->with(['profile.user'])
            ->orderByDesc('race_date')
            ->get();

        return $this->successResponse([
            'data' => RaceResultResource::collection($results)
                ->map(fn (RaceResultResource $r) => $r->withoutProfileId()),
        ]);
    }

    /** Get a single race result - only if approved */
    public function show(RaceResult $raceResult): JsonResponse
    {
        if (!$raceResult->is_approved) {
            return $this->errorResponse(['message' => ['Результат не найден или не подтверждён.']], 404);
        }

        $raceResult->load('profile.user');

        return $this->successResponse([
            'data' => RaceResultResource::make($raceResult),
        ]);
    }

    /** Store a new race result - requires admin approval */
    public function store(StoreRaceResultRequest $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (! $profile) {
            return $this->errorResponse(['profile' => ['Профиль не найден.']], 403);
        }

        $data = $request->validated();
        $data['is_approved'] = false; // Results require admin approval

        $raceResult = $profile->raceResults()->create($data);
        $raceResult->load('profile.user');

        return $this->successResponse([
            'message' => 'Результат отправлен на подтверждение администратором.',
            'data' => RaceResultResource::make($raceResult),
        ], 201);
    }

    /** Update a race result */
    public function update(UpdateRaceResultRequest $request, RaceResult $raceResult): JsonResponse
    {
        $raceResult->update($request->validated());
        $raceResult->load('profile.user');

        return $this->successResponse([
            'data' => RaceResultResource::make($raceResult),
        ]);
    }

    /** Delete a race result */
    public function destroy(DeleteRaceResultRequest $request, RaceResult $raceResult): JsonResponse
    {
        $raceResult->delete();

        return $this->successResponse([
            'message' => 'Результат удалён.',
        ]);
    }
}
