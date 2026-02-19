<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpcomingRace\StoreUpcomingRaceRequest;
use App\Http\Resources\UpcomingRaceResource;
use App\Models\UpcomingRace;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class UpcomingRaceController extends Controller
{
    use ApiResponse;

    /**
     * Get locale for response translation.
     */
    private function getResponseLocale(Request $request): string
    {
        $user = $request->user();
        if ($user && $user->locale) {
            return $user->locale;
        }

        return $request->input('locale')
            ?? $this->detectLocaleFromHeader($request)
            ?? config('app.locale', 'en');
    }

    /**
     * Detect locale from Accept-Language header.
     */
    private function detectLocaleFromHeader(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        $availableLocales = ['en', 'ru'];
        $preferred = substr($acceptLanguage, 0, 2);

        return in_array($preferred, $availableLocales) ? $preferred : null;
    }

    /**
     * Translate message using request locale.
     */
    private function trans(Request $request, string $key, array $params = []): string
    {
        $locale = $this->getResponseLocale($request);
        $originalLocale = App::getLocale();
        App::setLocale($locale);

        $translated = trans($key, $params);

        App::setLocale($originalLocale);

        return $translated;
    }

    /**
     * Get list of upcoming races with optional filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = UpcomingRace::with(['race', 'userProfile.user'])
            ->where('upcoming_races.is_active', true)
            ->join('races', 'upcoming_races.race_id', '=', 'races.id')
            ->where('races.is_active', true)
            ->orderBy('races.date');

        // Отсекаем записи без профиля заранее, чтобы не ловить null в маппере
        $query->whereHas('userProfile');

        // Filter by only_future parameter (default: true for backward compatibility)
        $onlyFuture = $request->query('only_future', 'true');
        if ($onlyFuture !== 'false') {
            $query->whereHas('race', function ($q) {
                $q->where('date', '>=', now()->toDateString());
            });
        }

        if ($request->filled('user_profile_id')) {
            $query->where('user_profile_id', $request->query('user_profile_id'));
        }

        if ($request->filled('race_type')) {
            $query->whereHas('race', function ($q) use ($request) {
                $q->where('type', $request->query('race_type'));
            });
        }

        // Select only upcoming_races columns to avoid conflicts
        $query->select('upcoming_races.*');

        $races = $query->get();

        return $this->successResponse([
            'data' => UpcomingRaceResource::collection($races)
        ]);
    }

    /**
     * Create a new upcoming race.
     */
    public function store(StoreUpcomingRaceRequest $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return $this->errorResponse([
                'profile' => [$this->trans($request, 'api.profile.not_found')]
            ], 403);
        }

        $race = $profile->upcomingRaces()->create($request->validated());
        $race->load('race', 'userProfile.user');

        return $this->successResponse([
            'message' => $this->trans($request, 'api.upcoming_race.created'),
            'data' => UpcomingRaceResource::make($race)
        ], 201);
    }
}
