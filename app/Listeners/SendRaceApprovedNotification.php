<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Notifications\SendNotificationAction;
use App\Events\RaceApproved;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRaceApprovedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(RaceApproved $event): void
    {
        $raceResult = $event->raceResult;
        $raceResult->load(['profile.user']);
        $profile = $raceResult->profile;
        $athleteUser = $profile?->user;

        $location = $raceResult->location ?? '';
        $raceDate = $raceResult->race_date?->format('d.m.Y') ?? '';
        
        // Получаем имя атлета: сначала из профиля, потом из пользователя
        $athleteName = $profile?->admin_full_name 
            ?? $athleteUser?->name 
            ?? 'Атлет';

        // Отправляем уведомление самому атлету
        if ($athleteUser) {
            SendNotificationAction::dispatch(
                $athleteUser,
                'race_approved',
                'race',
                [
                    'race_id' => $raceResult->id,
                    'action' => 'approved',
                ],
                [
                    'location' => $location,
                    'race_date' => $raceDate,
                ]
            );
        }

        // Отправляем уведомление всем остальным пользователям
        $allUsers = User::withoutGlobalScope('hide_reviewer')
            ->where('is_admin', false)
            ->where('is_reviewer', false)
            ->get();

        foreach ($allUsers as $user) {
            // Пропускаем самого атлета
            if ($athleteUser && $user->id === $athleteUser->id) {
                continue;
            }

            SendNotificationAction::dispatch(
                $user,
                'race_approved_broadcast',
                'race',
                [
                    'race_id' => $raceResult->id,
                    'action' => 'approved_broadcast',
                    'athlete_id' => $athleteUser?->id,
                    'profile_id' => $profile?->id,
                ],
                [
                    'athlete_name' => $athleteName,
                    'location' => $location,
                    'race_date' => $raceDate,
                ]
            );
        }
    }
}
