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
            'Пароль изменен',
            'Пароль вашего аккаунта был успешно изменен. Все другие сессии завершены.',
            'security',
            [
                'action' => 'password_changed',
            ]
        );
    }
}
