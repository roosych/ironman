<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Actions\Notifications\SendNotificationAction;
use App\Events\TransferRequestRejected;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransferRequestRejectedNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TransferRequestRejected $event): void
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

        // Отправляем уведомление пользователю о том, что его запрос отклонен
        SendNotificationAction::dispatch(
            $user,
            'transfer_request_rejected',
            'transfer',
            [
                'transfer_request_id' => $transferRequest->id,
                'action' => 'rejected',
                'source_athlete_id' => $sourceAthlete?->id,
            ],
            [
                'athlete_name' => $athleteName,
                'comment' => $transferRequest->admin_comment ?? 'Без комментария',
            ]
        );
    }
}