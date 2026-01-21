# 🧪 Тестирование FCM уведомлений

## Вариант 1: Artisan команда (рекомендуется)

### Базовое использование

```bash
php artisan fcm:test
```

Команда автоматически найдет первого пользователя с зарегистрированным FCM токеном и отправит тестовое уведомление.

### С указанием пользователя

```bash
# По email
php artisan fcm:test --user=user@example.com

# По ID
php artisan fcm:test --user=1
```

### С кастомными параметрами

```bash
php artisan fcm:test \
  --user=user@example.com \
  --title="Новая гонка" \
  --body="Вы добавили upcoming Ironman" \
  --type=upcoming
```

### Синхронная отправка (без очереди)

```bash
php artisan fcm:test --user=user@example.com --sync
```

Полезно для быстрого тестирования без запуска queue worker.

### Параметры команды

- `--user` - Email или ID пользователя (опционально)
- `--title` - Заголовок уведомления (по умолчанию: "Тестовое уведомление")
- `--body` - Текст уведомления (по умолчанию: "Это тестовое уведомление для проверки FCM")
- `--type` - Тип уведомления: system, race, result, upcoming, admin (по умолчанию: system)
- `--sync` - Отправить синхронно без очереди

---

## Вариант 2: API endpoint (для Postman/Flutter)

### Endpoint

```
POST /api/v1/notifications/test
```

**⚠️ Важно:** Работает только в development окружении!

### Headers

```
Authorization: Bearer {your_auth_token}
Content-Type: application/json
```

### Body (опционально)

```json
{
  "title": "Тестовое уведомление",
  "body": "Это тестовое уведомление для проверки FCM",
  "type": "system",
  "data": {
    "test": "true",
    "race_id": "123"
  }
}
```

### Пример запроса (cURL)

```bash
curl -X POST http://localhost:8000/api/v1/notifications/test \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Тест",
    "body": "Проверка FCM",
    "type": "system"
  }'
```

### Пример ответа (201)

```json
{
  "success": true,
  "message": "Тестовое уведомление отправлено на 2 устройство(а).",
  "data": {
    "id": 1,
    "title": "Тест",
    "body": "Проверка FCM",
    "type": "system",
    "data": {
      "test": "true"
    },
    "read_at": null,
    "is_read": false,
    "created_at": "2026-01-19T18:00:00+00:00"
  }
}
```

---

## 📱 Пошаговая инструкция тестирования

### 1. Запустите queue worker (если не используете --sync)

```bash
php artisan queue:work
```

Или через dev скрипт:
```bash
composer dev
```

### 2. Зарегистрируйте FCM токен в мобильном приложении

Убедитесь, что мобильное приложение:
- Получило FCM токен
- Отправило его на сервер через `POST /api/v1/user/fcm-token`

### 3. Отправьте тестовое уведомление

**Через команду:**
```bash
php artisan fcm:test --user=your@email.com --sync
```

**Через API:**
```bash
POST /api/v1/notifications/test
```

### 4. Проверьте результат

- ✅ Уведомление должно появиться на устройстве
- ✅ Уведомление должно быть сохранено в БД (таблица `notifications`)
- ✅ Проверьте логи Laravel на наличие ошибок

---

## 🔍 Проверка статуса

### Проверить зарегистрированные токены

```bash
php artisan tinker
>>> User::find(1)->fcmTokens()->get(['token', 'device_type', 'created_at']);
```

### Проверить отправленные уведомления

```bash
php artisan tinker
>>> User::find(1)->notifications()->latest()->get(['title', 'body', 'type', 'created_at']);
```

### Проверить очередь

```bash
# Посмотреть задачи в очереди
php artisan queue:work --once

# Или проверить в БД
php artisan tinker
>>> DB::table('jobs')->count();
```

---

## 🐛 Отладка проблем

### Уведомление не приходит

1. **Проверьте FCM токен:**
   ```bash
   php artisan tinker
   >>> User::find(1)->fcmTokens()->count();
   ```

2. **Проверьте Firebase настройки:**
   ```bash
   php artisan tinker
   >>> config('firebase.project_id');
   >>> file_exists(Storage::path(config('firebase.credentials')));
   ```

3. **Проверьте логи:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Проверьте queue worker:**
   - Убедитесь, что worker запущен
   - Проверьте, нет ли failed jobs

### Ошибки Firebase

- Проверьте правильность `FIREBASE_PROJECT_ID` в `.env`
- Проверьте наличие и доступность файла credentials
- Проверьте права Service Account в Firebase Console

### Токен не регистрируется

- Проверьте авторизацию (Bearer token)
- Проверьте формат запроса
- Проверьте логи Laravel

---

## 📝 Примеры использования

### Тест разных типов уведомлений

```bash
# Системное уведомление
php artisan fcm:test --type=system

# Уведомление о гонке
php artisan fcm:test --type=race --title="Новая гонка" --body="Ironman добавлен"

# Уведомление о результате
php artisan fcm:test --type=result --title="Результат подтвержден"
```

### Массовое тестирование

```bash
# Отправить всем пользователям с токенами
php artisan tinker
>>> User::whereHas('fcmTokens')->each(function($user) {
...   \App\Actions\Notifications\SendNotificationAction::dispatch(
...     $user, 'Тест', 'Массовое тестирование', 'system'
...   );
... });
```

---

## ✅ Checklist перед тестированием

- [ ] Firebase настроен (Project ID и credentials)
- [ ] Queue worker запущен (или используйте --sync)
- [ ] FCM токен зарегистрирован в БД
- [ ] Мобильное приложение запущено и подключено к интернету
- [ ] Разрешения на уведомления выданы в приложении

---

## 🎯 Быстрый старт

```bash
# 1. Запустить queue worker (в отдельном терминале)
php artisan queue:work

# 2. Отправить тестовое уведомление
php artisan fcm:test --user=your@email.com

# 3. Проверить на устройстве
```

Готово! 🎉

