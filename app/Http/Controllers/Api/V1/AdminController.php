<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RaceResultResource;
use App\Models\RaceResult;
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

    /**
     * Check if current user is admin.
     */
    private function checkAdmin(): ?User
    {
        $admin = auth()->user();
        if (! $admin || ! $admin->profile || ! $admin->profile->isAdmin()) {
            return null;
        }
        return $admin;
    }

    /**
     * Get list of pending race results (waiting for approval).
     * 
     * GET /api/v1/admin/race-results/pending
     */
    public function pendingRaceResults(): JsonResponse
    {
        $admin = $this->checkAdmin();
        if (! $admin) {
            return $this->errorResponse([
                'message' => ['Доступ запрещён. Требуются права администратора.'],
            ], 403);
        }

        $results = RaceResult::with(['profile.user', 'approver'])
            ->where('is_approved', false)
            ->orderBy('created_at')
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

    /**
     * Approve a race result.
     * 
     * POST /api/v1/admin/race-results/{raceResult}/approve
     */
    public function approveRaceResult(RaceResult $raceResult): JsonResponse
    {
        $admin = $this->checkAdmin();
        if (! $admin) {
            return $this->errorResponse([
                'message' => ['Доступ запрещён. Требуются права администратора.'],
            ], 403);
        }

        if ($raceResult->is_approved) {
            return $this->errorResponse([
                'message' => ['Результат уже подтверждён.'],
            ], 422);
        }

        $raceResult->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        $raceResult->load(['profile.user', 'approver']);

        return $this->successResponse([
            'message' => 'Результат успешно подтверждён.',
            'data' => RaceResultResource::make($raceResult),
        ]);
    }

    /**
     * Reject a race result (delete it).
     * 
     * DELETE /api/v1/admin/race-results/{raceResult}/reject
     */
    public function rejectRaceResult(RaceResult $raceResult): JsonResponse
    {
        $admin = $this->checkAdmin();
        if (! $admin) {
            return $this->errorResponse([
                'message' => ['Доступ запрещён. Требуются права администратора.'],
            ], 403);
        }

        if ($raceResult->is_approved) {
            return $this->errorResponse([
                'message' => ['Нельзя отклонить уже подтверждённый результат.'],
            ], 422);
        }

        $raceResult->delete();

        return $this->successResponse([
            'message' => 'Результат отклонён и удалён.',
        ]);
    }
}

