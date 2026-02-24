<?php

declare(strict_types=1);

namespace App\Notifications\Admin;

use App\Models\ResultTransferRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;

class NewTransferRequestSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly ResultTransferRequest $transferRequest
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        try {
            $notifiable->refresh();
            $locale = $notifiable->locale ?? config('app.locale', 'en');
        } catch (\Exception $e) {
            $locale = config('app.locale', 'en');
        }

        $originalLocale = App::getLocale();
        App::setLocale($locale);

        $requester = $this->transferRequest->user;
        $athleteName = $requester?->name ?? '—';

        $sourceAthlete = $this->transferRequest->sourceAthlete;
        $sourceAthleteName = $sourceAthlete?->admin_full_name ?? '—';

        $adminUrl = config('app.url') . '/admin/transfer-requests';

        $message = (new MailMessage)
            ->subject(trans('emails.admin_new_transfer_request.subject'))
            ->greeting(trans('emails.admin_new_transfer_request.greeting'))
            ->line(trans('emails.admin_new_transfer_request.line'))
            ->line(trans('emails.admin_new_transfer_request.requested_by', ['athlete_name' => $athleteName]))
            ->line(trans('emails.admin_new_transfer_request.source_athlete', ['source_athlete_name' => $sourceAthleteName]))
            ->action(trans('emails.admin_new_transfer_request.action'), $adminUrl)
            ->salutation(trans('emails.admin_new_transfer_request.salutation', ['app_name' => config('app.name')]));

        App::setLocale($originalLocale);

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [];
    }
}
