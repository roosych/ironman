🔔 TASK: Внедрение Firebase Cloud Messaging (FCM) в Laravel API
🎯 Цель

Реализовать серверную поддержку push-уведомлений через Firebase Cloud Messaging для мобильного приложения (Flutter).

Backend должен:

хранить FCM-токены устройств

отправлять push-уведомления конкретному пользователю

поддерживать multiple devices (несколько токенов на одного юзера)

корректно инвалидировать токены

быть готовым к расширению (массовые уведомления, события, админка)

🧱 Технологии и ограничения

Backend: Laravel

Auth: Laravel Sanctum

Клиент: Flutter

Push provider: Firebase Cloud Messaging

HTTP v1 API (НЕ legacy)

📁 Этап 1. Firebase проект
Требования:

Создать Firebase Project

Включить Cloud Messaging

Сгенерировать Service Account JSON

Сохранить credentials:

НЕ коммитить в git

хранить через .env или storage/app/firebase/

📁 Этап 2. Конфигурация Laravel
Добавить переменные окружения
FIREBASE_PROJECT_ID=your_project_id
FIREBASE_CREDENTIALS=storage/app/firebase/firebase.json

Создать config файл

config/firebase.php

return [
    'project_id' => env('FIREBASE_PROJECT_ID'),
    'credentials' => env('FIREBASE_CREDENTIALS'),
];

📁 Этап 3. Таблица FCM токенов
Миграция

Создать таблицу fcm_tokens:

Schema::create('fcm_tokens', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('token')->unique();
    $table->string('device_type')->nullable(); // android / ios
    $table->string('device_name')->nullable();
    $table->timestamps();
});

Модель

App\Models\FcmToken.php

class FcmToken extends Model
{
    protected $fillable = [
        'user_id',
        'token',
        'device_type',
        'device_name',
    ];
}

📁 Этап 4. API для регистрации FCM токена
Endpoint
POST /api/v1/user/fcm-token

Body
{
  "token": "fcm_device_token",
  "device_type": "android",
  "device_name": "Pixel 7"
}

Поведение:

Требует авторизацию (Sanctum)

Один и тот же токен не дублировать

При повторной отправке — обновлять updated_at

Controller

UserFcmTokenController@store

Логика:

updateOrCreate по token

Привязывать к текущему auth()->id()

📁 Этап 5. Удаление FCM токена (logout)
Endpoint
DELETE /api/v1/user/fcm-token

Body
{
  "token": "fcm_device_token"
}

Использовать при:

Logout

App uninstall / token refresh

📁 Этап 6. Сервис отправки push-уведомлений
Создать сервис

App\Services\Firebase\FcmService.php

Ответственность:

Получать OAuth2 access token

Отправлять HTTP v1 запросы в FCM

Обрабатывать ошибки (invalid token)

Метод отправки
sendToToken(string $token, array $notification, array $data = [])

sendToUser(User $user, array $notification, array $data = [])

Пример payload
{
  "message": {
    "token": "FCM_TOKEN",
    "notification": {
      "title": "New race added",
      "body": "Your upcoming race is confirmed"
    },
    "data": {
      "type": "race",
      "race_id": "12"
    }
  }
}

📁 Этап 7. Инвалидация токенов
Требования:

Если FCM возвращает:

UNREGISTERED

INVALID_ARGUMENT

→ удалить токен из базы

📁 Этап 8. Событийная архитектура (обязательно)
Создать события:

RaceCreated

RaceApproved

ProfileSynced

PasswordChanged

Listener:

Отправка push-уведомлений через FcmService

📁 Этап 9. Массовые уведомления (подготовка)

Реализовать метод:

sendToMany(array $tokens, array $notification, array $data = [])


(реализация может быть deferred)

📁 Этап 10. Безопасность и best practices
Обязательно:

❌ Не хранить Firebase credentials в git

❌ Не отправлять push напрямую из контроллеров

✅ Использовать сервисный слой

✅ Использовать очереди (Laravel Queue) для отправки

📁 Этап 11. Очереди (опционально, но желательно)

Отправку push выносить в Job

Использовать ShouldQueue

✅ Acceptance Criteria

Пользователь может получать push на несколько устройств

Токены корректно удаляются

Backend готов к:

персональным уведомлениям

массовым уведомлениям

расширению под админку

Код чистый, расширяемый, без логики в контроллерах

🧠 Примечание для Flutter

Backend не отвечает за:

Permission request

Token refresh handling

Flutter обязан:

отправлять FCM token после логина

обновлять токен при onTokenRefresh