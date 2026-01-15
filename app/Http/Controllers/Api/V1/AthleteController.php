<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

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

        $data = $profiles->map(function (UserProfile $profile) {
            $counts = $profile->raceResults
                ->groupBy(fn ($result) => $result->race_type->value)
                ->map(fn ($items) => $items->count());

            return [
                'id' => $profile->id,
                'name' => $profile->user?->name ?? $profile->admin_full_name,
                'avatar' => $profile->user?->avatar?->url,
                'race_counts' => [
                    'ironman' => $counts->get('ironman', 0),
                    'ironman_70_3' => $counts->get('ironman_70_3', 0),
                    '5150' => $counts->get('5150', 0),
                ],
            ];
        });

        return $this->successResponse(['data' => $data]);
    }
}
