<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TransferRequestCreated;
use App\Models\User;
use App\Notifications\Admin\NewTransferRequestSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfNewTransferRequest implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TransferRequestCreated $event): void
    {
        $transferRequest = $event->transferRequest;
        $transferRequest->load(['user', 'sourceAthlete']);

        // The hide_reviewer global scope intentionally excludes reviewer accounts.
        // This is correct behaviour: reviewer accounts should not receive admin emails.
        $admins = User::where('is_admin', true)->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new NewTransferRequestSubmittedNotification($transferRequest));
    }
}
