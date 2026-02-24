<?php

declare(strict_types=1);

return [
    'verify_email' => [
        'subject' => 'E-poçt ünvanını təsdiqləyin',
        'greeting' => 'Salam!',
        'line' => 'E-poçt ünvanınızı təsdiqləmək üçün aşağıdakı düyməyə klikləyin.',
        'action' => 'E-poçtu təsdiqlə',
        'footer' => 'Əgər siz hesab yaratmamısınızsa, heç bir əməliyyat tələb olunmur.',
        'salutation' => 'Hörmətlə, :app_name',
    ],
    'reset_password' => [
        'subject' => 'Şifrəni sıfırla',
        'greeting' => 'Salam!',
        'line' => 'Hesabınız üçün şifrə sıfırlama sorğusu aldığımız üçün bu e-poçtu alırsınız.',
        'action' => 'Şifrəni sıfırla',
        'expire_line' => 'Şifrə sıfırlama linki :minutes dəqiqə ərzində etibarsız olacaq.',
        'footer' => 'Əgər siz şifrə sıfırlama sorğusu göndərməmisinizsə, heç bir əməliyyat tələb olunmur.',
        'salutation' => 'Hörmətlə, :app_name',
    ],
    'admin_new_race_result' => [
        'subject' => 'Yeni yarış nəticəsi təsdiq gözləyir',
        'greeting' => 'Salam, Admin!',
        'line' => 'Atlet yeni yarış nəticəsi göndərdi, bu nəticə sizin təsdiqlənməyinizi gözləyir.',
        'athlete' => 'Atlet: :athlete_name',
        'race_type' => 'Yarış növü: :race_type',
        'location' => 'Yer: :location',
        'race_date' => 'Yarış tarixi: :race_date',
        'action' => 'Gözləyən nəticələrə baxın',
        'salutation' => 'Hörmətlə, :app_name',
    ],
    'admin_new_transfer_request' => [
        'subject' => 'Yeni nəticə köçürmə sorğusu',
        'greeting' => 'Salam, Admin!',
        'line' => 'Atlet yeni nəticə köçürmə sorğusu yaratdı, bu sorğu sizin rəyinizi gözləyir.',
        'requested_by' => 'Sorğu edən: :athlete_name',
        'source_athlete' => 'Atletdən köçürmə: :source_athlete_name',
        'action' => 'Köçürmə sorğularına baxın',
        'salutation' => 'Hörmətlə, :app_name',
    ],
];
