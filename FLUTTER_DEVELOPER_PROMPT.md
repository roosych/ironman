# Промпт для Flutter разработчика: Интеграция мультиязычных уведомлений

## Краткое описание изменений

Бэкенд теперь поддерживает **мультиязычные уведомления**. Все push-уведомления и email автоматически отправляются на языке, выбранном пользователем.

## Что нужно сделать в Flutter приложении:

### 1. Добавить поле `locale` в модель User

```dart
class User {
  final int id;
  final String name;
  final String email;
  final String locale; // ← НОВОЕ ПОЛЕ (ru или en)
  final bool verified;
  
  // ...
}
```

### 2. Отправлять язык при регистрации/логине

```dart
// Получаем язык приложения
final appLocale = Localizations.localeOf(context).languageCode;
final locale = (appLocale == 'ru' || appLocale == 'en') ? appLocale : 'en';

// При регистрации
await api.post('/api/v1/auth/register', {
  'name': name,
  'email': email,
  'password': password,
  'password_confirmation': passwordConfirmation,
  'locale': locale, // ← Добавить
});

// При логине
await api.post('/api/v1/auth/login', {
  'email': email,
  'password': password,
  'locale': locale, // ← Добавить
});
```

### 3. Создать метод для обновления языка

```dart
Future<bool> updateUserLocale(String locale) async {
  try {
    final response = await api.put('/api/v1/auth/locale', {
      'locale': locale, // 'ru' или 'en'
    });
    
    if (response['success'] == true) {
      // Сохранить локально
      await prefs.setString('user_locale', locale);
      return true;
    }
    return false;
  } catch (e) {
    return false;
  }
}
```

### 4. Синхронизировать язык при изменении в настройках

```dart
void onLanguageChanged(String newLocale) async {
  // 1. Обновить язык в приложении
  setAppLocale(newLocale);
  
  // 2. Отправить на бэкенд (если пользователь авторизован)
  if (isAuthenticated) {
    await updateUserLocale(newLocale);
  }
  
  // 3. Сохранить локально
  await prefs.setString('user_locale', newLocale);
}
```

### 5. Использовать язык из API при авторизации

```dart
// После успешного логина/регистрации
final userLocale = response['data']['locale'] ?? 'en';
await prefs.setString('user_locale', userLocale);
setAppLocale(userLocale);
```

## Новый API endpoint

**PUT** `/api/v1/auth/locale`

**Request:**
```json
{
  "locale": "en"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Язык успешно обновлён.",
  "data": {
    "locale": "en"
  }
}
```

**Валидация:** `locale` должен быть `"ru"` или `"en"`

## Локализованные ответы API

**Важно:** Все ответы сервера теперь автоматически локализуются на основе языка пользователя!

### Что это значит:

1. **Сообщения об успехе/ошибках** приходят уже переведенными:
   - Регистрация: `"Регистрация прошла успешно."` / `"Registration successful."`
   - Логин: `"Вход выполнен успешно."` / `"Login successful."`
   - Сброс пароля: `"Письмо для сброса пароля отправлено."` / `"Password reset email sent."`
   - И т.д.

2. **Не нужно переводить сообщения на клиенте** - просто показывайте их пользователю:
   ```dart
   if (response['success'] == true) {
     showSnackBar(response['message']); // Уже переведено!
   }
   ```

3. **Язык определяется автоматически** из:
   - Языка авторизованного пользователя (приоритет)
   - Параметра `locale` в запросе
   - Заголовка `Accept-Language`
   - Дефолтного языка (`en`)

### Пример обработки ответов:

```dart
// Регистрация
final response = await api.post('/api/v1/auth/register', {...});
if (response['success'] == true) {
  // response['message'] уже на правильном языке!
  showSnackBar(response['message']);
}

// Логин с ошибкой
try {
  await api.post('/api/v1/auth/login', {...});
} catch (e) {
  // Ошибка уже локализована
  final error = e.response.data['errors']['email'][0];
  showError(error); // "Неверный email или пароль." или "Invalid email or password."
}
```

## Важно

1. ✅ **Уведомления уже переведены** - не нужно переводить их на клиенте
2. ✅ **Ответы API уже переведены** - не нужно переводить сообщения на клиенте
3. ✅ **Поддерживаются только ru и en** - другие языки игнорируются
4. ✅ **Сохраняйте язык локально** - для работы офлайн
5. ✅ **Синхронизируйте с бэкендом** - при изменении языка отправляйте на сервер

## Пример полного flow

```dart
  // 1. При запуске приложения
final savedLocale = await prefs.getString('user_locale') ?? 'en';
if (isAuthenticated) {
  final user = await fetchCurrentUser();
  final locale = user.locale ?? savedLocale;
  setAppLocale(locale);
} else {
  setAppLocale(savedLocale);
}

// 2. При логине
final loginResponse = await api.post('/api/v1/auth/login', {
  'email': email,
  'password': password,
  'locale': getCurrentAppLocale(), // ru или en
});
final userLocale = loginResponse['data']['locale'] ?? 'en';
await prefs.setString('user_locale', userLocale);
setAppLocale(userLocale);

// 3. При изменении языка в настройках
void changeLanguage(String newLocale) async {
  setAppLocale(newLocale);
  await prefs.setString('user_locale', newLocale);
  if (isAuthenticated) {
    await api.put('/api/v1/auth/locale', {'locale': newLocale});
  }
}
```

## Тестирование

1. Зарегистрируйтесь с `locale: "en"`
2. Проверьте, что уведомления приходят на английском
3. Измените язык через `PUT /api/v1/auth/locale` на `"ru"`
4. Проверьте, что следующее уведомление на русском

---

**Подробное руководство:** см. `FLUTTER_INTEGRATION_GUIDE.md`

