<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'locale' => ['sometimes', 'string', 'in:ru,en'],
            'country_iso' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
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
            'name.required' => trans('api.validation.auth.register.name.required'),
            'email.required' => trans('api.validation.auth.register.email.required'),
            'email.email' => trans('api.validation.auth.register.email.email'),
            'email.unique' => trans('api.validation.auth.register.email.unique'),
            'password.required' => trans('api.validation.auth.register.password.required'),
            'password.confirmed' => trans('api.validation.auth.register.password.confirmed'),
            'country_iso.required' => trans('api.validation.auth.register.country_iso.required'),
            'country_iso.size' => trans('api.validation.auth.register.country_iso.size'),
            'country_iso.regex' => trans('api.validation.auth.register.country_iso.regex'),
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
