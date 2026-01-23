# Руководство по интеграции мультиязычных уведомлений для Flutter разработчика

## Обзор изменений на бэкенде

Бэкенд теперь поддерживает **мультиязычные уведомления** - все push-уведомления и email-уведомления автоматически отправляются на языке, который выбран пользователем в приложении.

## Что изменилось в API

### 1. Новое поле в модели User

В ответе API теперь возвращается поле `locale`:

```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Иван",
    "email": "user@example.com",
    "locale": "ru",  // ← НОВОЕ ПОЛЕ
    "verified": true,
    "profile": {...}
  }
}
```

**Поддерживаемые языки:** `ru` (русский), `en` (английский)

### ⚠️ ВАЖНО: Локализованные ответы API

**Все ответы сервера теперь автоматически локализуются!**

Все сообщения об успехе, ошибках и валидации приходят уже переведенными на язык пользователя. **Не нужно переводить их на клиенте** - просто показывайте пользователю.

**Примеры локализованных сообщений:**
- Регистрация: `"Регистрация прошла успешно."` / `"Registration successful."`
- Логин: `"Вход выполнен успешно."` / `"Login successful."`
- Ошибка логина: `"Неверный email или пароль."` / `"Invalid email or password."`
- Сброс пароля: `"Письмо для сброса пароля отправлено."` / `"Password reset email sent."`

**Язык определяется автоматически** из языка пользователя в базе данных или из запроса.

### 2. Новый endpoint для изменения языка

**PUT** `/api/v1/auth/locale`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
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

**Валидация:**
- `locale` обязателен
- Допустимые значения: `"ru"`, `"en"`
- При неверном значении вернется 422 с ошибкой

### 3. Обновленные endpoints регистрации и логина

Теперь можно передавать `locale` при регистрации и логине:

**POST** `/api/v1/auth/register`
```json
{
  "name": "Иван",
  "email": "user@example.com",
  "password": "password",
  "password_confirmation": "password",
  "locale": "en"  // ← ОПЦИОНАЛЬНО
}
```

**POST** `/api/v1/auth/login`
```json
{
  "email": "user@example.com",
  "password": "password",
  "locale": "en"  // ← ОПЦИОНАЛЬНО
}
```

**Важно:**
- Если `locale` не передан, бэкенд автоматически определит язык из заголовка `Accept-Language`
- Если заголовок отсутствует, используется дефолтный язык (`en`)

### 4. Автоматическое определение языка

Бэкенд автоматически определяет язык из:
1. Параметра `locale` в теле запроса (приоритет)
2. Заголовка `Accept-Language` (например: `Accept-Language: en-US,en;q=0.9,ru;q=0.8`)
3. Дефолтного языка из конфига (`en`)

## Что нужно сделать в Flutter приложении

### 1. Хранение языка пользователя

Сохраняйте `locale` из ответа API в локальное хранилище (SharedPreferences, Hive, etc.):

```dart
// После успешного логина/регистрации
final userLocale = userResponse['data']['locale'] ?? 'en';
await prefs.setString('user_locale', userLocale);
```

### 2. Отправка языка при регистрации/логине

```dart
// Получаем текущий язык приложения
final appLocale = Localizations.localeOf(context).languageCode;
// Поддерживаем только ru и en
final locale = (appLocale == 'ru' || appLocale == 'en') ? appLocale : 'en';

// При регистрации
final registerResponse = await api.post('/api/v1/auth/register', {
  'name': name,
  'email': email,
  'password': password,
  'password_confirmation': passwordConfirmation,
  'locale': locale, // ← Добавить
});

// При логине
final loginResponse = await api.post('/api/v1/auth/login', {
  'email': email,
  'password': password,
  'locale': locale, // ← Добавить
});
```

### 3. Создание метода для обновления языка

```dart
Future<bool> updateUserLocale(String locale) async {
  try {
    final response = await api.put(
      '/api/v1/auth/locale',
      {'locale': locale},
    );
    
    if (response['success'] == true) {
      // Сохраняем новый язык локально
      await prefs.setString('user_locale', locale);
      return true;
    }
    return false;
  } catch (e) {
    print('Error updating locale: $e');
    return false;
  }
}
```

### 4. Синхронизация языка приложения с бэкендом

При изменении языка в приложении:

```dart
void onLanguageChanged(String newLocale) async {
  // 1. Обновляем язык в приложении (через ваш state management)
  setAppLocale(newLocale);
  
  // 2. Отправляем на бэкенд
  final success = await updateUserLocale(newLocale);
  
  if (success) {
    // Показываем уведомление об успехе
    showSnackBar('Язык изменён');
  } else {
    // Обрабатываем ошибку
    showSnackBar('Ошибка при изменении языка');
  }
}
```

### 5. Отправка заголовка Accept-Language (опционально)

Если хотите, чтобы бэкенд автоматически определял язык из заголовка:

