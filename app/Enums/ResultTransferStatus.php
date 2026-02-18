<?php

declare(strict_types=1);

namespace App\Enums;

enum ResultTransferStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /**
     * Получить локализованный заголовок статуса.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('result_transfer.status.pending'),
            self::APPROVED => __('result_transfer.status.approved'),
            self::REJECTED => __('result_transfer.status.rejected'),
        };
    }

    /**
     * Получить цвет для статуса в админке.
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'orange',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
        };
    }

    /**
     * Проверить, является ли статус "в ожидании".
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Проверить, является ли статус "одобрен".
     */
    public function isApproved(): bool
    {
        return $this === self::APPROVED;
    }

    /**
     * Проверить, является ли статус "отклонён".
     */
    public function isRejected(): bool
    {
        return $this === self::REJECTED;
    }
}