# Руководство по локализованным ответам API

## Обзор

Все ответы API теперь автоматически локализуются на основе языка пользователя. **Не нужно переводить сообщения на клиенте** - просто показывайте их пользователю.

## Как это работает

### Определение языка

Язык для ответа определяется в следующем порядке приоритета:

1. **Язык авторизованного пользователя** (если пользователь авторизован)
2. **Параметр `locale`** в теле запроса
3. **Заголовок `Accept-Language`** в HTTP-запросе
4. **Дефолтный язык** (`en`)

### Локализованные сообщения

Все сообщения в ответах API автоматически переводятся:

- ✅ Сообщения об успехе (`message`)
- ✅ Сообщения об ошибках (`errors`)
- ✅ Push-уведомления
- ✅ Email-уведомления

## Примеры использования

### Успешные ответы

```dart
// Регистрация
POST /api/v1/auth/register
{
  "name": "User",
  "email": "user@example.com",
  "password": "password",
  "locale": "ru"  // опционально
}

// Ответ (уже переведен):
{
  "success": true,
  "message": "Регистрация прошла успешно.",  // или "Registration successful."
  "data": {...}
}
```

```dart
// В коде Flutter:
final response = await api.post('/api/v1/auth/register', data);
if (response['success'] == true) {
  // Просто показываем сообщение - оно уже переведено!
  showSnackBar(response['message']);
}
```

### Ошибки валидации

```dart
// Ошибка валидации (уже переведена):
{
  "success": false,
  "errors": {
    "email": ["Введите корректный email адрес."],  // или "Please enter a valid email address."
    "password": ["Пароль обязателен для заполнения."]  // или "Password is required."
  }
}

// В коде Flutter:
try {
  await api.post('/api/v1/auth/register', data);
} catch (e) {
  if (e.response?.statusCode == 422) {
    final errors = e.response.data['errors'];
    // Показываем первую ошибку - она уже переведена
    showError(errors.values.first[0]);
  }
}
```

### Ошибки авторизации

**Важно для безопасности:** При ошибке логина всегда возвращается общая ошибка:

```dart
// Неверный email или пароль (всегда одинаковое сообщение):
{
  "success": false,
  "errors": {
    "email": ["Неверный email или пароль."]  // или "Invalid email or password."
  }
}

// В коде Flutter:
try {
  await api.post('/api/v1/auth/login', {
    'email': email,
    'password': password,
  });
} catch (e) {
  if (e.response?.statusCode == 401) {
    final error = e.response.data['errors']['email'][0];
    // Всегда общая ошибка - нельзя определить существование email
    showError(error);
  }
}
```

### Сброс пароля

**Важно для безопасности:** При запросе сброса пароля всегда возвращается одинаковое сообщение:

```dart
// Запрос сброса пароля:
POST /api/v1/auth/forgot-password
{
  "email": "user@example.com"
}

// Ответ (всегда одинаковый, независимо от существования email):
{
  "success": true,
  "message": "Письмо для сброса пароля отправлено."  // или "Password reset email sent."
}

// В коде Flutter:
final response = await api.post('/api/v1/auth/forgot-password', {
  'email': email,
});
// Всегда показываем успех - нельзя определить существование email
showSnackBar(response['message']);
```

## Все локализованные endpoints

### Авторизация

| Endpoint | Метод | Локализованные поля |
|----------|-------|---------------------|
| `/api/v1/auth/register` | POST | `message`, `errors` |
| `/api/v1/auth/login` | POST | `message`, `errors` |
| `/api/v1/auth/logout` | POST | `message`, `errors` |
| `/api/v1/auth/forgot-password` | POST | `message` |
| `/api/v1/auth/reset-password` | POST | `message`, `errors` |
| `/api/v1/auth/email/resend-verification` | POST | `message`, `errors` |
| `/api/v1/auth/locale` | PUT | `message` |

### Пароль

| Endpoint | Метод | Локализованные поля |
|----------|-------|---------------------|
| `/api/v1/user/password` | PUT | `message`, `errors` |

## Рекомендации для разработчика

### ✅ Правильно

```dart
// Показываем сообщение напрямую - оно уже переведено
showSnackBar(response['message']);

// Показываем ошибку напрямую - она уже переведена
showError(errors['email'][0]);
```

### ❌ Неправильно

```dart
// НЕ нужно переводить на клиенте!
showSnackBar(translate(response['message'])); // ❌

// НЕ нужно проверять язык и переводить
if (locale == 'ru') {
  showSnackBar('Регистрация прошла успешно'); // ❌
} else {
  showSnackBar('Registration successful'); // ❌
}
```

## Обработка ошибок - полный пример

```dart
class ApiClient {
  Future<Map<String, dynamic>> post(String endpoint, Map<String, dynamic> data) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl$endpoint'),
        headers: {
          'Content-Type': 'application/json',
          'Accept-Language': await getCurrentLocale(), // Отправляем язык
          if (token != null) 'Authorization': 'Bearer $token',
        },
        body: jsonEncode(data),
      );

      final responseData = jsonDecode(response.body);

      if (responseData['success'] == true) {
        // Успех - сообщение уже переведено
        if (responseData['message'] != null) {
          showSnackBar(responseData['message']);
        }
        return responseData;
      } else {
        // Ошибка - сообщения уже переведены
        throw ApiException(responseData);
      }
    } on http.ClientException catch (e) {
      throw NetworkException(e.message);
    } on FormatException {
      throw ApiException({'message': 'Invalid response format'});
    }
  }
}

class ApiException implements Exception {
  final Map<String, dynamic> data;

  ApiException(this.data);

  String getFirstError() {
    if (data['errors'] != null) {
      final errors = data['errors'] as Map<String, dynamic>;
      final firstErrorKey = errors.keys.first;
      final firstError = errors[firstErrorKey] as List;
      return firstError[0]; // Уже переведено!
    }
    return data['message'] ?? 'An error occurred';
  }
}

// Использование:
try {
  await api.post('/api/v1/auth/login', {
    'email': email,
    'password': password,
  });
} on ApiException catch (e) {
  // Ошибка уже переведена
  showError(e.getFirstError());
}
```

## Тестирование

Для проверки локализации:

1. **Зарегистрируйтесь с `locale: "en"`**
   - Проверьте, что все сообщения на английском

2. **Измените язык через `PUT /api/v1/auth/locale`**
   - Проверьте, что следующие ответы на новом языке

3. **Отправьте запрос с заголовком `Accept-Language: ru`**
   - Проверьте, что ответы на русском

4. **Проверьте ошибки**
   - Попробуйте неверный логин - ошибка должна быть локализована
   - Попробуйте неверную валидацию - ошибки должны быть локализованы

## Вопросы?

Если что-то непонятно или нужна помощь - обращайтесь к бэкенд разработчику.

