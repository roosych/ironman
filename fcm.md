Backend: Notifications system + FCM (Laravel)
Контекст проекта

Backend: Laravel

Auth: Laravel Sanctum

Push: Firebase Cloud Messaging

FCM пакет уже установлен

Jobs / Actions в проекте есть, но нужно проверить корректность и при необходимости доработать

Уведомления нужны для мобильного приложения

Уведомления должны сохраняться в БД, а FCM использоваться только как транспорт доставки

🎯 Цель

Реализовать полноценную систему уведомлений:

хранение уведомлений в базе

API для мобильного клиента

интеграция с FCM

поддержка unread / read

возможность навигации по data payload

1️⃣ Миграции и модели
Создать миграцию notifications
notifications
- id
- user_id (FK → users.id, cascade)
- title (string)
- body (text)
- type (string) // system, race, result, upcoming, admin
- data (json, nullable) // race_id, profile_id, screen, etc
- read_at (timestamp, nullable)
- created_at
- updated_at

Создать модель Notification

$fillable: user_id, title, body, type, data, read_at

$casts: data => array, read_at => datetime

Связь:

Notification belongsTo User


Добавить в User:

public function notifications(): HasMany

2️⃣ API Endpoints (Sanctum protected)
Получить список уведомлений
GET /api/v1/notifications


Требования:

только для авторизованного пользователя

сортировка: latest()

пагинация (15)

вернуть unread_count

Пример ответа:

{
  "success": true,
  "data": [...],
  "meta": {
    "unread_count": 3
  }
}

Пометить уведомление как прочитанное
POST /api/v1/notifications/{id}/read


Проверять, что уведомление принадлежит пользователю

Записывать read_at = now()

Пометить все как прочитанные
POST /api/v1/notifications/read-all

3️⃣ FCM integration (проверить и доработать)
Проверить:

Где и как хранится fcm_token

Есть ли:

users.fcm_token или

отдельная таблица user_devices

👉 Если нет — реализовать user_devices:

user_devices
- id
- user_id
- fcm_token
- platform (android / ios)
- last_used_at
- created_at

API для регистрации FCM токена
POST /api/v1/devices
{
  "fcm_token": "...",
  "platform": "android"
}


token должен быть уникальным

один пользователь → много устройств

4️⃣ Архитектура отправки уведомлений
Создать Action
App\Actions\Notifications\SendNotificationAction


Вход:

(user, title, body, type, data = [])


Логика:

Создать запись в notifications

Отправить FCM push всем устройствам пользователя

Использовать data payload, не только notification

Использовать Job
SendNotificationJob


Job должен быть ShouldQueue

Action вызывается из Job

Job должен быть безопасным (retry, timeout)

5️⃣ FCM Payload (обязательно)
{
  "notification": {
    "title": "Новая гонка",
    "body": "Вы добавили upcoming Ironman"
  },
  "data": {
    "type": "race",
    "race_id": "12",
    "screen": "race_details"
  }
}

6️⃣ Использование в системе (пример)

При создании upcoming гонки:

создателю → уведомление

позже (в будущем) → подписчикам

SendNotificationAction::run(
  $user,
  'Новая гонка',
  'Вы добавили upcoming Ironman',
  'upcoming',
  ['race_id' => $race->id]
);

7️⃣ Безопасность и best practices

Уведомления доступны только владельцу

FCM ошибки логировать, но не ломать Job

При logout:

можно удалять device

При смене пароля:

Sanctum уже инвалидирует токены (OK)

8️⃣ Что НЕ делать

❌ Не хранить уведомления только в FCM
❌ Не полагаться на push как источник данных
❌ Не возвращать уведомления без пагинации

9️⃣ Definition of Done

✅ Миграции применяются
✅ API работает
✅ Уведомления сохраняются в БД
✅ Push уходит через FCM
✅ Unread / Read работает
✅ Архитектура готова к масштабированию