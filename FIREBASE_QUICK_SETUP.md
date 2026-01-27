# Firebase Quick Setup 🚀

## Быстрая настройка для новой команды

### 1. Скачайте 2 файла из Firebase Console

Для каждого проекта:
1. Откройте [Firebase Console](https://console.firebase.google.com/)
2. Project Settings → Service accounts → Generate new private key
3. Скачайте JSON файл

### 2. Разместите файлы

```
storage/app/firebase/
├── dev/firebase-dev.json          ← Development проект
└── prod/firebase-prod.json        ← Production проект
```

### 3. Обновите .env

```env
# Production (основной)
FIREBASE_PROJECT_ID=your-prod-project-id
FIREBASE_CREDENTIALS=storage/app/firebase/prod/firebase-prod.json

# Development
FIREBASE_DEV_PROJECT_ID=your-dev-project-id
FIREBASE_DEV_CREDENTIALS=storage/app/firebase/dev/firebase-dev.json
```

### 4. Готово! 🎉

Система автоматически выберет правильный Firebase проект на основе `APP_ENV`:

- `APP_ENV=local` → Development Firebase
- `APP_ENV=production` → Production Firebase

## ⚠️ Безопасность

- JSON файлы **НЕ попадают в Git** (уже настроено в .gitignore)
- Каждый разработчик скачивает файлы самостоятельно
- Не делитесь Firebase credentials в чатах/email

## Проверка

```bash
php artisan tinker

# В консоли:
App\Services\Firebase\FirebaseConfigService::isConfigured();
// Должно вернуть true

App\Services\Firebase\FirebaseConfigService::getCurrentEnvironment();
// Покажет текущее окружение
```

Подробная документация: `FIREBASE_SETUP.md`