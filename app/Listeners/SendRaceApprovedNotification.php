<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Notifications\SendNotificationAction;
use App\Events\RaceApproved;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRaceApprovedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(RaceApproved $event): void
    {
        $raceResult = $event->raceResult;
        $user = $raceResult->profile?->user;

        if (! $user) {
            return;
        }

        $location = $raceResult->location ?? 'Не указано';
        $raceDate = $raceResult->race_date?->format('d.m.Y') ?? '';

        SendNotificationAction::dispatch(
            $user,
            'Результат подтвержден',
            "Ваш результат гонки {$location} ({$raceDate}) подтвержден администратором.",
            'race',
            [
                'race_id' => $raceResult->id,
                'action' => 'approved',
            ]
        );
    }
}
