<?php

declare(strict_types=1);

namespace App\Http\Requests\ResultTransfer;

use App\Models\UserProfile;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateResultTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Авторизация проверяется через middleware
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'source_athlete_id' => [
                'required',
                'integer',
                Rule::exists(UserProfile::class, 'id')->where(function ($query) {
                    $query->where('role', 'athlete')
                          ->where('results_transferred', false);
                }),
            ],
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
            'source_athlete_id.required' => trans('api.validation.transfer.source_athlete_id.required'),
            'source_athlete_id.integer' => trans('api.validation.transfer.source_athlete_id.integer'),
            'source_athlete_id.exists' => trans('api.validation.transfer.source_athlete_id.exists'),
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