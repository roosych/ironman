<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * Кастомная модель токена Sanctum, которая отключает глобальные скоупы
 * при загрузке связанного пользователя. Это нужно, чтобы ревьюер
 * (is_reviewer = true) мог аутентифицироваться по токену.
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    /**
     * Override tokenable relation to avoid global scopes on User.
     */
    public function tokenable(): MorphTo
    {
        return parent::tokenable()->withoutGlobalScopes();
    }
}

