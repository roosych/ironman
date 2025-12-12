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

    /** Register a new user */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->safe()->name,
            'email' => $request->safe()->email,
            'password' => Hash::make($request->safe()->password),
        ]);

        //event(new Registered($user));

        $user->notify(new VerifyEmailNotification());

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
        if (!Auth::attempt($request->validated())) {
            return $this->errorResponse([
                'email' => ['Неверный email или пароль.']
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Remove all previous tokens
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        $responseData = [
            'data' => [
                'user' => UserResource::make($user),
                'token' => $token,
            ]
        ];

        // Если email не подтвержден, добавляем информацию об этом
        if (!$user->hasVerifiedEmail()) {
            $responseData['message'] = 'Ваш email не подтверждён. Пожалуйста, проверьте почту или запросите новое письмо для подтверждения.';
            $responseData['email_not_verified'] = true;
        }

        return $this->successResponse($responseData);
    }

    /** Logout user (revoke token) */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->errorResponse(['auth' => ['Не авторизован.']], 401);
        }

        $token = $user->currentAccessToken();
        $token?->delete();

        return $this->successResponse(['message' => 'Выход выполнен успешно.']);
    }

    /** Get authenticated user */
    public function user(Request $request): JsonResponse
    {
        return $this->successResponse([
            'data' => UserResource::make($request->user())
        ]);
    }

    /** Send email verification */
    public function sendVerificationEmail(Request $request): JsonResponse
    {
        $user = $request->user();

        // Проверка: подтвержденный пользователь не может запросить письмо заново
        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse([
                'email' => ['Email уже подтверждён. Нет необходимости отправлять письмо повторно.']
            ], 422);
        }

        $user->notify(new VerifyEmailNotification());

        return $this->successResponse(['message' => 'Письмо для подтверждения отправлено.']);
    }

    /** Send password reset link */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->safe()->email)->first();

        if ($user) {
            $token = Password::createToken($user);
            $user->notify(new ResetPasswordNotification($token));
        }

        return $this->successResponse();
    }

    /** Reset password */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();

                event(new PasswordReset($user));
            }
        );

        // Обработка различных статусов ошибок
        if ($status === Password::PASSWORD_RESET) {
            return $this->successResponse([
                'message' => 'Пароль успешно изменён.'
            ]);
        }

        // Детальная обработка ошибок
        $errorMessages = match ($status) {
            Password::INVALID_TOKEN => [
                'token' => ['Неверный или истёкший токен сброса пароля. Пожалуйста, запросите новую ссылку для сброса пароля.']
            ],
            Password::INVALID_USER => [
                'email' => ['Пользователь с таким email не найден.']
            ],
            Password::THROTTLED => [
                'email' => ['Слишком много попыток. Пожалуйста, попробуйте позже.']
            ],
            default => [
                'email' => ['Не удалось сбросить пароль. Проверьте данные и попробуйте снова.']
            ],
        };

        return $this->errorResponse($errorMessages, 422);
    }
}
