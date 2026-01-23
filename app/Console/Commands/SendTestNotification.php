<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Notifications\SendNotificationAction;
use App\Models\User;
use Illuminate\Console\Command;

class SendTestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:test 
                            {--user= : User email or ID}
                            {--title=Тестовое уведомление : Notification title}
                            {--body=Это тестовое уведомление для проверки FCM : Notification body}
                            {--type=system : Notification type (system, race, result, upcoming, admin)}
                            {--sync : Send synchronously (without queue)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test FCM notification to user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userIdentifier = $this->option('user');

        if (! $userIdentifier) {
            // Если пользователь не указан, берем первого с FCM токеном
            $user = User::whereHas('fcmTokens')->first();

            if (! $user) {
                $this->error('No user with FCM tokens found. Please specify user with --user option.');
                $this->info('Usage: php artisan fcm:test --user=user@example.com');

                return Command::FAILURE;
            }

            $this->info("Using user: {$user->email} (ID: {$user->id})");
        } else {
            // Поиск пользователя по email или ID
            $user = User::where('email', $userIdentifier)
                ->orWhere('id', $userIdentifier)
                ->first();

            if (! $user) {
                $this->error("User not found: {$userIdentifier}");

                return Command::FAILURE;
            }
        }

        // Проверка наличия FCM токенов
        $tokenCount = $user->fcmTokens()->count();
        if ($tokenCount === 0) {
            $this->warn("User {$user->email} has no FCM tokens registered.");
            $this->info('Make sure the mobile app has registered FCM token first.');

            return Command::FAILURE;
        }

        $this->info("User has {$tokenCount} FCM token(s) registered.");

        $title = $this->option('title');
        $body = $this->option('body');
        $type = $this->option('type');

        $this->info("Sending test notification...");
        $this->line("Title: {$title}");
        $this->line("Body: {$body}");
        $this->line("Type: {$type}");

        try {
            $data = [
                'test' => 'true',
                'timestamp' => now()->toIso8601String(),
            ];

            // For test notifications, create directly (bypass translation system)
            $notification = \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'data' => $data,
            ]);

            if ($this->option('sync')) {
                // Синхронная отправка (для тестирования)
                $fcmService = app(\App\Services\Firebase\FcmService::class);
                $fcmService->sendToUser($user, [
                    'title' => $title,
                    'body' => $body,
                ], array_merge(['type' => $type], array_map('strval', $data)));

                $this->info("✅ Notification sent synchronously!");
                $this->line("Notification ID: {$notification->id}");
            } else {
                // Асинхронная отправка через очередь
                \App\Jobs\SendNotificationJob::dispatch($notification->id);

                $this->info("✅ Notification queued!");
                $this->warn("Make sure queue worker is running: php artisan queue:work");
            }

            $this->newLine();
            $this->info("Check your device for the notification!");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to send notification: {$e->getMessage()}");
            $this->error($e->getTraceAsString());

            return Command::FAILURE;
        }
    }
}
