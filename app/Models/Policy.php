<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'policies';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type',
        'language',
        'title',
        'content',
        'is_active',
        'effective_date',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Policy types available in the system.
     */
    public const TYPES = [
        'privacy' => 'Privacy Policy',
        'terms' => 'Terms of Service',
        'data_processing' => 'Data Processing Agreement',
        'cookies' => 'Cookie Policy',
    ];

    /**
     * Supported languages.
     */
    public const SUPPORTED_LANGUAGES = ['en', 'ru', 'az'];

    /**
     * Get the human-readable type name.
     */
    protected function typeName(): Attribute
    {
        return Attribute::make(
            get: fn (): string => self::TYPES[$this->type] ?? $this->type,
        );
    }

    /**
     * Scope to filter by policy type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to filter by language.
     */
    public function scopeForLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope to get only active policies.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the latest active policy for a specific type and language.
     */
    public static function getLatestActive(string $type, string $language): ?self
    {
        return self::ofType($type)
            ->forLanguage($language)
            ->active()
            ->orderByDesc('effective_date')
            ->first();
    }
}
