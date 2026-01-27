# Firebase Multi-Environment Setup Guide

## Обзор

Этот проект поддерживает три окружения Firebase:
- **Development** (`local`/`development`) - для разработки
- **Staging** (`staging`) - для тестирования
- **Production** (`production`) - для продакшн

## Структура файлов

```
storage/app/firebase/
├── dev/
│   └── firebase-dev.json          # Credentials для разработки
├── staging/
│   └── firebase-staging.json      # Credentials для staging
└── prod/
    └── firebase-prod.json         # Credentials для продакшн
```

## 🔒 Безопасность

**ВАЖНО:** Firebase credentials файлы содержат приватные ключи и **НЕ ДОЛЖНЫ** попадать в Git!

- Все `*.json` файлы в папке `storage/app/firebase/` исключены из Git через `.gitignore`
- В репозитории остаются только `.gitkeep` файлы для сохранения структуры папок

## Настройка окружений

### 1. Создание проектов в Firebase Console

1. Перейдите в [Firebase Console](https://console.firebase.google.com/)
2. Создайте три отдельных проекта:
   - `ironman-dev` (для разработки)
   - `ironman-staging` (для тестирования)
   - `ironman-prod` (для продакшн, если еще не создан)

### 2. Скачивание Service Account Keys

Для каждого проекта в Firebase Console:

1. Перейдите в **Project Settings** (⚙️)
2. Откройте вкладку **Service accounts**
3. Выберите **Firebase Admin SDK**
4. Нажмите **Generate new private key**
5. Скачайте JSON файл

### 3. Размещение файлов

Разместите скачанные файлы в соответствующих папках:

```bash
# Development окружение
storage/app/firebase/dev/firebase-dev.json

# Staging окружение
storage/app/firebase/staging/firebase-staging.json

# Production окружение
storage/app/firebase/prod/firebase-prod.json
```

**Имена файлов важны!** Используйте именно эти имена или обновите переменные в `.env`.

### 4. Настройка переменных окружения

Обновите ваш `.env` файл:

```env
# Firebase Production Environment (основное)
FIREBASE_PROJECT_ID=your-prod-project-id
FIREBASE_CREDENTIALS=storage/app/firebase/prod/firebase-prod.json

# Firebase Development Environment
FIREBASE_DEV_PROJECT_ID=your-dev-project-id
FIREBASE_DEV_CREDENTIALS=storage/app/firebase/dev/firebase-dev.json

# Firebase Staging Environment
FIREBASE_STAGING_PROJECT_ID=your-staging-project-id
FIREBASE_STAGING_CREDENTIALS=storage/app/firebase/staging/firebase-staging.json
```

## Использование в коде

### Автоматический выбор окружения

Система автоматически выберет правильную конфигурацию Firebase на основе `APP_ENV`:

```php
use App\Services\Firebase\FirebaseConfigService;

// Получить конфигурацию для текущего окружения
$config = FirebaseConfigService::getConfig();
$projectId = $config['project_id'];
$credentialsPath = $config['credentials'];

// Или использовать отдельные методы
$projectId = FirebaseConfigService::getProjectId();
$credentialsPath = FirebaseConfigService::getCredentialsPath();

// Проверить, настроен ли Firebase для текущего окружения
if (FirebaseConfigService::isConfigured()) {
    // Firebase готов к использованию
}
```

### Получение конфигурации для конкретного окружения

```php
// Получить конфигурацию для staging, независимо от текущего APP_ENV
$stagingConfig = FirebaseConfigService::getConfigForEnvironment('staging');
```

## Соответствие APP_ENV и Firebase окружений

| APP_ENV | Firebase Environment | Credentials File |
|---------|---------------------|------------------|
| `local` | Development | `storage/app/firebase/dev/firebase-dev.json` |
| `development` | Development | `storage/app/firebase/dev/firebase-dev.json` |
| `staging` | Staging | `storage/app/firebase/staging/firebase-staging.json` |
| `production` | Production | `storage/app/firebase/prod/firebase-prod.json` |

## Миграция существующего проекта

Если у вас уже есть Firebase credentials:

1. Переместите существующий файл в папку `storage/app/firebase/prod/`
2. Обновите `FIREBASE_CREDENTIALS` в `.env` на новый путь
3. Настройте dev и staging окружения по инструкции выше

## Проверка настройки

Используйте Artisan команду для проверки конфигурации Firebase:

```bash
php artisan tinker

# В Tinker консоли:
App\Services\Firebase\FirebaseConfigService::getConfig();
App\Services\Firebase\FirebaseConfigService::isConfigured();
App\Services\Firebase\FirebaseConfigService::getCurrentEnvironment();
```

## Развертывание

При развертывании на серверах:

### Development Server
```env
APP_ENV=development
# Система автоматически использует dev Firebase проект
```

### Staging Server
```env
APP_ENV=staging
# Система автоматически использует staging Firebase проект
```

### Production Server
```env
APP_ENV=production
# Система автоматически использует production Firebase проект
```

## Troubleshooting

### Проблема: "Firebase credentials file not found"

1. Проверьте, что файл существует по указанному пути
2. Проверьте права доступа к файлу
3. Убедитесь, что путь в `.env` корректный

### Проблема: "Invalid Firebase credentials"

1. Убедитесь, что JSON файл не поврежден
2. Проверьте, что Service Account имеет необходимые права в Firebase Console
3. Убедитесь, что Project ID соответствует проекту в Firebase Console

### Проблема: "Wrong Firebase project"

1. Проверьте переменную `APP_ENV`
2. Убедитесь, что Project ID в `.env` соответствует нужному окружению
3. Проверьте, что используется правильный JSON файл

## FCM Push Notifications

Каждое окружение будет отправлять push-уведомления через соответствующий Firebase проект. Убедитесь, что мобильные приложения настроены на правильные проекты для каждого окружения.

## Дополнительная информация

- Firebase credentials автоматически исключены из Git для безопасности
- Структура папок сохраняется через `.gitkeep` файлы
- При смене окружения перезапустите сервер для применения новой конфигурации
- Используйте разные Firebase проекты для изоляции данных между окружениями