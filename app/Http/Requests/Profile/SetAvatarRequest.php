<?php

declare(strict_types=1);

namespace App\Http\Requests\Profile;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SetAvatarRequest extends FormRequest
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
        $userId = $this->user()?->id;

        return [
            'photo_id' => [
                'required',
                'integer',
                "exists:user_photos,id,user_id,{$userId}",
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo_id.required' => 'ID фотографии обязателен.',
            'photo_id.integer' => 'ID фотографии должен быть числом.',
            'photo_id.exists' => 'Фотография не найдена или не принадлежит вам.',
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
