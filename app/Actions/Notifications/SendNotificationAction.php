<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\Notification;
use App\Models\User;
use App\Services\Firebase\FcmService;

class SendNotificationAction
{
    public function __construct(
        private readonly FcmService $fcmService
    ) {}

    /**
     * Create notification (DB) and send via Job (async).
     *
     * @param  array<string, mixed>  $data
     */
    public static function dispatch(
        User $user,
        string $title,
        string $body,
        string $type,
        array $data = []
    ): void {
        // Create notification in DB once (idempotent for job retries)
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
        ]);

        \App\Jobs\SendNotificationJob::dispatch($notification->id);
    }

    /**
     * Send notification to user (save to DB and send FCM push).
     * Synchronous mode (useful for development/testing).
     *
     * @param  array<string, mixed>  $data
     * @return Notification
     */
    public function run(
        User $user,
        string $title,
        string $body,
        string $type,
        array $data = []
    ): Notification {
        // Create notification in database
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'data' => $data,
        ]);

        // Prepare FCM notification payload
        $fcmNotification = [
            'title' => $title,
            'body' => $body,
        ];

        // Prepare FCM data payload (convert all values to strings as required by FCM)
        $fcmData = array_merge(
            ['type' => $type],
            array_map('strval', $data)
        );

        // Send FCM push to all user devices
        // Errors are logged but don't break the flow
        try {
            $this->fcmService->sendToUser($user, $fcmNotification, $fcmData);
        } catch (\Exception $e) {
            // Log error but don't fail - notification is already saved in DB
            \Illuminate\Support\Facades\Log::error('Failed to send FCM push for notification', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $notification;
    }
}

