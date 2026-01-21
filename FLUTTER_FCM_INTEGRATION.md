# Flutter FCM Integration Guide

## 📱 Интеграция Firebase Cloud Messaging в Flutter приложение

Этот документ описывает, как интегрировать FCM (Firebase Cloud Messaging) в Flutter приложение для работы с Laravel backend.

---

## 📦 1. Установка зависимостей

Добавьте в `pubspec.yaml`:

```yaml
dependencies:
  flutter:
    sdk: flutter
  firebase_core: ^2.24.0
  firebase_messaging: ^14.7.0
  http: ^1.1.0
  shared_preferences: ^2.2.0
  device_info_plus: ^5.0.0
```

Затем выполните:
```bash
flutter pub get
```

---

## 🔧 2. Настройка Firebase

### Android

1. Скачайте `google-services.json` из Firebase Console
2. Поместите в `android/app/google-services.json`
3. В `android/build.gradle` добавьте:
```gradle
buildscript {
    dependencies {
        classpath 'com.google.gms:google-services:4.4.0'
    }
}
```

4. В `android/app/build.gradle` добавьте в конец:
```gradle
apply plugin: 'com.google.gms.google-services'
```

### iOS

1. Скачайте `GoogleService-Info.plist` из Firebase Console
2. Добавьте в Xcode проект (перетащите в `ios/Runner/`)
3. В `ios/Podfile` добавьте:
```ruby
platform :ios, '12.0'
```

4. Выполните:
```bash
cd ios && pod install
```

---

## 💻 3. Реализация FCM Service

Создайте файл `lib/services/fcm_service.dart`:

```dart
import 'dart:io';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';

class FcmService {
  static final FcmService _instance = FcmService._internal();
  factory FcmService() => _instance;
  FcmService._internal();

  final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  String? _currentToken;
  String? _apiBaseUrl;
  String? _authToken;

  /// Инициализация FCM
  Future<void> initialize({
    required String apiBaseUrl,
    String? authToken,
  }) async {
    _apiBaseUrl = apiBaseUrl;
    _authToken = authToken;

    // Запрос разрешений
    NotificationSettings settings = await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
    );

    if (settings.authorizationStatus == AuthorizationStatus.authorized ||
        settings.authorizationStatus == AuthorizationStatus.provisional) {
      // Получаем токен
      await _getAndRegisterToken();

      // Слушаем обновления токена
      _messaging.onTokenRefresh.listen((newToken) {
        _registerToken(newToken);
      });

      // Настройка обработчиков уведомлений
      _setupMessageHandlers();
    } else {
      debugPrint('FCM: Permission denied');
    }
  }

  /// Получить и зарегистрировать токен
  Future<void> _getAndRegisterToken() async {
    try {
      String? token = await _messaging.getToken();
      if (token != null) {
        _currentToken = token;
        await _registerToken(token);
      }
    } catch (e) {
      debugPrint('FCM: Error getting token: $e');
    }
  }

  /// Регистрация токена на сервере
  Future<void> _registerToken(String token) async {
    if (_apiBaseUrl == null || _authToken == null) {
      debugPrint('FCM: API URL or auth token not set');
      return;
    }

    try {
      final deviceInfo = DeviceInfoPlugin();
      String deviceName = 'Unknown Device';
      String deviceType = Platform.isAndroid ? 'android' : 'ios';

      if (Platform.isAndroid) {
        final androidInfo = await deviceInfo.androidInfo;
        deviceName = '${androidInfo.manufacturer} ${androidInfo.model}';
      } else if (Platform.isIOS) {
        final iosInfo = await deviceInfo.iosInfo;
        deviceName = '${iosInfo.name} (${iosInfo.model})';
      }

      final response = await http.post(
        Uri.parse('$_apiBaseUrl/api/v1/user/fcm-token'),
        headers: {
          'Authorization': 'Bearer $_authToken',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'token': token,
          'device_type': deviceType,
          'device_name': deviceName,
        }),
      );

      if (response.statusCode == 201) {
        debugPrint('FCM: Token registered successfully');
        await _saveTokenLocally(token);
      } else {
        debugPrint('FCM: Failed to register token: ${response.statusCode}');
        debugPrint('FCM: Response: ${response.body}');
      }
    } catch (e) {
      debugPrint('FCM: Error registering token: $e');
    }
  }

  /// Сохранение токена локально
  Future<void> _saveTokenLocally(String token) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString('fcm_token', token);
  }

  /// Обновление auth token (после логина)
  Future<void> updateAuthToken(String? authToken) async {
    _authToken = authToken;
    
    // Если токен уже есть, перерегистрируем его
    if (_currentToken != null) {
      await _registerToken(_currentToken!);
    } else {
      await _getAndRegisterToken();
    }
  }

  /// Удаление токена (при logout)
  Future<void> unregisterToken() async {
    if (_apiBaseUrl == null || _authToken == null || _currentToken == null) {
      return;
    }

    try {
      final response = await http.delete(
        Uri.parse('$_apiBaseUrl/api/v1/user/fcm-token'),
        headers: {
          'Authorization': 'Bearer $_authToken',
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: jsonEncode({
          'token': _currentToken,
        }),
      );

      if (response.statusCode == 200) {
        debugPrint('FCM: Token unregistered successfully');
        _currentToken = null;
        final prefs = await SharedPreferences.getInstance();
        await prefs.remove('fcm_token');
      }
    } catch (e) {
      debugPrint('FCM: Error unregistering token: $e');
    }
  }

  /// Настройка обработчиков сообщений
  void _setupMessageHandlers() {
    // Уведомления когда приложение открыто (foreground)
    FirebaseMessaging.onMessage.listen((RemoteMessage message) {
      debugPrint('FCM: Foreground message received');
      debugPrint('FCM: Title: ${message.notification?.title}');
      debugPrint('FCM: Body: ${message.notification?.body}');
      debugPrint('FCM: Data: ${message.data}');

      // Здесь можно показать локальное уведомление
      // или обновить UI приложения
      _handleNotification(message);
    });

    // Уведомления когда приложение в фоне (background)
    FirebaseMessaging.onMessageOpenedApp.listen((RemoteMessage message) {
      debugPrint('FCM: Background message opened app');
      _handleNotificationTap(message);
    });

    // Проверка, было ли приложение открыто из уведомления
    _messaging.getInitialMessage().then((RemoteMessage? message) {
      if (message != null) {
        debugPrint('FCM: App opened from notification');
        _handleNotificationTap(message);
      }
    });
  }

  /// Обработка уведомления
  void _handleNotification(RemoteMessage message) {
    // Здесь можно показать локальное уведомление
    // или обновить состояние приложения
    
    // Пример: обновить список уведомлений
    // notificationService.refreshNotifications();
  }

  /// Обработка тапа по уведомлению
  void _handleNotificationTap(RemoteMessage message) {
    final data = message.data;
    final type = data['type'];
    
    // Навигация в зависимости от типа уведомления
    switch (type) {
      case 'race':
        final raceId = data['race_id'];
        // Navigator.pushNamed(context, '/race/$raceId');
        break;
      case 'profile':
        final profileId = data['profile_id'];
        // Navigator.pushNamed(context, '/profile/$profileId');
        break;
      case 'security':
        // Navigator.pushNamed(context, '/security');
        break;
      default:
        // Navigator.pushNamed(context, '/notifications');
        break;
    }
  }

  /// Получить текущий токен
  String? get currentToken => _currentToken;
}

/// Top-level функция для обработки фоновых сообщений
/// Должна быть вне класса
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  await Firebase.initializeApp();
  debugPrint('FCM: Background message received');
  debugPrint('FCM: Title: ${message.notification?.title}');
  debugPrint('FCM: Body: ${message.notification?.body}');
  debugPrint('FCM: Data: ${message.data}');
  
  // Здесь можно обработать фоновое уведомление
  // Например, обновить локальную БД или показать уведомление
}
```

