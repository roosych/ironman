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
    'policy' => [
        'not_found' => 'Policy not found.',
        'invalid_type' => 'Invalid policy type.',
    ],
    'validation' => [
        'country_iso_required' => 'Country code is required.',
        'country_iso_invalid' => 'Country code must be in ISO format (e.g., RU, AZ, BR).',
        'auth' => [
            'register' => [
                'name' => [
                    'required' => 'Name is required.',
                ],
                'email' => [
                    'required' => 'Email is required.',
                    'email' => 'Please enter a valid email address.',
                    'unique' => 'Unable to complete registration with the provided data.',
                ],
                'password' => [
                    'required' => 'Password is required.',
                    'confirmed' => 'Passwords do not match.',
                ],
                'country_iso' => [
                    'required' => 'Country code is required.',
                    'size' => 'Country code must be exactly 2 characters.',
                    'regex' => 'Country code must be in ISO format (e.g., RU, AZ, BR).',
                ],
            ],
            'login' => [
                'email' => [
                    'required' => 'Email is required.',
                    'email' => 'Please enter a valid email address.',
                ],
                'password' => [
                    'required' => 'Password is required.',
                ],
            ],
            'forgot_password' => [
                'email' => [
                    'required' => 'Email is required.',
                    'email' => 'Please enter a valid email address.',
                ],
            ],
            'reset_password' => [
                'token' => [
                    'required' => 'Password reset token is required.',
                ],
                'email' => [
                    'required' => 'Email is required.',
                    'email' => 'Please enter a valid email address.',
                    'exists' => 'User with this email not found.',
                ],
                'password' => [
                    'required' => 'Password is required.',
                    'confirmed' => 'Passwords do not match.',
                ],
            ],
        ],
        'user' => [
            'change_password' => [
                'current_password' => [
                    'required' => 'Current password is required.',
                ],
                'new_password' => [
                    'required' => 'New password is required.',
                    'min' => 'New password must be at least 8 characters.',
                    'different' => 'New password must be different from current password.',
                    'confirmed' => 'Password confirmation does not match.',
                ],
            ],
        ],
        'update_locale' => [
            'locale' => [
                'required' => 'Language is required.',
                'in' => 'Only the following languages are supported: ru, en, az.',
            ],
        ],
        'fcm' => [
            'store_token' => [
                'token' => [
                    'required' => 'FCM token is required.',
                    'string' => 'FCM token must be a string.',
                    'max' => 'FCM token must not exceed 500 characters.',
                ],
                'device_type' => [
                    'in' => 'Device type must be android or ios.',
                ],
                'device_name' => [
                    'max' => 'Device name must not exceed 255 characters.',
                ],
            ],
            'delete_token' => [
                'token' => [
                    'required' => 'FCM token is required.',
                    'string' => 'FCM token must be a string.',
                    'max' => 'FCM token must not exceed 500 characters.',
                ],
            ],
        ],
        'upcoming_race' => [
            'store' => [
                'race_id' => [
                    'required' => 'Race is required.',
                    'integer' => 'Race ID must be a number.',
                    'exists' => 'Selected race not found.',
                ],
            ],
        ],
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
            'set_avatar' => [
                'photo_id' => [
                    'required' => 'Photo ID is required.',
                    'integer' => 'Photo ID must be a number.',
                    'exists' => 'Photo not found or does not belong to you.',
                ],
            ],
            'upload_photo' => [
                'photos' => [
                    'required' => 'At least one photo must be uploaded.',
                    'array' => 'Photos must be provided as an array.',
                    'min' => 'At least one photo must be uploaded.',
                    'max' => 'You can upload no more than 10 photos at once.',
                    '*' => [
                        'required' => 'Each file must be an image.',
                        'image' => 'File must be an image.',
                        'mimes' => 'Allowed formats: jpeg, png, jpg, webp.',
                        'max' => 'File size must not exceed 5 MB.',
                    ],
                ],
            ],
        ],
        'transfer' => [
            'source_athlete_id' => [
                'required' => 'Source athlete ID is required.',
                'integer' => 'Source athlete ID must be an integer.',
                'exists' => 'Selected athlete not found or not available for result transfer.',
            ],
            'comment' => [
                'required' => 'Comment is required when rejecting a request.',
                'string' => 'Comment must be a string.',
                'max' => 'Comment must not exceed 1000 characters.',
            ],
        ],
    ],
    'profile' => [
        'updated' => 'Profile successfully updated.',
        'country_iso_updated' => 'Country code successfully updated.',
        'not_found' => 'Profile not found.',
    ],
    'upcoming_race' => [
        'created' => 'Upcoming race successfully added.',
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
    'transfer' => [
        'request_created' => 'Result transfer request successfully created.',
        'request_not_found' => 'Result transfer request not found.',
        'request_approved' => 'Result transfer request approved.',
        'request_rejected' => 'Result transfer request rejected.',
        'no_request' => 'You have no active result transfer requests.',
        'already_has_pending' => 'You already have an active result transfer request.',
        'already_has_approved' => 'You already have an approved result transfer request.',
        'athlete_not_available' => 'This athlete is not available for result transfer.',
        'athlete_has_pending' => 'This athlete already has an active result transfer request.',
    ],
];

