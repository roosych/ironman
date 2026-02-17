<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\UpdateLocaleRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Load user with profile and photos, setting up proper relations.
     */
    private function loadUserWithProfile(User $user): User
    {
        $user->load(['photos']);

        // Загружаем профиль без глобального scope для скрытого пользователя
        $profile = \App\Models\UserProfile::withoutGlobalScope('hide_reviewer_profiles')
            ->where('user_id', $user->id)
            ->first();
        
        if ($profile) {
            $user->setRelation('profile', $profile);
            // Load only approved race results
            $profile->load(['raceResults' => function ($query) {
                $query->where('is_approved', true)
                    ->orderByDesc('race_date');
            }]);
            $profile->setRelation('user', $user);
        }

        return $user;
    }

    /**
     * Load user with profile for login (statistics only, without full race results list).
     */
    private function loadUserForLogin(User $user): User
    {
        $user->load(['photos']);

        // Загружаем профиль без глобального scope для скрытого пользователя
        $profile = UserProfile::withoutGlobalScope('hide_reviewer_profiles')
            ->where('user_id', $user->id)
            ->first();
        
        if ($profile) {
            $user->setRelation('profile', $profile);
            // Загружаем только одобренные результаты для статистики
            $profile->load(['raceResults' => function ($query) {
                $query->where('is_approved', true);
            }]);
            $profile->setRelation('user', $user);
        }

        return $user;
    }

    /**
     * Detect locale from request (header or parameter).
     */
    private function detectLocale(Request $request): string
    {
        // Check if locale is provided in request
        if ($request->has('locale') && in_array($request->input('locale'), ['ru', 'en', 'az'], true)) {
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
                if (in_array($lang, ['ru', 'en', 'az'], true)) {
                    return $lang;
                }
            }
        }

        // Fallback to default locale
        return config('app.locale', 'en');
    }

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


    /** Register a new user */
    public function register(RegisterRequest $request): JsonResponse
    {
        // Use locale from request if provided, otherwise detect from headers
        $locale = $request->safe()->locale ?? $this->detectLocale($request);

        // Создаем пользователя и профиль атлета в транзакции
        $user = DB::transaction(function () use ($request, $locale) {
            // Создаем пользователя
            $user = User::create([
                'name' => $request->safe()->name,
                'email' => $request->safe()->email,
                'password' => Hash::make($request->safe()->password),
                'locale' => $locale,
            ]);

            // Сразу создаем профиль атлета
            $user->profile()->create([
                'admin_full_name' => $request->safe()->name,
                'country_iso' => strtoupper($request->safe()->country_iso),
                'role' => 'athlete',
                'ironman_number' => 0,
            ]);

            return $user;
        });

        // event(new Registered($user));

        $user->notify(new VerifyEmailNotification);

        // Загружаем relations для консистентности ответа
        $this->loadUserWithProfile($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
            'message' => $this->trans($request, 'api.auth.register_success'),
            'data' => [
                'user' => UserResource::make($user),
                'token' => $token,
            ],
        ], 201);
    }

    /** Login user and create token */
    public function login(LoginRequest $request): JsonResponse
    {
        // Обходим глобальный scope для аутентификации, чтобы тестовый пользователь мог логиниться
        $user = User::withoutGlobalScope('hide_reviewer')
            ->where('email', $request->validated()['email'])
            ->first();

        // Always return the same error message to prevent email enumeration
        if (! $user || ! Hash::check($request->validated()['password'], $user->password)) {
            $locale = $this->detectLocale($request);
            $originalLocale = App::getLocale();
            App::setLocale($locale);

            $errorMessage = trans('api.auth.invalid_credentials');

            App::setLocale($originalLocale);

            return $this->errorResponse([
                'email' => [$errorMessage],
            ], 401);
        }

        // Update locale if provided in request or detected from headers
        $requestLocale = $request->safe()->locale ?? $this->detectLocale($request);
        if ($user->locale !== $requestLocale) {
            $user->update(['locale' => $requestLocale]);
        }

        $this->loadUserForLogin($user);

        // Remove all previous tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Создаем специальный request с флагом для исключения race_results при логине
        $loginRequest = clone $request;
        $loginRequest->merge(['exclude_race_results' => true]);

        $responseData = [
            'message' => $this->trans($request, 'api.auth.login_success'),
            'data' => [
                'user' => UserResource::make($user)->toArray($loginRequest),
                'token' => $token,
            ],
        ];

        if (! $user->hasVerifiedEmail()) {
            $responseData['message'] = $this->trans($request, 'api.auth.email_not_verified');
        }

        return $this->successResponse($responseData);
    }

    /** Logout user (revoke token) */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            $locale = $this->detectLocale($request);
            $originalLocale = App::getLocale();
            App::setLocale($locale);

            $errorMessage = trans('api.auth.unauthorized');

            App::setLocale($originalLocale);

            return $this->errorResponse(['auth' => [$errorMessage]], 401);
        }

        $token = $user->currentAccessToken();
        $token?->delete();

        return $this->successResponse([
            'message' => $this->trans($request, 'api.auth.logout_success'),
        ]);
    }

    /** Get authenticated user */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $this->loadUserWithProfile($user);

        return $this->successResponse([
            'data' => UserResource::make($user),
        ]);
    }

    /** Send email verification */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        // Проверка: подтвержденный пользователь не может запросить письмо заново
        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse([
                'email' => [$this->trans($request, 'api.auth.email_already_verified')],
            ], 422);
        }

        $user->notify(new VerifyEmailNotification);

        return $this->successResponse([
            'message' => $this->trans($request, 'api.auth.verification_sent'),
        ]);
    }

    /** Send password reset link */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        // Always return the same message to prevent email enumeration
        // Обходим глобальный scope для восстановления пароля
        $user = User::withoutGlobalScope('hide_reviewer')
            ->where('email', $request->safe()->email)
            ->first();

        if ($user) {
            $token = Password::createToken($user);
            $user->notify(new ResetPasswordNotification($token));
        }

        // Always return success message regardless of whether user exists
        return $this->successResponse([
            'message' => $this->trans($request, 'api.password.reset_link_sent'),
        ]);
    }

    /** Reset password */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        // Обходим глобальный scope для поиска пользователя
        $user = User::withoutGlobalScope('hide_reviewer')
            ->where('email', $request->safe()->email)
            ->first();

        if (! $user) {
            return $this->errorResponse([
                'email' => [$this->trans($request, 'api.password.user_not_found')],
            ], 422);
        }

        // Проверяем токен через Password facade
        if (! Password::tokenExists($user, $request->safe()->token)) {
            return $this->errorResponse([
                'token' => [$this->trans($request, 'api.password.invalid_token')],
            ], 422);
        }

        // Сбрасываем пароль
        $user->forceFill([
            'password' => Hash::make($request->safe()->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Удаляем все токены пользователя
        $user->tokens()->delete();

        // Удаляем токен сброса пароля
        Password::deleteToken($user);

        event(new PasswordReset($user));

        return $this->successResponse([
            'message' => $this->trans($request, 'api.password.reset_success'),
        ]);
    }

    /** Update user locale */
    public function updateLocale(UpdateLocaleRequest $request): JsonResponse
    {
        $user = $request->user();
        $user->update([
            'locale' => $request->safe()->locale,
        ]);

        return $this->successResponse([
            'message' => $this->trans($request, 'api.locale.updated'),
            'data' => [
                'locale' => $user->locale,
            ],
        ]);
    }
}
