<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Notifications\SendNotificationAction;
use App\Events\TransferRequestApproved;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransferRequestApprovedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TransferRequestApproved $event): void
    {
        $transferRequest = $event->transferRequest;
        $transferRequest->load(['user', 'sourceAthlete']);

        $user = $transferRequest->user;
        $sourceAthlete = $transferRequest->sourceAthlete;

        if (!$user) {
            return;
        }

        // Получаем имя атлета-донора
        $athleteName = $sourceAthlete?->admin_full_name ?? 'Неизвестный атлет';
        $resultsCount = $sourceAthlete?->raceResults()->count() ?? 0;

        // Отправляем уведомление пользователю о том, что его запрос одобрен
        SendNotificationAction::dispatch(
            $user,
            'transfer_request_approved',
            'transfer',
            [
                'transfer_request_id' => $transferRequest->id,
                'action' => 'approved',
                'source_athlete_id' => $sourceAthlete?->id,
            ],
            [
                'athlete_name' => $athleteName,
                'results_count' => $resultsCount,
            ]
        );
    }
}