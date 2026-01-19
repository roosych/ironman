# Инструкции для Flutter разработчика: Подтверждение результатов гонок

## Обзор изменений

Реализован функционал подтверждения результатов гонок администратором. Теперь все результаты, отправленные пользователями, требуют подтверждения администратором перед публикацией. Администратор работает через веб-интерфейс.

---

## 🔄 Изменения в API

### 1. Создание результата (POST `/api/v1/race-results`)

**Изменения:**
- Результаты теперь создаются с `is_approved: false` по умолчанию
- В ответе добавлено поле `is_approved: false`
- Добавлено сообщение: `"Результат отправлен на подтверждение администратором."`

**Пример запроса:**
```dart
final response = await http.post(
  Uri.parse('$baseUrl/api/v1/race-results'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'race_date': '2024-10-13',
    'location': 'Kona, Hawaii',
    'race_type': 'ironman',
    'swim_time': 4365,
    't1_time': 330,
    'bike_time': 18920,
    't2_time': 195,
    'run_time': 13530,
    'total_time': 37340,
    'age_group': 'M30-34',
    'overall_position': 156,
    'age_group_position': 12,
  }),
);
```

**Пример ответа:**
```json
{
  "success": true,
  "message": "Результат отправлен на подтверждение администратором.",
  "data": {
    "id": 123,
    "user_profile_id": 45,
    "race_date": "2024-10-13",
    "location": "Kona, Hawaii",
    "race_type": "ironman",
    "swim_time": "01:12:45",
    "total_time": "10:22:20",
    "is_approved": false,
    "approved_at": null,
    "approved_by": null
  }
}
```

---

### 2. Получение списка результатов (GET `/api/v1/race-results`)

**Изменения:**
- Теперь возвращаются **только подтвержденные** результаты (`is_approved: true`)
- Неподтвержденные результаты не отображаются в публичных списках

**Пример запроса:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/race-results?page=1'),
  headers: {
    'Authorization': 'Bearer $token',
  },
);
```

**Пример ответа:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "race_date": "2024-10-13",
      "location": "Kona, Hawaii",
      "race_type": "ironman",
      "is_approved": true,
      "approved_at": "2024-10-14T10:30:00.000000Z",
      "approved_by": {
        "id": 1,
        "name": "Admin User"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

---

### 3. Получение результата профиля (GET `/api/v1/profiles/{id}/race-results`)

**Изменения:**
- Возвращаются **только подтвержденные** результаты профиля

**Пример запроса:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/profiles/$profileId/race-results'),
  headers: {
    'Authorization': 'Bearer $token',
  },
);
```

---

### 4. Получение одного результата (GET `/api/v1/race-results/{id}`)

**Изменения:**
- Если результат не подтвержден, возвращается `404 Not Found`
- В ответе добавлены поля: `is_approved`, `approved_at`, `approved_by`

**Пример запроса:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/race-results/$resultId'),
  headers: {
    'Authorization': 'Bearer $token',
  },
);
```

---

## 📱 Рекомендации для Flutter приложения

### 1. Модель данных

Создайте модель для результата гонки:

```dart
class RaceResult {
  final int id;
  final int? userProfileId;
  final String name;
  final DateTime raceDate;
  final String location;
  final String raceType;
  final String raceTypeLabel;
  final String swimTime;
  final String t1Time;
  final String bikeTime;
  final String t2Time;
  final String runTime;
  final String totalTime;
  final String? ageGroup;
  final int? overallPosition;
  final int? ageGroupPosition;
  final bool isApproved;
  final DateTime? approvedAt;
  final Approver? approvedBy;

  RaceResult({
    required this.id,
    this.userProfileId,
    required this.name,
    required this.raceDate,
    required this.location,
    required this.raceType,
    required this.raceTypeLabel,
    required this.swimTime,
    required this.t1Time,
    required this.bikeTime,
    required this.t2Time,
    required this.runTime,
    required this.totalTime,
    this.ageGroup,
    this.overallPosition,
    this.ageGroupPosition,
    required this.isApproved,
    this.approvedAt,
    this.approvedBy,
  });

  factory RaceResult.fromJson(Map<String, dynamic> json) {
    return RaceResult(
      id: json['id'],
      userProfileId: json['user_profile_id'],
      name: json['name'] ?? '',
      raceDate: DateTime.parse(json['race_date']),
      location: json['location'],
      raceType: json['race_type'],
      raceTypeLabel: json['race_type_label'],
      swimTime: json['swim_time'],
      t1Time: json['t1_time'],
      bikeTime: json['bike_time'],
      t2Time: json['t2_time'],
      runTime: json['run_time'],
      totalTime: json['total_time'],
      ageGroup: json['age_group'],
      overallPosition: json['overall_position'],
      ageGroupPosition: json['age_group_position'],
      isApproved: json['is_approved'] ?? false,
      approvedAt: json['approved_at'] != null 
          ? DateTime.parse(json['approved_at']) 
          : null,
      approvedBy: json['approved_by'] != null 
          ? Approver.fromJson(json['approved_by']) 
          : null,
    );
  }
}

class Approver {
  final int id;
  final String name;

  Approver({required this.id, required this.name});

