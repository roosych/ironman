<?php

declare(strict_types=1);

namespace App\Actions\Notifications;

use App\Models\Notification;
use App\Models\User;
use App\Services\Firebase\FcmService;
use Illuminate\Support\Facades\App;

class SendNotificationAction
{
    public function __construct(
        private readonly FcmService $fcmService
    ) {}

    /**
     * Get user locale from database or fallback to default.
     * Always fetches fresh locale from database to ensure we use the latest value.
     * This method reloads the user from database by ID to ensure we have the latest locale.
     */
    private function getUserLocale(User $user): string
    {
        // Always fetch fresh user from database by ID before sending notification
        // This ensures we use the most up-to-date locale, even if the user
        // was passed through an event or queue job (which may serialize/deserialize the model)
        try {
            // Get user ID first (before any potential refresh issues)
            $userId = $user->id;
            
            if (! $userId) {
                \Illuminate\Support\Facades\Log::warning('User has no ID when getting locale for notification');
                return config('app.locale', 'en');
            }
            
            // Reload user from database by ID to get fresh data
            // Use withoutGlobalScopes to ensure we can find the user even if scopes are applied
            $freshUser = User::withoutGlobalScopes()->find($userId);
            
            if (! $freshUser) {
                \Illuminate\Support\Facades\Log::warning('User not found in database when getting locale for notification', [
                    'user_id' => $userId,
                ]);
                return config('app.locale', 'en');
            }
            
            // Get locale from fresh user model
            $locale = $freshUser->locale;
            
            // If locale is not set, use default
            if (empty($locale)) {
                \Illuminate\Support\Facades\Log::debug('User has no locale set, using default', [
                    'user_id' => $userId,
                ]);
                return config('app.locale', 'en');
            }
            
            \Illuminate\Support\Facades\Log::debug('Using user locale for notification', [
                'user_id' => $userId,
                'locale' => $locale,
            ]);
            
            return $locale;
        } catch (\Exception $e) {
            // If anything fails, fallback to default locale
            \Illuminate\Support\Facades\Log::error('Error getting user locale for notification', [
                'user_id' => $user->id ?? null,
                'error' => $e->getMessage(),
            ]);
            return config('app.locale', 'en');
        }
    }

    /**
     * Translate notification text using user's locale.
     *
     * @param  array<string, mixed>  $params
     */
    private function translate(string $key, string $locale, array $params = []): string
    {
        $originalLocale = App::getLocale();
        App::setLocale($locale);

        $translated = trans($key, $params);

        App::setLocale($originalLocale);

        return $translated;
    }

    /**
     * Translate notification for all supported locales.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, array<string, string>>
     */
    private function translateForAllLocales(string $translationKey, array $params = []): array
    {
        $translations = [];
        $supportedLocales = ['ru', 'en'];

        foreach ($supportedLocales as $locale) {
            $translations[$locale] = [
                'title' => $this->translate("notifications.{$translationKey}.title", $locale, $params),
                'body' => $this->translate("notifications.{$translationKey}.body", $locale, $params),
            ];
        }

        return $translations;
    }

    /**
     * Create notification (DB) and send via Job (async).
     *
     * @param  array<string, mixed>  $data
     */
    public static function dispatch(
        User $user,
        string $translationKey,
        string $type,
        array $data = [],
        array $translationParams = []
    ): void {
        $action = app(self::class);
        
        // Get user locale from database to determine which translation to use as default
        $userLocale = $action->getUserLocale($user);
        
        \Illuminate\Support\Facades\Log::info('Sending notification', [
            'user_id' => $user->id,
            'translation_key' => $translationKey,
            'user_locale' => $userLocale,
            'type' => $type,
        ]);

        // Translate for all supported locales and store in JSON
        $translations = $action->translateForAllLocales($translationKey, $translationParams);
        
        // Use user's locale for title/body fields (for backward compatibility)
        $title = $translations[$userLocale]['title'] ?? $translations['en']['title'];
        $body = $translations[$userLocale]['body'] ?? $translations['en']['body'];
        
        \Illuminate\Support\Facades\Log::debug('Notification translated for all locales', [
            'user_id' => $user->id,
            'user_locale' => $userLocale,
            'translations' => array_keys($translations),
        ]);

        // Create notification in DB with translations for all languages
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title, // Default title for backward compatibility
            'body' => $body, // Default body for backward compatibility
            'type' => $type,
            'data' => $data,
            'translations' => $translations, // Store all translations in JSON
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
        string $translationKey,
        string $type,
        array $data = [],
        array $translationParams = []
    ): Notification {
        $userLocale = $this->getUserLocale($user);

        // Translate for all supported locales and store in JSON
        $translations = $this->translateForAllLocales($translationKey, $translationParams);
        
        // Use user's locale for title/body fields (for backward compatibility)
        $title = $translations[$userLocale]['title'] ?? $translations['en']['title'];
        $body = $translations[$userLocale]['body'] ?? $translations['en']['body'];

        // Create notification in database with translations for all languages
        $notification = Notification::create([
            'user_id' => $user->id,
            'title' => $title, // Default title for backward compatibility
            'body' => $body, // Default body for backward compatibility
            'type' => $type,
            'data' => $data,
            'translations' => $translations, // Store all translations in JSON
        ]);

        // Prepare FCM notification payload using user's locale
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