```dart
// В вашем API клиенте
class ApiClient {
  final http.Client _client;
  
  Future<Map<String, dynamic>> post(String endpoint, Map<String, dynamic> data) async {
    final locale = await prefs.getString('user_locale') ?? 'en';
    
    final response = await _client.post(
      Uri.parse('$baseUrl$endpoint'),
      headers: {
        'Content-Type': 'application/json',
        'Accept-Language': locale, // ← Добавить заголовок
        if (token != null) 'Authorization': 'Bearer $token',
      },
      body: jsonEncode(data),
    );
    
    return jsonDecode(response.body);
  }
}
```

### 6. Обработка уведомлений

**Важно:** Push-уведомления теперь приходят уже переведенными на язык пользователя. Никаких дополнительных действий не требуется.

```dart
// Уведомления приходят уже на правильном языке
FirebaseMessaging.onMessage.listen((RemoteMessage message) {
  // message.notification?.title - уже переведен
  // message.notification?.body - уже переведен
  showNotification(
    title: message.notification?.title ?? '',
    body: message.notification?.body ?? '',
  );
});
```

## Рекомендуемый flow работы с языком

### При первом запуске приложения:

1. Проверяем сохраненный язык пользователя:
   ```dart
   final savedLocale = await prefs.getString('user_locale');
   ```

2. Если пользователь авторизован, используем язык из API:
   ```dart
   if (isAuthenticated) {
     final user = await fetchCurrentUser();
     final locale = user['locale'] ?? savedLocale ?? 'en';
     setAppLocale(locale);
   } else {
     setAppLocale(savedLocale ?? 'en');
   }
   ```

### При логине/регистрации:

1. Определяем язык приложения
2. Отправляем `locale` в запросе
3. Сохраняем полученный `locale` из ответа

### При изменении языка в настройках:

1. Обновляем язык в приложении
2. Если пользователь авторизован - отправляем на бэкенд
3. Сохраняем локально

## Пример полной интеграции

```dart
class LocaleService {
  final SharedPreferences _prefs;
  final ApiClient _api;
  
  LocaleService(this._prefs, this._api);
  
  // Получить текущий язык
  Future<String> getCurrentLocale() async {
    // Приоритет: API > сохраненный > дефолт
    if (await _api.isAuthenticated()) {
      final user = await _api.getCurrentUser();
      return user['locale'] ?? await _getSavedLocale() ?? 'en';
    }
    return await _getSavedLocale() ?? 'en';
  }
  
  // Сохранить язык локально
  Future<void> saveLocale(String locale) async {
    await _prefs.setString('user_locale', locale);
  }
  
  // Получить сохраненный язык
  Future<String?> _getSavedLocale() async {
    return _prefs.getString('user_locale');
  }
  
  // Обновить язык на бэкенде
  Future<bool> updateLocale(String locale) async {
    try {
      final success = await _api.put('/api/v1/auth/locale', {
        'locale': locale,
      });
      
      if (success) {
        await saveLocale(locale);
        return true;
      }
      return false;
    } catch (e) {
      print('Error updating locale: $e');
      return false;
    }
  }
  
  // Получить язык для отправки в API
  String getLocaleForApi(BuildContext context) {
    final appLocale = Localizations.localeOf(context).languageCode;
    return (appLocale == 'ru' || appLocale == 'en') ? appLocale : 'en';
  }
}
```

## Важные моменты

1. **Всегда синхронизируйте язык** - при изменении языка в приложении отправляйте на бэкенд
2. **Сохраняйте локально** - для работы офлайн и быстрого доступа
3. **Используйте язык из API** - при авторизации приоритет у языка из бэкенда
4. **Уведомления уже переведены** - не нужно переводить их на клиенте
5. **Ответы API уже переведены** - не нужно переводить сообщения на клиенте, просто показывайте их
6. **Поддерживаются только ru и en** - другие языки будут игнорироваться, используется fallback

## Обработка локализованных ответов API

**Важно:** Все ответы сервера теперь автоматически локализуются! Не нужно переводить сообщения на клиенте.

### Успешные ответы

```dart
// Регистрация
final response = await api.post('/api/v1/auth/register', {...});
if (response['success'] == true) {
  // response['message'] уже на правильном языке!
  showSnackBar(response['message']); // "Регистрация прошла успешно." или "Registration successful."
}
```

### Ошибки

```dart
try {
  await api.post('/api/v1/auth/login', {...});
} catch (e) {
  if (e.response?.statusCode == 401) {
    // Ошибка уже локализована
    final error = e.response.data['errors']['email'][0];
    showError(error); // "Неверный email или пароль." или "Invalid email or password."
  }
}
```

**Подробнее:** см. `API_LOCALIZATION_GUIDE.md`

## Тестирование

Для проверки работы:

1. Зарегистрируйтесь с `locale: "en"`
2. Проверьте, что в ответе `user.locale === "en"`
3. Измените язык через `PUT /api/v1/auth/locale`
4. Отправьте тестовое уведомление - оно должно прийти на английском
5. Измените язык обратно на `ru` - следующее уведомление должно быть на русском

## Вопросы?

Если что-то непонятно или нужна помощь с интеграцией - обращайтесь к бэкенд разработчику.

