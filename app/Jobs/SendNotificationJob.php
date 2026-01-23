<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Notification;
use App\Models\User;
use App\Services\Firebase\FcmService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class SendNotificationJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private readonly int $notificationId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(FcmService $fcmService): void
    {
        $notification = Notification::with('user')->find($this->notificationId);

        if (! $notification) {
            \Illuminate\Support\Facades\Log::warning('Notification not found for notification job', [
                'notification_id' => $this->notificationId,
            ]);
            return;
        }

        $user = $notification->user;
        if (! $user) {
            \Illuminate\Support\Facades\Log::warning('Notification has no user', [
                'notification_id' => $notification->id,
            ]);
            return;
        }

        // Get user locale from database to use correct translation
        $userLocale = $this->getUserLocale($user);
        
        // Use translation from JSON if available, otherwise fallback to stored title/body
        $title = $notification->getTitle($userLocale);
        $body = $notification->getBody($userLocale);

        $fcmNotification = [
            'title' => $title,
            'body' => $body,
        ];
        
        \Illuminate\Support\Facades\Log::debug('Sending FCM notification with locale', [
            'notification_id' => $notification->id,
            'user_id' => $user->id,
            'locale' => $userLocale,
        ]);

        $data = $notification->data ?? [];
        $fcmData = array_merge(
            ['type' => $notification->type],
            array_map('strval', $data)
        );

        try {
            $fcmService->sendToUser($user, $fcmNotification, $fcmData);
        } catch (\Exception $e) {
            // Log error but don't throw - job will be retried by queue worker
            \Illuminate\Support\Facades\Log::error('Failed to send FCM push for notification job', [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Get user locale from database.
     */
    private function getUserLocale(User $user): string
    {
        try {
            // Reload user from database to get fresh locale
            $freshUser = User::withoutGlobalScopes()->find($user->id);
            
            if (! $freshUser) {
                return config('app.locale', 'en');
            }
            
            return $freshUser->locale ?? config('app.locale', 'en');
        } catch (\Exception $e) {
            return config('app.locale', 'en');
        }
    }
}
