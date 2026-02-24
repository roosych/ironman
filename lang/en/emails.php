<?php

declare(strict_types=1);

return [
    'verify_email' => [
        'subject' => 'Verify Email Address',
        'greeting' => 'Hello!',
        'line' => 'Please click the button below to verify your email address.',
        'action' => 'Verify Email',
        'footer' => 'If you did not create an account, no further action is required.',
        'salutation' => 'Regards, :app_name',
    ],
    'reset_password' => [
        'subject' => 'Reset Password',
        'greeting' => 'Hello!',
        'line' => 'You are receiving this email because we received a password reset request for your account.',
        'action' => 'Reset Password',
        'expire_line' => 'This password reset link will expire in :minutes minutes.',
        'footer' => 'If you did not request a password reset, no further action is required.',
        'salutation' => 'Regards, :app_name',
    ],
];

