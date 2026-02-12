<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => ['sometimes', 'string', 'max:255'],
            'role' => ['sometimes', 'string', Rule::in(['athlete', 'coach', 'admin'])],
            'ironman_number' => ['nullable', 'integer', 'min:1'],
            'country_iso' => ['nullable', 'string', 'size:2'],
            'bio' => ['nullable', 'string', 'max:500'],
            'social_links' => ['nullable', 'array'],
            'social_links.strava' => ['nullable', 'url', 'max:255'],
            'social_links.instagram' => ['nullable', 'string', 'max:255'],
            'social_links.facebook' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Имя не должно превышать 255 символов.',
            'role.in' => 'Роль должна быть одной из: athlete, coach, admin.',
            'ironman_number.integer' => 'Номер Ironman должен быть целым числом.',
            'ironman_number.min' => 'Номер Ironman должен быть положительным числом.',
            'country_iso.string' => 'Код страны должен быть строкой.',
            'country_iso.size' => 'Код страны должен состоять из 2 символов.',
            'bio.max' => 'Биография не должна превышать 500 символов.',
            'social_links.strava.url' => 'Ссылка на Strava должна быть валидным URL.',
            'social_links.facebook.url' => 'Ссылка на Facebook должна быть валидным URL.',
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
