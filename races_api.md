Laravel 12 проект.

Модель Race уже существует.
Enum App\Enums\RaceType уже существует.
Sanctum уже настроен.

Нужно создать API:

GET /api/v1/races

С авторизацией (auth:sanctum).

========================================================
ТРЕБОВАНИЯ
========================================================

1️⃣ Авторизация обязательна

Route должен быть внутри:

Route::middleware('auth:sanctum')

2️⃣ Вернуть только активные гонки

where('is_active', true)

3️⃣ Поддержать фильтрацию:

Query параметры:

- search (по location)
- type (enum значение)
- country (country_iso)
- date_from
- date_to

Все фильтры optional.

4️⃣ Сортировка:

По умолчанию:
orderBy('date')

5️⃣ Ответ в формате:

{
  "success": true,
  "data": [
      {
          "id": 1,
          "date": "2026-06-14",
          "location": "Hamburg",
          "type": "ironman",
          "type_label": "Ironman",
          "country_iso": "DE"
      }
  ]
}

6️⃣ Использовать API Resource

Создать:

App\Http\Resources\RaceResource

Добавить:
- type_label через $this->type->label()

7️⃣ Контроллер

Создать:

App\Http\Controllers\Api\V1\RaceController

Метод:

public function index(Request $request)

Использовать query builder аккуратно.

8️⃣ Валидация query параметров

Использовать:

$request->validate([
    'search' => ['nullable','string'],
    'type' => ['nullable','string'],
    'country' => ['nullable','string','size:2'],
    'date_from' => ['nullable','date'],
    'date_to' => ['nullable','date'],
]);

9️⃣ Обработка неверного enum type

Если передан невалидный type → вернуть 422.

10️⃣ Ничего не менять в других контроллерах.

========================================================
ФИНАЛЬНО
========================================================

Нужно создать:

- Route
- Controller
- Resource
- Валидацию
- Фильтрацию
- Авторизацию
- Чистый код

Без лишних комментариев.
Production-ready.
