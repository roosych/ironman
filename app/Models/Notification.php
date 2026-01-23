<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type',
        'data',
        'read_at',
        'translations',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
            'translations' => 'array',
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get title in specified locale or fallback to stored title.
     */
    public function getTitle(?string $locale = null): string
    {
        if ($locale && $this->translations && isset($this->translations[$locale]['title'])) {
            return $this->translations[$locale]['title'];
        }

        // Fallback to stored title (for backward compatibility)
        return $this->title;
    }

    /**
     * Get body in specified locale or fallback to stored body.
     */
    public function getBody(?string $locale = null): string
    {
        if ($locale && $this->translations && isset($this->translations[$locale]['body'])) {
            return $this->translations[$locale]['body'];
        }

        // Fallback to stored body (for backward compatibility)
        return $this->body;
    }

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if ($this->isRead()) {
            return false;
        }

        return $this->update(['read_at' => now()]);
    }
}
