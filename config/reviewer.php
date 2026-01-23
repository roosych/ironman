<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Reviewer User Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the hidden reviewer user used for testing purposes.
    | This user is hidden from normal queries via global scope.
    |
    */

    'email' => env('REVIEWER_EMAIL', 'reviewer@example.com'),
    'password' => env('REVIEWER_PASSWORD', 'password'),
    'name' => env('REVIEWER_NAME', 'Reviewer User'),
    
    /*
    |--------------------------------------------------------------------------
    | Reviewer Profile Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the reviewer user profile.
    |
    */
    
    'profile' => [
        'external_id' => env('REVIEWER_PROFILE_EXTERNAL_ID', 'reviewer_user'),
        'role' => env('REVIEWER_PROFILE_ROLE', 'athlete'),
        'ironman_number' => env('REVIEWER_PROFILE_IRONMAN_NUMBER', 0),
        'admin_full_name' => env('REVIEWER_PROFILE_NAME', 'Reviewer User'),
    ],
];

