<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ResultTransferStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResultTransferRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'source_athlete_id',
        'status',
        'reviewed_by',
        'reviewed_at',
        'comment',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ResultTransferStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * Связь с пользователем, который подал запрос.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Связь с профилем атлета-донора (источник результатов).
     */
    public function sourceAthlete(): BelongsTo
    {
        return $this->belongsTo(UserProfile::class, 'source_athlete_id');
    }

    /**
     * Связь с администратором, который рассмотрел запрос.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope для фильтрации по статусу "в ожидании".
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ResultTransferStatus::PENDING);
    }

    /**
     * Scope для фильтрации по статусу "одобрен".
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ResultTransferStatus::APPROVED);
    }

    /**
     * Scope для фильтрации по статусу "отклонён".
     */
    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ResultTransferStatus::REJECTED);
    }

    /**
     * Scope для фильтрации рассмотренных запросов.
     */
    public function scopeReviewed(Builder $query): Builder
    {
        return $query->whereIn('status', [ResultTransferStatus::APPROVED, ResultTransferStatus::REJECTED]);
    }

    /**
     * Проверить, можно ли одобрить запрос.
     */
    public function canBeApproved(): bool
    {
        return $this->status->isPending() && $this->sourceAthlete->results_transferred === false;
    }

    /**
     * Проверить, можно ли отклонить запрос.
     */
    public function canBeRejected(): bool
    {
        return $this->status->isPending();
    }
}