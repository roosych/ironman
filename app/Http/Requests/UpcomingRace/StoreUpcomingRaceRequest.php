<?php

declare(strict_types=1);

namespace App\Http\Requests\UpcomingRace;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpcomingRaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'race_id' => ['required', 'integer', 'exists:races,id'],
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
            'race_id.required' => trans('api.validation.upcoming_race.store.race_id.required'),
            'race_id.integer' => trans('api.validation.upcoming_race.store.race_id.integer'),
            'race_id.exists' => trans('api.validation.upcoming_race.store.race_id.exists'),
        ];
    }
}
