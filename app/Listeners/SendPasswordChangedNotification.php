<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Notifications\SendNotificationAction;
use App\Events\PasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPasswordChangedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(PasswordChanged $event): void
    {
        $user = $event->user;

        SendNotificationAction::dispatch(
            $user,
            'password_changed',
            'security',
            [
                'action' => 'password_changed',
            ]
        );
    }
}
