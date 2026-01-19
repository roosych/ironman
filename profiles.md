🧩 TASK: Upcoming Races (запланированные гонки атлетов)
🎯 Цель

Реализовать функционал upcoming гонок, которые атлет планирует пройти.

Атлет может:

создать будущую гонку

указать тип, локацию и дату

видеть свои upcoming гонки

видеть все upcoming гонки других атлетов

В будущем:

админ сможет подтверждать / скрывать гонки

🧠 Ключевые решения (ВАЖНО)
❓ Один эндпоинт или несколько?

➡️ Один эндпоинт со смарт-фильтрацией — лучше

Причины:

проще поддерживать

меньше дублирования логики

легко расширить (admin, pagination, search)

🗄 Модель данных
Таблица: upcoming_races
Schema::create('upcoming_races', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_profile_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->enum('race_type', [
        'ironman',
        'ironman_70_3',
        '5150'
    ]);

    $table->string('location');
    $table->date('race_date');

    // Для будущей модерации
    $table->boolean('is_active')->default(true);

    $table->timestamps();
});

🧩 Модель UpcomingRace
class UpcomingRace extends Model
{
    protected $fillable = [
        'user_profile_id',
        'race_type',
        'location',
        'race_date',
        'is_active',
    ];

    protected $casts = [
        'race_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function profile()
    {
        return $this->belongsTo(UserProfile::class, 'user_profile_id');
    }
}

🔗 Связь в UserProfile
public function upcomingRaces()
{
    return $this->hasMany(UpcomingRace::class);
}

📡 API эндпоинты
1️⃣ Создание upcoming гонки (атлет)
POST /api/v1/upcoming-races

Body
{
  "race_type": "ironman",
  "location": "Hamburg, Germany",
  "race_date": "2026-07-12"
}

Логика

user_profile_id берётся из авторизованного пользователя

is_active = true по умолчанию

проверка, что дата в будущем

2️⃣ Получение списка upcoming гонок (универсальный)
GET /api/v1/upcoming-races

Query params (опционально)
Параметр	Назначение
user_profile_id	Гонки конкретного атлета
race_type	Фильтр по типу
only_active	default = true
Примеры
/api/v1/upcoming-races
/api/v1/upcoming-races?user_profile_id=39
/api/v1/upcoming-races?race_type=ironman

📤 Формат ответа
{
  "success": true,
  "data": [
    {
      "id": 12,
      "race_type": "ironman",
      "race_type_label": "Ironman",
      "location": "Hamburg, Germany",
      "race_date": "2026-07-12",
      "is_active": true,
      "created_by": {
        "id": 39,
        "name": "Aydin Karimov",
        "avatar": "http://localhost:8000/storage/profile_photos/1/avatar.jpg"
      }
    }
  ]
}

🧠 Контроллер (логика получения)
public function index(Request $request)
{
    $query = UpcomingRace::with('profile.avatar')
        ->where('is_active', true)
        ->orderBy('race_date');

    if ($request->filled('user_profile_id')) {
        $query->where('user_profile_id', $request->user_profile_id);
    }

    if ($request->filled('race_type')) {
        $query->where('race_type', $request->race_type);
    }

    $races = $query->get();

    return response()->json([
        'success' => true,
        'data' => $races->map(fn ($race) => [
            'id' => $race->id,
            'race_type' => $race->race_type,
            'race_type_label' => $race->race_type_label,
            'location' => $race->location,
            'race_date' => $race->race_date->toDateString(),
            'is_active' => $race->is_active,
            'created_by' => [
                'id' => $race->profile->id,
                'name' => $race->profile->display_name,
                'avatar' => optional($race->profile->avatar)->url,
            ],
        ])
    ]);
}

🧪 Валидация (создание)
$request->validate([
    'race_type' => 'required|in:ironman,ironman_70_3,5150',
    'location' => 'required|string|max:255',
    'race_date' => 'required|date|after:today',
]);
