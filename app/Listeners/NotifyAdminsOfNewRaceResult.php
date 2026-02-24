<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RaceCreated;
use App\Models\User;
use App\Notifications\Admin\NewRaceResultSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfNewRaceResult implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(RaceCreated $event): void
    {
        $raceResult = $event->raceResult;
        $raceResult->load(['profile.user']);

        // The hide_reviewer global scope intentionally excludes reviewer accounts.
        // This is correct behaviour: reviewer accounts should not receive admin emails.
        $admins = User::where('is_admin', true)->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new NewRaceResultSubmittedNotification($raceResult));
    }
}
