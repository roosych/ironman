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
        $user = $this->user();
        $locale = $user?->locale ?? config('app.locale', 'en');
        app()->setLocale($locale);

        return [
            'photos.required' => trans('api.validation.profile.upload_photo.photos.required'),
            'photos.array' => trans('api.validation.profile.upload_photo.photos.array'),
            'photos.min' => trans('api.validation.profile.upload_photo.photos.min'),
            'photos.max' => trans('api.validation.profile.upload_photo.photos.max'),
            'photos.*.required' => trans('api.validation.profile.upload_photo.photos.*.required'),
            'photos.*.image' => trans('api.validation.profile.upload_photo.photos.*.image'),
            'photos.*.mimes' => trans('api.validation.profile.upload_photo.photos.*.mimes'),
            'photos.*.max' => trans('api.validation.profile.upload_photo.photos.*.max'),
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
