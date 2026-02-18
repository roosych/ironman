<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\ResultTransferStatus;
use App\Models\RaceResult;
use App\Models\ResultTransferRequest;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ResultTransferService
{
    /**
     * Получить атлетов, доступных для переноса результатов.
     * Возвращает атлетов где results_transferred = false и нет pending запросов.
     */
    public function getEligibleAthletes(): Collection
    {
        // Получаем ID профилей, для которых есть pending запросы
        $pendingAthleteIds = ResultTransferRequest::pending()
            ->pluck('source_athlete_id')
            ->toArray();

        return UserProfile::with([
            'user.avatar',
            'raceResults:id,user_profile_id,race_type',
        ])
            ->where('role', 'athlete')
            ->where('results_transferred', false)
            ->whereNull('user_id')
            ->whereNotIn('id', $pendingAthleteIds)
            ->get();
    }

    /**
     * Создать запрос на перенос результатов.
     *
     * @throws InvalidArgumentException
     */
    public function createRequest(User $user, int $sourceAthleteId): ResultTransferRequest
    {
        return DB::transaction(function () use ($user, $sourceAthleteId) {
            // Проверяем, что у пользователя нет активного запроса
            if ($user->hasPendingTransferRequest()) {
                throw new InvalidArgumentException('У пользователя уже есть активный запрос на перенос результатов.');
            }

            // Проверяем, что у пользователя нет одобренного запроса
            if ($user->hasApprovedTransferRequest()) {
                throw new InvalidArgumentException('У пользователя уже был одобрен запрос на перенос результатов.');
            }

            // Проверяем существование атлета-донора
            $sourceAthlete = UserProfile::where('id', $sourceAthleteId)
                ->where('role', 'athlete')
                ->where('results_transferred', false)
                ->first();

            if (!$sourceAthlete) {
                throw new InvalidArgumentException('Атлет не найден или его результаты уже перенесены.');
            }

            // Проверяем, что для этого атлета нет pending запроса
            if (ResultTransferRequest::pending()->where('source_athlete_id', $sourceAthleteId)->exists()) {
                throw new InvalidArgumentException('Для этого атлета уже есть активный запрос на перенос результатов.');
            }

            // Создаем запрос
            return ResultTransferRequest::create([
                'user_id' => $user->id,
                'source_athlete_id' => $sourceAthleteId,
                'status' => ResultTransferStatus::PENDING,
            ]);
        });
    }

    /**
     * Одобрить запрос на перенос результатов.
     * Копирует результаты от донора к получателю и помечает донора как transferred.
     *
     * @throws InvalidArgumentException
     */
    public function approveRequest(ResultTransferRequest $request, User $admin, ?string $comment = null): ResultTransferRequest
    {
        return DB::transaction(function () use ($request, $admin, $comment) {
            // Перезагружаем запрос с блокировкой строки
            $lockedRequest = ResultTransferRequest::lockForUpdate()->find($request->id);

            if (!$lockedRequest) {
                throw new InvalidArgumentException('Запрос не найден.');
            }

            if (!$lockedRequest->canBeApproved()) {
                throw new InvalidArgumentException('Запрос не может быть одобрен. Проверьте статус запроса и атлета-донора.');
            }

            // Блокируем строку атлета-донора
            $sourceAthlete = UserProfile::lockForUpdate()->find($lockedRequest->source_athlete_id);

            if (!$sourceAthlete || $sourceAthlete->results_transferred) {
                throw new InvalidArgumentException('Атлет-донор не найден или его результаты уже перенесены.');
            }

            // Получаем профиль получателя или создаем его, если не существует
            $targetUser = $lockedRequest->user;
            $targetProfile = $targetUser->profile;

            if (!$targetProfile) {
                $targetProfile = $targetUser->profile()->create([
                    'role' => 'athlete',
                    'admin_full_name' => $targetUser->name,
                ]);
            }

            // Получаем результаты донора
            $sourceResults = RaceResult::where('user_profile_id', $sourceAthlete->id)->get();

            // Копируем результаты донора к получателю
            foreach ($sourceResults as $result) {
                // Проверяем, нет ли уже такого результата у получателя (защита от дублирования)
                $existingResult = RaceResult::where('user_profile_id', $targetProfile->id)
                    ->where('race_date', $result->race_date)
                    ->where('location', $result->location)
                    ->where('race_type', $result->race_type)
                    ->first();

                if (!$existingResult) {
                    RaceResult::create([
                        'user_profile_id' => $targetProfile->id,
                        'race_date' => $result->race_date,
                        'location' => $result->location,
                        'race_type' => $result->race_type,
                        'swim_time' => $result->swim_time,
                        't1_time' => $result->t1_time,
                        'bike_time' => $result->bike_time,
                        't2_time' => $result->t2_time,
                        'run_time' => $result->run_time,
                        'total_time' => $result->total_time,
                        'age_group' => $result->age_group,
                        'overall_position' => $result->overall_position,
                        'age_group_position' => $result->age_group_position,
                        'is_approved' => $result->is_approved,
                        'approved_at' => $result->approved_at,
                        'approved_by' => $result->approved_by,
                    ]);
                }
            }

            // Помечаем донора как перенесенного
            $sourceAthlete->update(['results_transferred' => true]);

            // Обновляем запрос
            $lockedRequest->update([
                'status' => ResultTransferStatus::APPROVED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'comment' => $comment,
            ]);

            return $lockedRequest->fresh();
        });
    }

    /**
     * Отклонить запрос на перенос результатов.
     *
     * @throws InvalidArgumentException
     */
    public function rejectRequest(ResultTransferRequest $request, User $admin, ?string $comment = null): ResultTransferRequest
    {
        return DB::transaction(function () use ($request, $admin, $comment) {
            // Перезагружаем запрос с блокировкой строки
            $lockedRequest = ResultTransferRequest::lockForUpdate()->find($request->id);

            if (!$lockedRequest) {
                throw new InvalidArgumentException('Запрос не найден.');
            }

            if (!$lockedRequest->canBeRejected()) {
                throw new InvalidArgumentException('Запрос не может быть отклонён.');
            }

            // Обновляем запрос
            $lockedRequest->update([
                'status' => ResultTransferStatus::REJECTED,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
                'comment' => $comment,
            ]);

            return $lockedRequest->fresh();
        });
    }

    /**
     * Получить запросы на перенос для админки с пагинацией.
     */
    public function getRequestsForAdmin(?string $status = null, int $perPage = 20)
    {
        $query = ResultTransferRequest::with([
            'user:id,name,email',
            'sourceAthlete:id,admin_full_name',
            'reviewedBy:id,name',
        ])->orderByDesc('created_at');

        if ($status && in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }
}