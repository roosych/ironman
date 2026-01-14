TASK: API — Смена пароля пользователя
Цель

Реализовать безопасный API-эндпоинт для смены пароля аутентифицированного пользователя, с проверкой текущего пароля, валидацией нового и корректными ответами.

🔐 Общие требования

Пользователь должен быть авторизован

Пароль хранится только в хешированном виде

Проверять текущий пароль

Новый пароль не должен совпадать с текущим

После успешной смены пароля:

текущая сессия остаётся активной

refresh / access токены не инвалидируются (на этом этапе)

📡 API
Endpoint

PUT /api/v1/user/password

Request Body
{
  "current_password": "old_password_123",
  "new_password": "newStrongPassword123",
  "new_password_confirmation": "newStrongPassword123"
}

Validation rules

current_password

required

string

new_password

required

string

min: 8

must be different from current_password

new_password_confirmation

required

must match new_password

📤 Responses
✅ Success (200)
{
  "success": true,
  "message": "Password updated successfully"
}

❌ Validation error (422)
{
  "success": false,
  "errors": {
    "new_password": [
      "The new password must be at least 8 characters."
    ]
  }
}

❌ Wrong current password (403)
{
  "success": false,
  "message": "Current password is incorrect"
}

❌ Unauthorized (401)
{
  "success": false,
  "message": "Unauthenticated"
}

🧠 Backend Logic (Laravel)
Контроллер

UserPasswordController или метод в UserController

Использовать Auth::user()

Алгоритм

Получить текущего пользователя

Проверить current_password через Hash::check

Проверить, что new_password !== current_password

Захешировать new_password

Сохранить в users.password

Вернуть success response

Пример псевдокода
$user = Auth::user();

if (!Hash::check($request->current_password, $user->password)) {
    return response()->json([
        'success' => false,
        'message' => 'Current password is incorrect'
    ], 403);
}

$user->password = Hash::make($request->new_password);
$user->save();

return response()->json([
    'success' => true,
    'message' => 'Password updated successfully'
]);

🛡 Security notes

Не логировать пароли

Не возвращать детали, какой именно пароль неверный

Использовать стандартный Laravel Hash

Не хранить пароль в profile или других таблицах

🧪 Testing

Неверный текущий пароль → 403

Новый пароль = старый → 422

Пароли не совпадают → 422

Успешная смена → 200

Неавторизованный запрос → 401

✅ Done criteria

Эндпоинт доступен только авторизованным пользователям

Пароль реально обновляется в БД

Старый пароль больше не работает

Ответы соответствуют спецификации

Код соответствует стилю проекта