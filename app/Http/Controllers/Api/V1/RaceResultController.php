<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RaceResult\DeleteRaceResultRequest;
use App\Http\Requests\RaceResult\StoreRaceResultRequest;
use App\Http\Requests\RaceResult\UpdateRaceResultRequest;
use App\Http\Resources\RaceResultResource;
use App\Models\RaceResult;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class RaceResultController extends Controller
{
    use ApiResponse;

    /** Get all race results (paginated) */
    public function index(): JsonResponse
    {
        $results = RaceResult::orderByDesc('race_date')
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

    /** Get race results for a specific user */
    public function userResults(User $user): JsonResponse
    {
        $results = $user->raceResults()
            ->orderByDesc('race_date')
            ->get();

        return $this->successResponse([
            'data' => RaceResultResource::collection($results)
                ->map(fn (RaceResultResource $r) => $r->withoutUserId()),
        ]);
    }

    /** Get a single race result */
    public function show(RaceResult $raceResult): JsonResponse
    {
        $raceResult->load('user');

        return $this->successResponse([
            'data' => RaceResultResource::make($raceResult),
        ]);
    }

    /** Store a new race result */
    public function store(StoreRaceResultRequest $request): JsonResponse
    {
        $raceResult = $request->user()->raceResults()->create($request->validated());

        return $this->successResponse([
            'data' => RaceResultResource::make($raceResult),
        ], 201);
    }

    /** Update a race result */
    public function update(UpdateRaceResultRequest $request, RaceResult $raceResult): JsonResponse
    {
        $raceResult->update($request->validated());

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
