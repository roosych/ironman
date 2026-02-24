<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\RaceResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class NewRaceResultSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly RaceResult $raceResult
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Respect each admin's locale
        try {
            $notifiable->refresh();
            $locale = $notifiable->locale ?? config('app.locale', 'en');
        } catch (\Exception $e) {
            $locale = config('app.locale', 'en');
        }

        $originalLocale = App::getLocale();
        App::setLocale($locale);

        $profile = $this->raceResult->profile;
        $athleteName = $profile?->admin_full_name
            ?? $profile?->user?->name
            ?? '—';

        $raceType = $this->raceResult->race_type?->value ?? '—';
        $location = $this->raceResult->location ?? '—';
        $raceDate = $this->raceResult->race_date?->format('d.m.Y') ?? '—';

        $adminUrl = config('app.url') . '/admin/race-results/pending';

        $message = (new MailMessage)
            ->subject(trans('emails.admin_new_race_result.subject'))
            ->greeting(trans('emails.admin_new_race_result.greeting'))
            ->line(trans('emails.admin_new_race_result.line'))
            ->line(trans('emails.admin_new_race_result.athlete', ['athlete_name' => $athleteName]))
            ->line(trans('emails.admin_new_race_result.race_type', ['race_type' => $raceType]))
            ->line(trans('emails.admin_new_race_result.location', ['location' => $location]))
            ->line(trans('emails.admin_new_race_result.race_date', ['race_date' => $raceDate]))
            ->action(trans('emails.admin_new_race_result.action'), $adminUrl)
            ->salutation(trans('emails.admin_new_race_result.salutation', ['app_name' => config('app.name')]));

        App::setLocale($originalLocale);

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
