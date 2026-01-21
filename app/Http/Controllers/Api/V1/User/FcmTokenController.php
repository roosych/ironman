<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Fcm\DeleteFcmTokenRequest;
use App\Http\Requests\Fcm\StoreFcmTokenRequest;
use App\Models\FcmToken;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FcmTokenController extends Controller
{
    use ApiResponse;

    /**
     * Register or update FCM token for authenticated user.
     */
    public function store(StoreFcmTokenRequest $request): JsonResponse
    {
        $user = $request->user();
        $validated = $request->safe()->all();

        // Используем updateOrCreate чтобы не дублировать токены
        $fcmToken = FcmToken::updateOrCreate(
            ['token' => $validated['token']],
            [
                'user_id' => $user->id,
                'device_type' => $validated['device_type'] ?? null,
                'device_name' => $validated['device_name'] ?? null,
            ]
        );

        return $this->successResponse([
            'message' => 'FCM токен успешно зарегистрирован.',
            'data' => [
                'token' => $fcmToken->token,
                'device_type' => $fcmToken->device_type,
                'device_name' => $fcmToken->device_name,
            ],
        ], 201);
    }

    /**
     * Delete FCM token (logout, app uninstall, token refresh).
     */
    public function destroy(DeleteFcmTokenRequest $request): JsonResponse
    {
        $user = $request->user();
        $token = $request->safe()->token;

        // Удаляем токен только если он принадлежит текущему пользователю
        $deleted = FcmToken::where('token', $token)
            ->where('user_id', $user->id)
            ->delete();

        if (! $deleted) {
            return $this->errorResponse([
                'token' => ['FCM токен не найден или не принадлежит вам.'],
            ], 404);
        }

        return $this->successResponse([
            'message' => 'FCM токен успешно удален.',
        ]);
    }
}

