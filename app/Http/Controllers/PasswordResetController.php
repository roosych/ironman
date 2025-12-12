<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset form
     * 
     * Пользователь переходит по ссылке из письма и видит форму для ввода нового пароля.
     * Валидация токена происходит при отправке формы через API.
     */
    public function showResetForm(Request $request): View
    {
        $token = $request->query('token');
        $email = $request->query('email');

        // Проверка наличия обязательных параметров
        if (!$token || !$email) {
            return view('auth.reset-password-error', [
                'message' => 'Неверная ссылка для сброса пароля. Пожалуйста, запросите новую ссылку.',
            ]);
        }

        // Проверка существования пользователя (опционально, для лучшего UX)
        $user = User::where('email', $email)->first();
        if (!$user) {
            return view('auth.reset-password-error', [
                'message' => 'Пользователь с таким email не найден.',
            ]);
        }

        // Показываем форму - валидация токена произойдет при отправке через API
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Show success page after password reset
     */
    public function showSuccess(): View
    {
        return view('auth.reset-password-success');
    }
}

