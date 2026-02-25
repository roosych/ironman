<?php

declare(strict_types=1);

return [
    'auth' => [
        'register_success' => 'Qeydiyyat uğurlu oldu.',
        'login_success' => 'Giriş uğurlu oldu.',
        'logout_success' => 'Çıxış uğurlu oldu.',
        'invalid_credentials' => 'Yanlış email və ya parol.',
        'unauthorized' => 'İcazə verilmir.',
        'email_not_verified' => 'Email təsdiqlənməyib. Lütfən emailinizi yoxlayın və ya yeni təsdiqləmə emaili istəyin.',
        'verification_sent' => 'Təsdiqləmə emaili göndərildi.',
        'email_already_verified' => 'Email artıq təsdiqlənib. Yenidən göndərməyə ehtiyac yoxdur.',
    ],
    'password' => [
        'reset_link_sent' => 'Parolu bərpa etmə emaili göndərildi.',
        'reset_success' => 'Parol uğurla dəyişdirildi.',
        'reset_failed' => 'Parolu bərpa etmək mümkün olmadı. Lütfən tokeni yoxlayın və yenidən cəhd edin.',
        'invalid_token' => 'Yanlış və ya vaxtı keçmiş parol bərpa tokeni. Lütfən yeni parol bərpa linki istəyin.',
        'user_not_found' => 'Bu emaillə istifadəçi tapılmadı.',
        'changed_success' => 'Parol uğurla dəyişdirildi.',
        'invalid_credentials' => 'Hazırkı parol yanlışdır.',
    ],
    'locale' => [
        'updated' => 'Dil uğurla yeniləndi.',
    ],
    'athlete' => [
        'not_found' => 'Atlet tapılmadı.',
    ],
    'policy' => [
        'not_found' => 'Siyasət tapılmadı.',
        'invalid_type' => 'Yanlış siyasət növü.',
    ],
    'validation' => [
        'country_iso_required' => 'Ölkə kodu tələb olunur.',
        'country_iso_invalid' => 'Ölkə kodu ISO formatında olmalıdır (məsələn: RU, AZ, BR).',
        'auth' => [
            'register' => [
                'name' => [
                    'required' => 'Ad tələb olunur.',
                ],
                'email' => [
                    'required' => 'Email tələb olunur.',
                    'email' => 'Düzgün email ünvanı daxil edin.',
                    'unique' => 'Verilmiş məlumatlarla qeydiyyatı tamamlamaq mümkün deyil.',
                ],
                'password' => [
                    'required' => 'Parol tələb olunur.',
                    'confirmed' => 'Parollar uyğun gəlmir.',
                ],
                'country_iso' => [
                    'required' => 'Ölkə kodu tələb olunur.',
                    'size' => 'Ölkə kodu 2 hərfdən ibarət olmalıdır.',
                    'regex' => 'Ölkə kodu ISO formatında olmalıdır (məsələn: RU, AZ, BR).',
                ],
            ],
            'login' => [
                'email' => [
                    'required' => 'Email tələb olunur.',
                    'email' => 'Düzgün email ünvanı daxil edin.',
                ],
                'password' => [
                    'required' => 'Parol tələb olunur.',
                ],
            ],
            'forgot_password' => [
                'email' => [
                    'required' => 'Email tələb olunur.',
                    'email' => 'Düzgün email ünvanı daxil edin.',
                ],
            ],
            'reset_password' => [
                'token' => [
                    'required' => 'Parol bərpa tokeni tələb olunur.',
                ],
                'email' => [
                    'required' => 'Email tələb olunur.',
                    'email' => 'Düzgün email ünvanı daxil edin.',
                    'exists' => 'Bu emaillə istifadəçi tapılmadı.',
                ],
                'password' => [
                    'required' => 'Parol tələb olunur.',
                    'confirmed' => 'Parollar uyğun gəlmir.',
                ],
            ],
        ],
        'user' => [
            'change_password' => [
                'current_password' => [
                    'required' => 'Hazırkı parol tələb olunur.',
                ],
                'new_password' => [
                    'required' => 'Yeni parol tələb olunur.',
                    'min' => 'Yeni parol ən azı 8 simvoldan ibarət olmalıdır.',
                    'different' => 'Yeni parol hazırkı paroldan fərqli olmalıdır.',
                    'confirmed' => 'Parol təsdiqi uyğun gəlmir.',
                ],
            ],
        ],
        'update_locale' => [
            'locale' => [
                'required' => 'Dil tələb olunur.',
                'in' => 'Yalnız bu dillər dəstəklənir: ru, en, az.',
            ],
        ],
    ],
    'profile' => [
        'updated' => 'Profil uğurla yeniləndi.',
        'country_iso_updated' => 'Ölkə kodu uğurla yeniləndi.',
        'not_found' => 'Profil tapılmadı.',
    ],
    'photo' => [
        'not_found_or_unauthorized' => 'Foto tapılmadı və ya sizə aid deyil.',
        'deleted' => 'Foto uğurla silindi.',
    ],
    'race_result' => [
        'not_found_or_not_approved' => 'Nəticə tapılmadı və ya təsdiqlənməyib.',
        'submitted_for_approval' => 'Nəticə administrator tərəfindən təsdiq üçün göndərildi.',
        'deleted' => 'Nəticə silindi.',
    ],
];