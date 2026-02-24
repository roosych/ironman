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
    'admin_new_race_result' => [
        'subject' => 'New Race Result Awaiting Approval',
        'greeting' => 'Hello, Admin!',
        'line' => 'A new race result has been submitted and requires your approval.',
        'athlete' => 'Athlete: :athlete_name',
        'race_type' => 'Race type: :race_type',
        'location' => 'Location: :location',
        'race_date' => 'Race date: :race_date',
        'action' => 'View Pending Results',
        'salutation' => 'Regards, :app_name',
    ],
    'admin_new_transfer_request' => [
        'subject' => 'New Result Transfer Request',
        'greeting' => 'Hello, Admin!',
        'line' => 'A new result transfer request has been submitted and requires your review.',
        'requested_by' => 'Requested by: :athlete_name',
        'source_athlete' => 'Transfer from athlete: :source_athlete_name',
        'action' => 'View Transfer Requests',
        'salutation' => 'Regards, :app_name',
    ],
];
