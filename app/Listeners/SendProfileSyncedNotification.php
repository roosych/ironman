<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Notifications\SendNotificationAction;
use App\Events\ProfileSynced;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendProfileSyncedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(ProfileSynced $event): void
    {
        $user = $event->user;

        SendNotificationAction::dispatch(
            $user,
            'Профиль синхронизирован',
            'Ваш профиль успешно привязан к аккаунту.',
            'profile',
            [
                'profile_id' => $event->profile->id,
                'action' => 'synced',
            ]
        );
    }
}
