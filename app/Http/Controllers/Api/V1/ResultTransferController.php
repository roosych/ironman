<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use App\Services\ResultTransferService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;

class ResultTransferController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly ResultTransferService $transferService
    ) {}

    /**
     * Получить список атлетов, доступных для переноса результатов.
     */
    public function eligibleAthletes(Request $request): JsonResponse
    {
        $athletes = $this->transferService->getEligibleAthletes();

        $data = $athletes->map(function (UserProfile $profile) {
            $counts = $profile->raceResults
                ->groupBy(fn ($result) => $result->race_type->value)
                ->map(fn ($items) => $items->count());

            return [
                'id' => $profile->id,
                'name' => $profile->user?->name ?? $profile->admin_full_name,
                'avatar' => $profile->user?->avatar?->url,
                'country_iso' => $profile->country_iso,
                'ironman_number' => $profile->ironman_number,
                'race_counts' => [
                    'ironman' => $counts->get('ironman', 0),
                    'ironman_70_3' => $counts->get('ironman_70_3', 0),
                    '5150' => $counts->get('5150', 0),
                ],
                'total_races' => $profile->raceResults->count(),
            ];
        });

        return $this->successResponse(['data' => $data]);
    }

    /**
     * Создать запрос на перенос результатов.
     */
    public function store(Request $request): JsonResponse
    {
        // Базовая валидация
        $request->validate([
            'source_athlete_id' => ['required', 'integer', 'exists:user_profiles,id'],
        ]);

        try {
            $transferRequest = $this->transferService->createRequest(
                $request->user(),
                (int) $request->input('source_athlete_id')
            );

            return $this->successResponse([
                'message' => $this->trans($request, 'api.transfer.request_created'),
                'data' => [
                    'id' => $transferRequest->id,
                    'source_athlete_id' => $transferRequest->source_athlete_id,
                    'status' => $transferRequest->status->value,
                    'created_at' => $transferRequest->created_at->toISOString(),
                ],
            ], 201);
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse([
                'source_athlete_id' => [$e->getMessage()],
            ], 422);
        }
    }

    /**
     * Получить текущий запрос пользователя на перенос результатов.
     */
    public function current(Request $request): JsonResponse
    {
        $user = $request->user();

        $currentRequest = $user->resultTransferRequests()
            ->with(['sourceAthlete:id,admin_full_name', 'reviewedBy:id,name'])
            ->orderByDesc('created_at')
            ->first();

        if (!$currentRequest) {
            return $this->successResponse([
                'data' => null,
                'message' => $this->trans($request, 'api.transfer.no_request'),
            ]);
        }

        return $this->successResponse([
            'data' => [
                'id' => $currentRequest->id,
                'source_athlete' => [
                    'id' => $currentRequest->sourceAthlete->id,
                    'name' => $currentRequest->sourceAthlete->admin_full_name,
                ],
                'status' => $currentRequest->status->value,
                'status_label' => $currentRequest->status->label(),
                'comment' => $currentRequest->comment,
                'reviewed_by' => $currentRequest->reviewedBy?->name,
                'reviewed_at' => $currentRequest->reviewed_at?->toISOString(),
                'created_at' => $currentRequest->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * Получить локаль для ответа.
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
     * Определить локаль из заголовка Accept-Language.
     */
    private function detectLocaleFromHeader(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');
        if (!$acceptLanguage) {
            return null;
        }

        $languages = explode(',', $acceptLanguage);
        foreach ($languages as $language) {
            $lang = trim(explode(';', $language)[0]);
            $lang = explode('-', $lang)[0]; // Get language part without country

            if (in_array($lang, ['ru', 'en'], true)) {
                return $lang;
            }
        }

        return null;
    }

    /**
     * Перевести сообщение используя локаль запроса.
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
}