<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Admin User Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the admin user used for admin panel access.
    |
    */

    'email' => env('ADMIN_EMAIL', 'admin@example.com'),
    'password' => env('ADMIN_PASSWORD', 'FocusPocus'),
    'name' => env('ADMIN_NAME', 'Admin'),
];