  factory Approver.fromJson(Map<String, dynamic> json) {
    return Approver(
      id: json['id'],
      name: json['name'],
    );
  }
}
```

---

### 2. Отображение статуса результата

Добавьте индикатор статуса в UI:

```dart
Widget buildStatusIndicator(RaceResult result) {
  if (result.isApproved) {
    return Row(
      children: [
        Icon(Icons.check_circle, color: Colors.green, size: 16),
        SizedBox(width: 4),
        Text(
          'Подтверждено',
          style: TextStyle(color: Colors.green, fontSize: 12),
        ),
      ],
    );
  } else {
    return Row(
      children: [
        Icon(Icons.hourglass_empty, color: Colors.orange, size: 16),
        SizedBox(width: 4),
        Text(
          'Ожидает подтверждения',
          style: TextStyle(color: Colors.orange, fontSize: 12),
        ),
      ],
    );
  }
}
```

---

### 3. После создания результата

Покажите пользователю сообщение:

```dart
Future<void> createRaceResult(RaceResultData data) async {
  try {
    final response = await apiService.createRaceResult(data);
    
    if (response['success'] == true) {
      // Показать SnackBar с сообщением
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            'Ваш результат отправлен на подтверждение администратором. '
            'Он появится в списке после проверки.',
          ),
          duration: Duration(seconds: 4),
          backgroundColor: Colors.blue,
        ),
      );
      
      // Обновить локальный список (если нужно)
      // Но помните: неподтвержденные результаты не показываются в публичных списках
    }
  } catch (e) {
    // Обработка ошибок
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text('Ошибка при создании результата: $e'),
        backgroundColor: Colors.red,
      ),
    );
  }
}
```

---

### 4. Обновление списков

**Важно:**
- После создания результата обновите локальный список, но отметьте его как `is_approved: false`
- Не показывайте неподтвержденные результаты в публичных списках
- Для личного профиля пользователя можно показывать все его результаты (включая неподтвержденные)

**Пример фильтрации:**
```dart
List<RaceResult> filterApprovedResults(List<RaceResult> results) {
  return results.where((result) => result.isApproved).toList();
}

// Для личного профиля - показываем все
List<RaceResult> getUserResults(List<RaceResult> results) {
  return results; // Все результаты пользователя, включая неподтвержденные
}
```

---

### 5. Обработка ошибок

```dart
Future<RaceResult?> getRaceResult(int resultId) async {
  try {
    final response = await http.get(
      Uri.parse('$baseUrl/api/v1/race-results/$resultId'),
      headers: {'Authorization': 'Bearer $token'},
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return RaceResult.fromJson(data['data']);
      }
    } else if (response.statusCode == 404) {
      // Результат не найден или не подтвержден
      throw Exception('Результат не найден или еще не подтвержден администратором');
    } else {
      throw Exception('Ошибка сервера: ${response.statusCode}');
    }
  } catch (e) {
    print('Ошибка при получении результата: $e');
    rethrow;
  }
  return null;
}
```

---

### 6. Отображение в списке результатов

```dart
ListView.builder(
  itemCount: approvedResults.length,
  itemBuilder: (context, index) {
    final result = approvedResults[index];
    return Card(
      child: ListTile(
        leading: result.isApproved
            ? Icon(Icons.check_circle, color: Colors.green)
            : Icon(Icons.hourglass_empty, color: Colors.orange),
        title: Text(result.location),
        subtitle: Text(
          '${result.raceTypeLabel} • ${result.raceDate.toString().split(' ')[0]}',
        ),
        trailing: Text(
          result.totalTime,
          style: TextStyle(fontWeight: FontWeight.bold),
        ),
        onTap: () {
          // Переход к деталям результата
          Navigator.push(
            context,
            MaterialPageRoute(
              builder: (context) => RaceResultDetailScreen(result: result),
            ),
          );
        },
      ),
    );
  },
)
```

---

## 📊 Структура данных результата

### Полная структура ответа:

```json
{
  "id": 123,
  "user_profile_id": 45,
  "name": "John Doe",
  "race_date": "2024-10-13",
  "location": "Kona, Hawaii",
  "race_type": "ironman",
  "race_type_label": "Ironman",
  "swim_time": "01:12:45",
  "t1_time": "00:05:30",
  "bike_time": "05:15:20",
  "t2_time": "00:03:15",
  "run_time": "03:45:30",
  "total_time": "10:22:20",
  "age_group": "M30-34",
  "overall_position": 156,
  "age_group_position": 12,
  "is_approved": true,
  "approved_at": "2024-10-14T10:30:00.000000Z",
  "approved_by": {
    "id": 1,
    "name": "Admin User"
  }
}
```

---

## ⚠️ Важные замечания

1. **Публичные API**: Все публичные эндпоинты (`GET /api/v1/race-results`, `GET /api/v1/profiles/{id}/race-results`) показывают только подтвержденные результаты.

2. **Создание результата**: После создания результата он имеет статус `is_approved: false` и не будет виден в публичных списках до подтверждения администратором.

3. **Личные результаты**: Пользователь может видеть свои неподтвержденные результаты через эндпоинт `/api/v1/auth/user` (если они включены в профиль).

4. **Обновление результатов**: Пользователь может обновлять свои неподтвержденные результаты. После обновления статус подтверждения сбрасывается на `false`.

5. **404 ошибка**: Если при получении результата возвращается 404, это может означать, что результат еще не подтвержден администратором.

---

## 🎨 UI/UX рекомендации

### Экран создания результата

После успешного создания покажите:
- ✅ Успешное сообщение: "Результат отправлен на подтверждение"
- ℹ️ Информацию: "Ваш результат будет проверен администратором и появится в списке после подтверждения"
- 🔄 Индикатор загрузки во время отправки

### Экран списка результатов

- Показывайте только подтвержденные результаты (`is_approved: true`)
- Добавьте индикатор "Подтверждено" (зеленая галочка) для визуального подтверждения
- Для личного профиля можно показать все результаты с разными индикаторами

### Экран деталей результата

- Показывайте информацию о подтверждении (если `approved_at` и `approved_by` не null)
- Можно добавить текст: "Подтверждено администратором [имя] [дата]"

---

## 📞 Вопросы?

Если возникнут вопросы по интеграции, обращайтесь к backend разработчику.
