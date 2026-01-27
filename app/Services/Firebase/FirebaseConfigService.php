<?php

declare(strict_types=1);

namespace App\Services\Firebase;

use Illuminate\Support\Facades\Config;

/**
 * Firebase Configuration Service
 *
 * Provides environment-aware Firebase configuration for different deployment stages.
 * Automatically selects the correct Firebase project and credentials based on APP_ENV.
 */
class FirebaseConfigService
{
    /**
     * Get Firebase configuration for the current environment.
     *
     * @return array{project_id: string|null, credentials: string|null}
     */
    public static function getConfig(): array
    {
        $env = config('app.env', 'production');
        $environments = config('firebase.environments', []);

        // If environment-specific config exists, use it
        if (isset($environments[$env])) {
            return [
                'project_id' => $environments[$env]['project_id'] ?? config('firebase.project_id'),
                'credentials' => $environments[$env]['credentials'] ?? config('firebase.credentials'),
            ];
        }

        // Fallback to default configuration
        return [
            'project_id' => config('firebase.project_id'),
            'credentials' => config('firebase.credentials'),
        ];
    }

    /**
     * Get Firebase project ID for the current environment.
     */
    public static function getProjectId(): ?string
    {
        $config = self::getConfig();
        return $config['project_id'];
    }

    /**
     * Get Firebase credentials path for the current environment.
     */
    public static function getCredentialsPath(): ?string
    {
        $config = self::getConfig();
        return $config['credentials'];
    }

    /**
     * Get Firebase configuration for a specific environment.
     *
     * @param string $environment Environment name (local, development, staging, production)
     * @return array{project_id: string|null, credentials: string|null}
     */
    public static function getConfigForEnvironment(string $environment): array
    {
        $environments = config('firebase.environments', []);

        if (isset($environments[$environment])) {
            return [
                'project_id' => $environments[$environment]['project_id'],
                'credentials' => $environments[$environment]['credentials'],
            ];
        }

        return [
            'project_id' => null,
            'credentials' => null,
        ];
    }

    /**
     * Check if Firebase is configured for the current environment.
     */
    public static function isConfigured(): bool
    {
        $config = self::getConfig();
        return !empty($config['project_id']) && !empty($config['credentials']);
    }

    /**
     * Get all available Firebase environments.
     *
     * @return array<string, array>
     */
    public static function getAvailableEnvironments(): array
    {
        return config('firebase.environments', []);
    }

    /**
     * Get current environment name.
     */
    public static function getCurrentEnvironment(): string
    {
        return config('app.env', 'production');
    }
}