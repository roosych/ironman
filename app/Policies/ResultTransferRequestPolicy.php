<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ResultTransferRequest;
use App\Models\User;

class ResultTransferRequestPolicy
{
    /**
     * Может ли пользователь просматривать любые запросы на перенос результатов.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Может ли пользователь просматривать конкретный запрос на перенос результатов.
     */
    public function view(User $user, ResultTransferRequest $request): bool
    {
        // Администратор может просматривать любые запросы
        if ($user->isAdmin()) {
            return true;
        }

        // Пользователь может просматривать только свои запросы
        return $request->user_id === $user->id;
    }

    /**
     * Может ли пользователь создать запрос на перенос результатов.
     */
    public function create(User $user): bool
    {
        // Пользователь должен быть аутентифицированным
        // и не должен иметь активного или одобренного запроса
        return !$user->hasPendingTransferRequest() && !$user->hasApprovedTransferRequest();
    }

    /**
     * Может ли пользователь обновить запрос на перенос результатов.
     */
    public function update(User $user, ResultTransferRequest $request): bool
    {
        // Только администратор может обновлять запросы (для одобрения/отклонения)
        return $user->isAdmin();
    }

    /**
     * Может ли пользователь удалить запрос на перенос результатов.
     */
    public function delete(User $user, ResultTransferRequest $request): bool
    {
        // Пользователь может удалить только свой pending запрос
        if ($request->user_id === $user->id && $request->status->isPending()) {
            return true;
        }

        // Администратор может удалить любой запрос
        return $user->isAdmin();
    }

    /**
     * Может ли пользователь одобрить запрос на перенос результатов.
     */
    public function approve(User $user, ResultTransferRequest $request): bool
    {
        return $user->isAdmin() && $request->canBeApproved();
    }

    /**
     * Может ли пользователь отклонить запрос на перенос результатов.
     */
    public function reject(User $user, ResultTransferRequest $request): bool
    {
        return $user->isAdmin() && $request->canBeRejected();
    }

    /**
     * Может ли пользователь просматривать статистику запросов.
     */
    public function viewStats(User $user): bool
    {
        return $user->isAdmin();
    }
}