<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Always fetch fresh locale from database before sending email
        // Refresh user model to ensure we have the latest locale value
        try {
            $notifiable->refresh();
            $locale = $notifiable->locale ?? config('app.locale', 'en');
        } catch (\Exception $e) {
            // If refresh fails, use default locale
            $locale = config('app.locale', 'en');
        }
        
        $originalLocale = App::getLocale();
        App::setLocale($locale);

        $resetUrl = $this->resetUrl($notifiable);
        $expireMinutes = config('auth.passwords.users.expire', 60);

        $message = (new MailMessage)
            ->subject(trans('emails.reset_password.subject'))
            ->greeting(trans('emails.reset_password.greeting'))
            ->line(trans('emails.reset_password.line'))
            ->action(trans('emails.reset_password.action'), $resetUrl)
            ->line(trans('emails.reset_password.expire_line', ['minutes' => $expireMinutes]))
            ->line(trans('emails.reset_password.footer'))
            ->salutation(trans('emails.reset_password.salutation', ['app_name' => config('app.name')]));

        App::setLocale($originalLocale);

        return $message;
    }

    protected function resetUrl(object $notifiable): string
    {
        return config('app.frontend_url').'/reset-password?'.http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
