<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'different:current_password', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $user = $this->user();
        $locale = $user?->locale ?? config('app.locale', 'en');
        app()->setLocale($locale);

        return [
            'current_password.required' => trans('api.validation.user.change_password.current_password.required'),
            'new_password.required' => trans('api.validation.user.change_password.new_password.required'),
            'new_password.min' => trans('api.validation.user.change_password.new_password.min'),
            'new_password.different' => trans('api.validation.user.change_password.new_password.different'),
            'new_password.confirmed' => trans('api.validation.user.change_password.new_password.confirmed'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors(),
        ], 422));
    }
}
