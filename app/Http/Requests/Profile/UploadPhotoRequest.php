<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UploadPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // 5MB max
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.required' => 'Необходимо загрузить хотя бы одну фотографию.',
            'photos.array' => 'Фотографии должны быть переданы как массив.',
            'photos.min' => 'Необходимо загрузить хотя бы одну фотографию.',
            'photos.max' => 'Можно загрузить не более 10 фотографий за раз.',
            'photos.*.required' => 'Каждый файл должен быть изображением.',
            'photos.*.image' => 'Файл должен быть изображением.',
            'photos.*.mimes' => 'Допустимые форматы: jpeg, png, jpg, webp.',
            'photos.*.max' => 'Размер файла не должен превышать 5 МБ.',
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
