<?php

declare(strict_types=1);

return [
    'race_approved' => [
        'title' => 'Результат подтвержден',
        'body' => 'Ваш результат гонки :location (:race_date) подтвержден администратором.',
    ],
    'race_approved_broadcast' => [
        'title' => 'Новый результат',
        'body' => ':athlete_name прошел гонку :location (:race_date)',
    ],
    'race_created' => [
        'title' => 'Новый результат гонки',
        'body' => 'Ваш результат отправлен на подтверждение администратором.',
    ],
    'profile_synced' => [
        'title' => 'Профиль синхронизирован',
        'body' => 'Ваш профиль успешно привязан к аккаунту.',
    ],
    'password_changed' => [
        'title' => 'Пароль изменен',
        'body' => 'Пароль вашего аккаунта был успешно изменен. Все другие сессии завершены.',
    ],
    'transfer_request_approved' => [
        'title' => 'Запрос на трансфер одобрен',
        'body' => 'Ваш запрос на перенос :results_count результат(ов) от атлета ":athlete_name" был одобрен.',
    ],
    'transfer_request_rejected' => [
        'title' => 'Запрос на трансфер отклонен',
        'body' => 'Ваш запрос на перенос результатов от атлета ":athlete_name" был отклонен. Причина: :comment',
    ],
];

