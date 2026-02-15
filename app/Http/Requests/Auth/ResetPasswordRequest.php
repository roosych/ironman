<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $user = $this->user();
        $locale = $user?->locale ?? $this->input('locale') ?? config('app.locale', 'en');
        app()->setLocale($locale);

        return [
            'token.required' => trans('api.validation.auth.reset_password.token.required'),
            'email.required' => trans('api.validation.auth.reset_password.email.required'),
            'email.email' => trans('api.validation.auth.reset_password.email.email'),
            'email.exists' => trans('api.validation.auth.reset_password.email.exists'),
            'password.required' => trans('api.validation.auth.reset_password.password.required'),
            'password.confirmed' => trans('api.validation.auth.reset_password.password.confirmed'),
        ];
    }

    /**
     * Возврат валидационных ошибок в JSON вместо HTML.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
