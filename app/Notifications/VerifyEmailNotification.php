<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

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

        $verificationUrl = $this->verificationUrl($notifiable);

        $message = (new MailMessage)
            ->subject(trans('emails.verify_email.subject'))
            ->greeting(trans('emails.verify_email.greeting'))
            ->line(trans('emails.verify_email.line'))
            ->action(trans('emails.verify_email.action'), $verificationUrl)
            ->line(trans('emails.verify_email.footer'))
            ->salutation(trans('emails.verify_email.salutation', ['app_name' => config('app.name')]));

        App::setLocale($originalLocale);

        return $message;
    }

    protected function verificationUrl(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
