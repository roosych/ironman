<?php

declare(strict_types=1);

return [
    'auth' => [
        'register_success' => 'Registration successful.',
        'login_success' => 'Login successful.',
        'logout_success' => 'Logout successful.',
        'invalid_credentials' => 'Invalid email or password.',
        'unauthorized' => 'Unauthorized.',
        'email_not_verified' => 'Your email is not verified. Please check your email or request a new verification email.',
        'verification_sent' => 'Verification email sent.',
        'email_already_verified' => 'Email is already verified. No need to resend the email.',
    ],
    'password' => [
        'reset_link_sent' => 'Password reset email sent.',
        'reset_success' => 'Password successfully changed.',
        'reset_failed' => 'Failed to reset password. Please check the token and try again.',
        'invalid_token' => 'Invalid or expired password reset token. Please request a new password reset link.',
        'user_not_found' => 'User with this email not found.',
        'changed_success' => 'Password successfully changed.',
        'invalid_credentials' => 'Current password is incorrect.',
    ],
    'locale' => [
        'updated' => 'Locale successfully updated.',
    ],
    'athlete' => [
        'not_found' => 'Athlete not found.',
        'no_photos' => 'No photos found for this athlete.',
    ],
    'validation' => [
        'country_iso_required' => 'Country code is required.',
        'country_iso_invalid' => 'Country code must be in ISO format (e.g., RU, AZ, BR).',
        'profile' => [
            'name' => [
                'max' => 'Name must not exceed 255 characters.',
            ],
            'role' => [
                'in' => 'Role must be one of: athlete, coach, admin.',
            ],
            'ironman_number' => [
                'integer' => 'Ironman number must be an integer.',
                'min' => 'Ironman number must be a positive number.',
            ],
            'country_iso' => [
                'string' => 'Country code must be a string.',
                'size' => 'Country code must be exactly 2 characters.',
                'regex' => 'Country code must be in ISO format (e.g., RU, AZ, BR).',
            ],
            'bio' => [
                'max' => 'Bio must not exceed 500 characters.',
            ],
            'social_links' => [
                'strava' => [
                    'url' => 'Strava link must be a valid URL.',
                ],
                'facebook' => [
                    'url' => 'Facebook link must be a valid URL.',
                ],
            ],
        ],
    ],
    'profile' => [
        'updated' => 'Profile successfully updated.',
        'country_iso_updated' => 'Country code successfully updated.',
        'not_found' => 'Profile not found.',
    ],
    'photo' => [
        'not_found_or_unauthorized' => 'Photo not found or does not belong to you.',
        'deleted' => 'Photo successfully deleted.',
    ],
    'race_result' => [
        'not_found_or_not_approved' => 'Result not found or not approved.',
        'submitted_for_approval' => 'Result submitted for admin approval.',
        'deleted' => 'Result deleted.',
    ],
];

