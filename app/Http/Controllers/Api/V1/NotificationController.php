<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Actions\Notifications\SendNotificationAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get list of notifications for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = (int) $request->get('per_page', 15);
        $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50

        $notifications = $user->notifications()
            ->latest()
            ->paginate($perPage);

        $unreadCount = $user->notifications()
            ->whereNull('read_at')
            ->count();

        return $this->successResponse([
            'data' => NotificationResource::collection($notifications->items()),
            'meta' => [
                'current_page' => $notifications->currentPage(),
                'per_page' => $notifications->perPage(),
                'total' => $notifications->total(),
                'last_page' => $notifications->lastPage(),
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = $user->notifications()->find($id);

        if (! $notification) {
            return $this->errorResponse([
                'notification' => ['Уведомление не найдено или не принадлежит вам.'],
            ], 404);
        }

        $notification->markAsRead();

        return $this->successResponse([
            'data' => NotificationResource::make($notification),
            'message' => 'Уведомление помечено как прочитанное.',
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $updated = $user->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return $this->successResponse([
            'message' => "Все уведомления помечены как прочитанные ({$updated}).",
            'data' => [
                'marked_count' => $updated,
            ],
        ]);
    }

    /**
     * Delete single notification.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = $user->notifications()->find($id);

        if (! $notification) {
            return $this->errorResponse([
                'notification' => ['Уведомление не найдено или не принадлежит вам.'],
            ], 404);
        }

        $notification->delete();

        return $this->successResponse([
            'message' => 'Уведомление удалено.',
        ]);
    }

    /**
     * Send test notification (development only).
     */
    public function sendTest(Request $request): JsonResponse
    {
        // Только для разработки
        if (app()->environment('production')) {
            return $this->errorResponse([
                'message' => ['This endpoint is only available in development.'],
            ], 403);
        }

        $user = $request->user();
        $title = $request->input('title', 'Тестовое уведомление');
        $body = $request->input('body', 'Это тестовое уведомление для проверки FCM');
        $type = $request->input('type', 'system');
        $data = $request->input('data', ['test' => 'true']);

        // Проверка наличия FCM токенов
        $tokenCount = $user->fcmTokens()->count();
        if ($tokenCount === 0) {
            return $this->errorResponse([
                'message' => ['У вас нет зарегистрированных FCM токенов. Зарегистрируйте токен через POST /api/v1/user/fcm-token'],
            ], 400);
        }

        // Отправка синхронно для тестирования
        $action = app(SendNotificationAction::class);
        $notification = $action->run($user, $title, $body, $type, $data);

        return $this->successResponse([
            'message' => "Тестовое уведомление отправлено на {$tokenCount} устройство(а).",
            'data' => NotificationResource::make($notification),
        ], 201);
    }
}
