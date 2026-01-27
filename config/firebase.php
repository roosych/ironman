<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration supports multiple environments (dev, staging, prod).
    | The system automatically selects the correct Firebase project and
    | credentials based on the APP_ENV setting.
    |
    */

    'project_id' => env('FIREBASE_PROJECT_ID'),
    'credentials' => env('FIREBASE_CREDENTIALS', 'storage/app/firebase/firebase.json'),

    /*
    |--------------------------------------------------------------------------
    | Environment-Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for different environments. The system will automatically
    | use the configuration matching your APP_ENV setting.
    |
    */

    'environments' => [
        'local' => [
            'project_id' => env('FIREBASE_DEV_PROJECT_ID'),
            'credentials' => env('FIREBASE_DEV_CREDENTIALS', 'storage/app/firebase/dev/firebase-dev.json'),
        ],
        'development' => [
            'project_id' => env('FIREBASE_DEV_PROJECT_ID'),
            'credentials' => env('FIREBASE_DEV_CREDENTIALS', 'storage/app/firebase/dev/firebase-dev.json'),
        ],
        'staging' => [
            'project_id' => env('FIREBASE_STAGING_PROJECT_ID'),
            'credentials' => env('FIREBASE_STAGING_CREDENTIALS', 'storage/app/firebase/staging/firebase-staging.json'),
        ],
        'production' => [
            'project_id' => env('FIREBASE_PROD_PROJECT_ID', env('FIREBASE_PROJECT_ID')),
            'credentials' => env('FIREBASE_PROD_CREDENTIALS', env('FIREBASE_CREDENTIALS')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto Environment Detection
    |--------------------------------------------------------------------------
    |
    | Automatically select environment-specific configuration based on APP_ENV.
    | If environment-specific config exists, it will override the default.
    |
    */

    'auto_env_config' => function () {
        $env = config('app.env', 'production');
        $environments = config('firebase.environments', []);

        if (isset($environments[$env])) {
            return [
                'project_id' => $environments[$env]['project_id'] ?? config('firebase.project_id'),
                'credentials' => $environments[$env]['credentials'] ?? config('firebase.credentials'),
            ];
        }

        return [
            'project_id' => config('firebase.project_id'),
            'credentials' => config('firebase.credentials'),
        ];
    },
];

