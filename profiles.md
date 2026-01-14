Переработка архитектуры профилей атлетов и результатов (Laravel API)

⚠️ ОБЯЗАТЕЛЬНО: работа только в новой Git-ветке

🔷 GIT FLOW (СДЕЛАТЬ ПЕРВЫМ)

1️⃣ Убедиться, что текущая ветка — main
2️⃣ Создать новую ветку от main:

git checkout main
git pull
git checkout -b feature/user-profile-decoupling


❗ Вся работа ТОЛЬКО в этой ветке
❗ В main ничего не коммитить

🔷 КОНТЕКСТ ПРОЕКТА

Ты работаешь с существующим Laravel API (Laravel 11/12, Sanctum, REST, API-first).
Аутентификация, профили, результаты и мобильное приложение уже существуют и работают.

❗ Твоя задача — переработать связи, не ломая API.

🔷 ТЕКУЩАЯ ПРОБЛЕМА

Сейчас:

RaceResult привязан к user_id

UserProfile создаётся автоматически после подтверждения email

Нельзя заранее создать профили и привязать к ним результаты

🎯 ЦЕЛЬ

Перейти на Data-first архитектуру, где:

UserProfile создаётся заранее

RaceResult принадлежит UserProfile

User после регистрации вручную привязывается к профилю

user_profiles.user_id может быть NULL

❌ Никакого автоматического создания профиля

🧱 КАНОНИЧЕСКАЯ МОДЕЛЬ
User

аккаунт

email / пароль / токены

может существовать без профиля

UserProfile

атлет / тренер / админ

может существовать без user

содержит:

role

ironman_number (nullable)

bio

social_links

admin_full_name (служебное поле)

user_id (nullable)

RaceResult

принадлежит UserProfile

❌ НЕ принадлежит User напрямую

🔷 ЧТО НУЖНО СДЕЛАТЬ
1️⃣ Миграции базы данных
user_profiles

добавить поле:

admin_full_name VARCHAR(255) NOT NULL


user_id:

nullable

unique

foreign key → users.id

nullOnDelete

race_results

удалить user_id

добавить user_profile_id

foreign key → user_profiles.id

2️⃣ Обновить модели Eloquent
User
public function profile(): HasOne
{
    return $this->hasOne(UserProfile::class);
}


❌ НЕ создавать профиль автоматически
❌ НЕ менять логику регистрации

UserProfile
public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}

public function raceResults(): HasMany
{
    return $this->hasMany(RaceResult::class);
}

RaceResult
public function profile(): BelongsTo
{
    return $this->belongsTo(UserProfile::class, 'user_profile_id');
}

3️⃣ Регистрация и Email Verification

❗ КРИТИЧЕСКОЕ

После регистрации

После подтверждения email

🚫 НЕ создавать UserProfile автоматически

User может:

логиниться

иметь profile = null

4️⃣ API поведение

Если профиль не привязан:

{
  "profile": null
}


Frontend сам решает, что показывать.

5️⃣ Привязка профиля (РУЧНАЯ)

❌ НЕ делать публичный эндпоинт

Привязка выполняется:

вручную через БД

или через админ-инструмент (НЕ в этом таске)

UPDATE user_profiles
SET user_id = :userId
WHERE id = :profileId;

6️⃣ Обновить запросы результатов

Все результаты получаются через user.profile.raceResults

GET /users/{id}/race-results:

искать через user_profiles.id

7️⃣ Совместимость

❌ не ломать JSON формат

❌ не менять структуру API

❌ не добавлять бизнес-логику

✅ только переработка связей

🔒 КАЧЕСТВО

Чистые миграции

Строгая типизация

Без логики в контроллерах

API подходит для WEB + Mobile

🧪 ПРОВЕРКА

✔ Профиль может существовать без user
✔ User может существовать без профиля
✔ Результаты привязаны к профилю
✔ После ручной привязки:

профиль появляется

результаты доступны

❗ ЗАПРЕЩЕНО

Автосоздание профиля

Привязка результатов к user

Матчинг по email

Хранение ФИО в users

🔚 ЗАВЕРШЕНИЕ РАБОТЫ

После выполнения:

git status        # рабочее дерево чистое
git commit -m "Refactor profiles and race results to data-first architecture"


❗ НЕ делать merge в main