<?php

declare(strict_types=1);

return [
    'race_approved' => [
        'title' => 'Race Result Approved',
        'body' => 'Your race result :location (:race_date) has been approved by the administrator.',
    ],
    'race_approved_broadcast' => [
        'title' => 'New Race Result',
        'body' => ':athlete_name completed race :location (:race_date)',
    ],
    'race_created' => [
        'title' => 'New Race Result',
        'body' => 'Your result has been sent for administrator approval.',
    ],
    'profile_synced' => [
        'title' => 'Profile Synced',
        'body' => 'Your profile has been successfully linked to your account.',
    ],
    'password_changed' => [
        'title' => 'Password Changed',
        'body' => 'Your account password has been successfully changed. All other sessions have been terminated.',
    ],
    'transfer_request_approved' => [
        'title' => 'Transfer Request Approved',
        'body' => 'Your request to transfer :results_count result(s) from athlete ":athlete_name" has been approved.',
    ],
    'transfer_request_rejected' => [
        'title' => 'Transfer Request Rejected',
        'body' => 'Your request to transfer results from athlete ":athlete_name" has been rejected. Reason: :comment',
    ],
];

