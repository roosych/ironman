<?php

declare(strict_types=1);

namespace App\Services\Firebase;

use App\Models\FcmToken;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class FcmService
{
    private ?\Kreait\Firebase\Messaging $messaging = null;

    /**
     * Get Firebase Messaging instance.
     */
    private function getMessaging(): \Kreait\Firebase\Messaging
    {
        if ($this->messaging !== null) {
            return $this->messaging;
        }

        $credentialsPath = config('firebase.credentials');
        $projectId = config('firebase.project_id');

        if (! $projectId || ! $credentialsPath) {
            throw new \RuntimeException('Firebase credentials not configured. Please set FIREBASE_PROJECT_ID and FIREBASE_CREDENTIALS in .env');
        }

        // Handle both absolute paths and storage paths
        if (str_starts_with($credentialsPath, '/') || preg_match('/^[A-Z]:\\\\/', $credentialsPath)) {
            // Absolute path
            $fullPath = $credentialsPath;
        } elseif (str_starts_with($credentialsPath, 'storage/app/')) {
            // Path already includes storage/app/ prefix
            $fullPath = storage_path(str_replace('storage/app/', 'app/', $credentialsPath));
        } else {
            // Relative path (will be resolved by Storage::path)
            $fullPath = Storage::path($credentialsPath);
        }

        if (! file_exists($fullPath)) {
            throw new \RuntimeException("Firebase credentials file not found: {$fullPath}");
        }

        $factory = (new Factory)->withServiceAccount($fullPath);
        $this->messaging = $factory->createMessaging();

        return $this->messaging;
    }

    /**
     * Send push notification to a specific FCM token.
     *
     * @param  array<string, mixed>  $notification
     * @param  array<string, mixed>  $data
     */
    public function sendToToken(string $token, array $notification, array $data = []): bool
    {
        try {
            $messaging = $this->getMessaging();

            $messagePayload = [
                'token' => $token,
                'notification' => [
                    'title' => $notification['title'] ?? '',
                    'body' => $notification['body'] ?? '',
                ],
            ];

            if (! empty($data)) {
                $messagePayload['data'] = array_map('strval', $data);
            }

            $message = CloudMessage::fromArray($messagePayload);

            // Send message
            $messaging->send($message);

            return true;
        } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
            // Token not found or unregistered
            $this->invalidateToken($token);
            Log::warning('FCM token not found', [
                'token' => substr($token, 0, 20).'...',
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Kreait\Firebase\Exception\Messaging\InvalidArgument $e) {
            // Invalid token
            $this->invalidateToken($token);
            Log::warning('FCM token invalid', [
                'token' => substr($token, 0, 20).'...',
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error('Failed to send FCM notification', [
                'token' => substr($token, 0, 20).'...',
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Unexpected error sending FCM notification', [
                'token' => substr($token, 0, 20).'...',
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send push notification to all user's devices.
     *
     * @param  array<string, mixed>  $notification
     * @param  array<string, mixed>  $data
     */
    public function sendToUser(User $user, array $notification, array $data = []): int
    {
        $tokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($tokens)) {
            return 0;
        }

        $successCount = 0;
        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $notification, $data)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Send push notification to multiple tokens.
     *
     * @param  array<string>  $tokens
     * @param  array<string, mixed>  $notification
     * @param  array<string, mixed>  $data
     */
    public function sendToMany(array $tokens, array $notification, array $data = []): int
    {
        $successCount = 0;
        foreach ($tokens as $token) {
            if ($this->sendToToken($token, $notification, $data)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Invalidate FCM token (delete from database).
     */
    private function invalidateToken(string $token): void
    {
        FcmToken::where('token', $token)->delete();
        Log::info('FCM token invalidated and removed', [
            'token' => substr($token, 0, 20).'...',
        ]);
    }
}
