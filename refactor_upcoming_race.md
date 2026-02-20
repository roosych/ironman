Laravel 12 проект.

ЦЕЛЬ:
1️⃣ Перейти на строгую нормализацию upcoming_races.
2️⃣ Обновить UpcomingRaceResource, чтобы JSON остался в старом формате, но данные брались из модели Race.

========================================================
ЧАСТЬ 1: Миграция
========================================================

Новая миграция: php artisan make:migration refactor_upcoming_races_to_use_race_id

up():
- Таблица upcoming_races теперь хранит только:
    - id
    - user_profile_id (FK)
    - race_id (FK) -> constrained('races')->cascadeOnDelete()
    - is_active
    - timestamps
- Добавить уникальный индекс: unique(['user_profile_id','race_id'])
- Удалить колонки:
    - race_type
    - location
    - race_date

down():
- Восстановить колонки:
    - race_type (string)
    - location (string)
    - race_date (date)
- Удалить unique индекс
- Удалить foreign key race_id

ВАЖНО:
- Сначала удалить индекс, потом FK
- Использовать dropColumn корректно
- Код production-ready
- Ничего больше не менять

========================================================
ЧАСТЬ 2: UpcomingRaceResource
========================================================

ЦЕЛЬ: JSON остался В ТОЧНО ТАКОМ ЖЕ ФОРМАТЕ:

{
    "success": true,
    "data": [
        {
            "id": 6,
            "race_type": "5150",
            "race_type_label": "5150",
            "location": "Hamburg",
            "race_date": "2026-02-18",
            "is_active": true,
            "created_by": {
                "id": 19,
                "name": "Rusik",
                "avatar": null
            }
        }
    ]
}

ТРЕБОВАНИЯ:

1️⃣ В UpcomingRaceResource использовать данные из связанной модели Race:

'race_type' => $this->race?->type?->value,
'race_type_label' => $this->race?->type?->label(),
'location' => $this->race?->location,
'race_date' => $this->race?->date?->toDateString(),

2️⃣ created_by оставить как раньше, получать через связи:

$this->userProfile?->user

'created_by' => [
    'id' => $this->userProfile?->user?->id,
    'name' => $this->userProfile?->user?->name,
    'avatar' => $this->userProfile?->user?->avatar,
]

3️⃣ Использовать nullsafe оператор ?->

4️⃣ В контроллере обязательный eager loading:

UpcomingRace::with([
    'race',
    'userProfile.user'
])

5️⃣ Не менять формат ответа, не трогать другие контроллеры

6️⃣ Код production-ready

========================================================
ДОПОЛНИТЕЛЬНО:

- Добавить сортировку upcoming по race.date
- Автоматическая фильтрация inactive races
- Продумать стратегию если race будет удалён
