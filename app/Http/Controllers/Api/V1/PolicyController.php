<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PolicyResource;
use App\Models\Policy;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rule;

class PolicyController extends Controller
{
    use ApiResponse;

    /**
     * Get locale for response translation.
     * Uses authenticated user's locale if available, otherwise detects from request.
     */
    private function getResponseLocale(Request $request): string
    {
        $user = $request->user();
        if ($user && $user->locale) {
            return $user->locale;
        }

        return $this->detectLocale($request);
    }

    /**
     * Detect locale from request (header or parameter).
     */
    private function detectLocale(Request $request): string
    {
        // Check if locale is provided in request
        if ($request->has('locale') && in_array($request->input('locale'), Policy::SUPPORTED_LANGUAGES, true)) {
            return $request->input('locale');
        }

        // Try to detect from Accept-Language header
        $acceptLanguage = $request->header('Accept-Language');
        if ($acceptLanguage) {
            // Parse Accept-Language header (e.g., "en-US,en;q=0.9,ru;q=0.8")
            $languages = explode(',', $acceptLanguage);
            foreach ($languages as $lang) {
                $lang = trim(explode(';', $lang)[0]);
                $lang = strtolower(explode('-', $lang)[0]); // Get main language code
                if (in_array($lang, Policy::SUPPORTED_LANGUAGES, true)) {
                    return $lang;
                }
            }
        }

        // Fallback to default locale
        return config('app.locale', 'en');
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
     * Get policy by type and language.
     */
    public function getByType(Request $request, string $type): JsonResponse
    {
        // Validate policy type
        if (!array_key_exists($type, Policy::TYPES)) {
            return $this->errorResponse([
                'type' => [$this->trans($request, 'api.policy.invalid_type')],
            ], 400);
        }

        // Get user's preferred language
        $language = $this->getResponseLocale($request);

        // Find the policy
        $policy = Policy::getLatestActive($type, $language);

        if (!$policy) {
            return $this->errorResponse([
                'policy' => [$this->trans($request, 'api.policy.not_found')],
            ], 404);
        }

        return $this->successResponse([
            'data' => PolicyResource::make($policy),
        ]);
    }

    /**
     * Get all available policy types.
     */
    public function types(Request $request): JsonResponse
    {
        $types = collect(Policy::TYPES)->map(function ($name, $key) {
            return [
                'type' => $key,
                'name' => $name,
            ];
        })->values();

        return $this->successResponse([
            'data' => $types,
        ]);
    }

    /**
     * Get available languages for policies.
     */
    public function languages(Request $request): JsonResponse
    {
        $languages = collect(Policy::SUPPORTED_LANGUAGES)->map(function ($code) {
            return [
                'code' => $code,
                'name' => $this->getLanguageName($code),
            ];
        });

        return $this->successResponse([
            'data' => $languages,
        ]);
    }

    /**
     * Get human-readable language name.
     */
    private function getLanguageName(string $code): string
    {
        $names = [
            'en' => 'English',
            'ru' => 'Русский',
            'az' => 'Azərbaycan',
        ];

        return $names[$code] ?? $code;
    }
}
