<?php

declare(strict_types=1);

namespace App\Http\Requests\Fcm;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeleteFcmTokenRequest extends FormRequest
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
            'token' => ['required', 'string', 'max:500'],
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
            'token.required' => trans('api.validation.fcm.delete_token.token.required'),
            'token.string' => trans('api.validation.fcm.delete_token.token.string'),
            'token.max' => trans('api.validation.fcm.delete_token.token.max'),
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

