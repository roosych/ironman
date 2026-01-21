<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Notifications\SendNotificationAction;
use App\Events\RaceCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRaceCreatedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(RaceCreated $event): void
    {
        $raceResult = $event->raceResult;
        $user = $raceResult->profile?->user;

        if (! $user) {
            return;
        }

        SendNotificationAction::dispatch(
            $user,
            'Новый результат гонки',
            'Ваш результат отправлен на подтверждение администратором.',
            'race',
            [
                'race_id' => $raceResult->id,
                'action' => 'created',
            ]
        );
    }
}
