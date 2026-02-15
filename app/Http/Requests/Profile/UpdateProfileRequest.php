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
            'country_iso' => ['nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
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
        $user = $this->user();
        $locale = $user?->locale ?? config('app.locale', 'en');
        app()->setLocale($locale);

        return [
            'name.max' => trans('api.validation.profile.name.max'),
            'role.in' => trans('api.validation.profile.role.in'),
            'ironman_number.integer' => trans('api.validation.profile.ironman_number.integer'),
            'ironman_number.min' => trans('api.validation.profile.ironman_number.min'),
            'country_iso.string' => trans('api.validation.profile.country_iso.string'),
            'country_iso.size' => trans('api.validation.profile.country_iso.size'),
            'country_iso.regex' => trans('api.validation.profile.country_iso.regex'),
            'bio.max' => trans('api.validation.profile.bio.max'),
            'social_links.strava.url' => trans('api.validation.profile.social_links.strava.url'),
            'social_links.facebook.url' => trans('api.validation.profile.social_links.facebook.url'),
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
