<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_reviewer',
        'is_admin',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_reviewer' => 'boolean',
            'is_admin' => 'boolean',
        ];
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(UserPhoto::class);
    }

    public function avatar(): HasOne
    {
        return $this->hasOne(UserPhoto::class)->where('is_avatar', true);
    }

    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function resultTransferRequests(): HasMany
    {
        return $this->hasMany(ResultTransferRequest::class);
    }

    /**
     * Check if user is admin.
     * User is admin if is_admin is true OR profile has admin role.
     */
    public function isAdmin(): bool
    {
        if ($this->is_admin) {
            return true;
        }

        $profile = $this->profile;
        return $profile && $profile->isAdmin();
    }

    /**
     * Проверить, есть ли у пользователя активный (pending) запрос на перенос результатов.
     */
    public function hasPendingTransferRequest(): bool
    {
        return $this->resultTransferRequests()->pending()->exists();
    }

    /**
     * Проверить, есть ли у пользователя одобренный запрос на перенос результатов.
     */
    public function hasApprovedTransferRequest(): bool
    {
        return $this->resultTransferRequests()->approved()->exists();
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('hide_reviewer', function (Builder $query) {
            $query->where('is_reviewer', false);
        });
    }
}