---

## 🚀 4. Инициализация в main.dart

```dart
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'services/fcm_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Инициализация Firebase
  await Firebase.initializeApp();
  
  // Регистрация background handler
  FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  
  runApp(MyApp());
}

class MyApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Ironman App',
      home: HomePage(),
    );
  }
}
```

---

## 🔐 5. Интеграция с Auth Service

В вашем Auth Service после успешного логина:

```dart
class AuthService {
  final FcmService _fcmService = FcmService();
  
  Future<void> login(String email, String password) async {
    // ... ваш код логина ...
    
    // После получения auth token
    final authToken = response['data']['token'];
    
    // Инициализируем FCM с auth token
    await _fcmService.initialize(
      apiBaseUrl: 'https://your-api.com',
      authToken: authToken,
    );
  }
  
  Future<void> logout() async {
    // Удаляем FCM токен с сервера
    await _fcmService.unregisterToken();
    
    // ... остальной код logout ...
  }
}
```

---

## 📬 6. Получение списка уведомлений

Создайте сервис для работы с уведомлениями:

```dart
class NotificationService {
  final String _apiBaseUrl;
  final String? _authToken;

  NotificationService({
    required String apiBaseUrl,
    String? authToken,
  }) : _apiBaseUrl = apiBaseUrl, _authToken = authToken;

  /// Получить список уведомлений
  Future<Map<String, dynamic>> getNotifications({
    int page = 1,
    int perPage = 15,
  }) async {
    final response = await http.get(
      Uri.parse('$_apiBaseUrl/api/v1/notifications?page=$page&per_page=$perPage'),
      headers: {
        'Authorization': 'Bearer $_authToken',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      return jsonDecode(response.body);
    } else {
      throw Exception('Failed to load notifications');
    }
  }

  /// Пометить уведомление как прочитанное
  Future<void> markAsRead(int notificationId) async {
    final response = await http.post(
      Uri.parse('$_apiBaseUrl/api/v1/notifications/$notificationId/read'),
      headers: {
        'Authorization': 'Bearer $_authToken',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode != 200) {
      throw Exception('Failed to mark notification as read');
    }
  }

  /// Пометить все как прочитанные
  Future<void> markAllAsRead() async {
    final response = await http.post(
      Uri.parse('$_apiBaseUrl/api/v1/notifications/read-all'),
      headers: {
        'Authorization': 'Bearer $_authToken',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode != 200) {
      throw Exception('Failed to mark all as read');
    }
  }
}
```

