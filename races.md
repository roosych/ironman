Laravel 12 проект.

Enum App\Enums\RaceType уже существует.
Создавать его НЕ нужно.
Использовать его в кастах модели.

Нужно создать:

1) Миграцию таблицы races
2) Модель Race
3) Сидер RaceSeeder (5 гонок)
4) Зарегистрировать сидер в DatabaseSeeder

Комментарии в коде — на русском.
Код production-ready.
Ничего лишнего не менять.

========================================================
1️⃣ МИГРАЦИЯ races
========================================================

Создать таблицу races со следующими колонками:

- id
- date (date)
- location (string)
- type (string) — хранит значение enum RaceType
- country_iso (string, длина 2)
- is_active (boolean, default true)
- timestamps

Добавить индексы:

- index(date)
- index(type)
- index(country_iso)
- index(is_active)

========================================================
2️⃣ МОДЕЛЬ Race
========================================================

namespace App\Models;

Требования:

- protected $fillable:
    date
    location
    type
    country_iso
    is_active

- protected $casts:
    date => 'date'
    is_active => 'boolean'
    type => App\Enums\RaceType::class

- Связь:

public function upcomingRaces()
{
    return $this->hasMany(UpcomingRace::class);
}

========================================================
3️⃣ СИДЕР RaceSeeder
========================================================

Создать 5 гонок:

1) IRONMAN Hamburg
   date: 2026-06-14
   location: Hamburg
   country_iso: DE
   type: RaceType::IRONMAN

2) IRONMAN 70.3 Warsaw
   date: 2026-05-20
   location: Warsaw
   country_iso: PL
   type: RaceType::HALF_IRONMAN

3) Berlin Marathon
   date: 2026-09-27
   location: Berlin
   country_iso: DE
   type: RaceType::MARATHON

4) Paris Triathlon
   date: 2026-07-10
   location: Paris
   country_iso: FR
   type: RaceType::TRIATHLON

5) Ultra Trail Alps
   date: 2026-08-18
   location: Innsbruck
   country_iso: AT
   type: RaceType::ULTRA

Использовать:

Race::create([...]);

Не использовать factory.
Не использовать faker.

========================================================
4️⃣ DATABASESEEDER
========================================================

Добавить:

$this->call(RaceSeeder::class);

========================================================
ТРЕБОВАНИЯ
========================================================

- Использовать strict typing
- Использовать enum кастинг Laravel 12
- Не менять другие файлы
- Код должен быть чистым
- Без лишних комментариев
