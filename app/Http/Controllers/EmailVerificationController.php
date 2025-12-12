<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    /**
     * Verify email address and show success/error page
     * 
     * Этот метод обрабатывает ссылки из email и показывает страницу-заглушку.
     * Ссылки из email должны быть веб-маршрутами, а не API-маршрутами.
     */
    public function verify(Request $request, int $id, string $hash): View
    {
        $user = User::find($id);

        // Пользователь не найден
        if (!$user) {
            return view('auth.verify-email-error', [
                'message' => 'Пользователь не найден. Пожалуйста, проверьте ссылку или зарегистрируйтесь заново.',
            ]);
        }

        // Проверка подписи (должна быть первой, чтобы защитить от подделки)
        if (!$request->hasValidSignature()) {
            return view('auth.verify-email-error', [
                'message' => 'Ссылка для подтверждения недействительна или истекла. Пожалуйста, запросите новую ссылку для подтверждения.',
            ]);
        }

        // Проверка hash
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return view('auth.verify-email-error', [
                'message' => 'Неверная ссылка для подтверждения. Пожалуйста, запросите новую ссылку для верификации.',
            ]);
        }

        // Уже подтвержден
        if ($user->hasVerifiedEmail()) {
            return view('auth.verify-email-success', [
                'message' => 'Ваш email уже был подтверждён ранее.',
                'user' => $user,
            ]);
        }

        // Подтверждаем email
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return view('auth.verify-email-success', [
            'message' => 'Ваш email успешно подтверждён! Теперь вы можете войти в систему.',
            'user' => $user,
        ]);
    }
}

