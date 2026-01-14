<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    use ApiResponse;

    /**
     * Link an existing profile to a user.
     * 
     * POST /api/v1/admin/users/{user}/link-profile/{profile}
     */
    public function linkProfileToUser(User $user, UserProfile $profile): JsonResponse
    {
        // Проверка прав админа
        $admin = auth()->user();
        if (! $admin || ! $admin->profile || ! $admin->profile->isAdmin()) {
            return $this->errorResponse([
                'message' => ['Доступ запрещён. Требуются права администратора.'],
            ], 403);
        }

        // Проверка: профиль уже привязан к другому пользователю
        if ($profile->user_id !== null && $profile->user_id !== $user->id) {
            return $this->errorResponse([
                'profile' => ['Этот профиль уже привязан к другому пользователю.'],
            ], 422);
        }

        // Если у пользователя уже есть профиль - удаляем его (заменяем на существующий)
        if ($user->profile && $user->profile->id !== $profile->id) {
            $oldProfile = $user->profile;
            $oldProfile->update(['user_id' => null]);
        }

        // Привязываем профиль к пользователю
        $profile->update([
            'user_id' => $user->id,
            'synced_existing_profile' => true,
            'sync_requested' => false,
        ]);

        // Обновляем admin_full_name из имени пользователя, если не установлено
        if (! $profile->admin_full_name) {
            $profile->update(['admin_full_name' => $user->name]);
        }

        return $this->successResponse([
            'message' => 'Профиль успешно привязан к пользователю.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'profile' => [
                    'id' => $profile->id,
                    'role' => $profile->role,
                    'ironman_number' => $profile->ironman_number,
                ],
            ],
        ]);
    }
}

