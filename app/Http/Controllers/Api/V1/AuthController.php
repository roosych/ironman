<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Traits\ApiResponse;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $profile->load(['raceResults' => function ($query) {
                $query->orderByDesc('race_date');
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

    /** Register a new user */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->safe()->name,
            'email' => $request->safe()->email,
            'password' => Hash::make($request->safe()->password),
        ]);

        // event(new Registered($user));

        $user->notify(new VerifyEmailNotification);

        // Загружаем relations для консистентности ответа (будут null/empty для нового пользователя)
        $this->loadUserWithProfile($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->successResponse([
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

        if (! $user || ! Hash::check($request->validated()['password'], $user->password)) {
            return $this->errorResponse([
                'email' => ['Неверный email или пароль.'],
            ], 401);
        }

        $this->loadUserForLogin($user);

        // Remove all previous tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Создаем специальный request с флагом для исключения race_results при логине
        $loginRequest = clone $request;
        $loginRequest->merge(['exclude_race_results' => true]);

        $responseData = [
            'data' => [
                'user' => UserResource::make($user)->toArray($loginRequest),
                'token' => $token,
            ],
        ];

        if (! $user->hasVerifiedEmail()) {
            $responseData['message'] = 'Ваш email не подтверждён. Пожалуйста, проверьте почту или запросите новое письмо для подтверждения.';
        }

        return $this->successResponse($responseData);
    }

    /** Logout user (revoke token) */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return $this->errorResponse(['auth' => ['Не авторизован.']], 401);
        }

        $token = $user->currentAccessToken();
        $token?->delete();

        return $this->successResponse(['message' => 'Выход выполнен успешно.']);
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
                'email' => ['Email уже подтверждён. Нет необходимости отправлять письмо повторно.'],
            ], 422);
        }

        $user->notify(new VerifyEmailNotification);

        return $this->successResponse(['message' => 'Письмо для подтверждения отправлено.']);
    }

    /** Send password reset link */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        // Обходим глобальный scope для восстановления пароля
        $user = User::withoutGlobalScope('hide_reviewer')
            ->where('email', $request->safe()->email)
            ->first();

        if ($user) {
            $token = Password::createToken($user);
            $user->notify(new ResetPasswordNotification($token));
        }

        return $this->successResponse();
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
                'email' => ['Пользователь с таким email не найден.'],
            ], 422);
        }

        // Проверяем токен через Password facade
        if (! Password::tokenExists($user, $request->safe()->token)) {
            return $this->errorResponse([
                'token' => ['Неверный или истёкший токен сброса пароля. Пожалуйста, запросите новую ссылку для сброса пароля.'],
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
            'message' => 'Пароль успешно изменён.',
        ]);
    }
}