---

## 🎯 7. Пример использования в UI

```dart
class NotificationsPage extends StatefulWidget {
  @override
  _NotificationsPageState createState() => _NotificationsPageState();
}

class _NotificationsPageState extends State<NotificationsPage> {
  final NotificationService _notificationService = NotificationService(
    apiBaseUrl: 'https://your-api.com',
    authToken: 'your-auth-token',
  );

  List<dynamic> _notifications = [];
  int _unreadCount = 0;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadNotifications();
  }

  Future<void> _loadNotifications() async {
    setState(() => _isLoading = true);
    
    try {
      final data = await _notificationService.getNotifications();
      setState(() {
        _notifications = data['data'] ?? [];
        _unreadCount = data['meta']['unread_count'] ?? 0;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Ошибка загрузки уведомлений')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Уведомления'),
        actions: [
          if (_unreadCount > 0)
            TextButton(
              onPressed: _markAllAsRead,
              child: Text('Прочитать все'),
            ),
        ],
      ),
      body: _isLoading
          ? Center(child: CircularProgressIndicator())
          : ListView.builder(
              itemCount: _notifications.length,
              itemBuilder: (context, index) {
                final notification = _notifications[index];
                return ListTile(
                  title: Text(notification['title']),
                  subtitle: Text(notification['body']),
                  trailing: notification['is_read']
                      ? null
                      : Icon(Icons.circle, color: Colors.blue, size: 12),
                  onTap: () => _handleNotificationTap(notification),
                );
              },
            ),
    );
  }

  Future<void> _markAllAsRead() async {
    try {
      await _notificationService.markAllAsRead();
      await _loadNotifications();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Ошибка')),
      );
    }
  }

  Future<void> _handleNotificationTap(Map<String, dynamic> notification) async {
    // Пометить как прочитанное
    if (!notification['is_read']) {
      await _notificationService.markAsRead(notification['id']);
      await _loadNotifications();
    }

    // Навигация в зависимости от типа
    final data = notification['data'] ?? {};
    final type = notification['type'];

    switch (type) {
      case 'race':
        final raceId = data['race_id'];
        // Navigator.pushNamed(context, '/race/$raceId');
        break;
      // ... другие типы
    }
  }
}
```

---

## 📋 8. Checklist интеграции

- [ ] Установлены зависимости (`firebase_core`, `firebase_messaging`)
- [ ] Настроен Firebase для Android (`google-services.json`)
- [ ] Настроен Firebase для iOS (`GoogleService-Info.plist`)
- [ ] Создан `FcmService` класс
- [ ] Инициализирован FCM в `main.dart`
- [ ] Зарегистрирован background handler
- [ ] Интегрирован с Auth Service (вызов после логина)
- [ ] Реализована отправка токена на сервер
- [ ] Реализована обработка входящих уведомлений
- [ ] Реализована навигация по типам уведомлений
- [ ] Реализован UI для списка уведомлений
- [ ] Протестировано на реальных устройствах

---

## 🔍 9. Отладка

### Проверка токена
```dart
final token = await FirebaseMessaging.instance.getToken();
print('FCM Token: $token');
```

### Логирование
Включите debug режим:
```dart
FirebaseMessaging.instance.setAutoInitEnabled(true);
```

### Тестирование отправки
Используйте Firebase Console → Cloud Messaging → Send test message

---

## ⚠️ 10. Важные моменты

1. **Токен обновляется** - слушайте `onTokenRefresh` и отправляйте новый токен на сервер
2. **Разрешения** - запрашивайте разрешения при первом запуске
3. **Background** - background handler должен быть top-level функцией
4. **iOS** - для iOS нужны дополнительные настройки в Xcode (Push Notifications capability)
5. **Android** - минимальная версия Android должна быть 21+

---

## 📚 Дополнительные ресурсы

- [Firebase Messaging Flutter](https://firebase.flutter.dev/docs/messaging/overview)
- [FCM HTTP v1 API](https://firebase.google.com/docs/cloud-messaging/migrate-v1)
- [Laravel Backend API Documentation](./fcm.md)

---

## 🆘 Поддержка

При возникновении проблем проверьте:
1. Логи в консоли Flutter
2. Логи Firebase Console
3. Логи Laravel backend
4. Правильность настройки Firebase проектов

