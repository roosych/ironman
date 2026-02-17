<?php

declare(strict_types=1);

namespace App\Http\Requests\UpcomingRace;

use App\Enums\RaceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'race_type' => ['required', Rule::enum(RaceType::class)],
            'location' => ['required', 'string', 'max:255'],
            'race_date' => ['required', 'date', 'after:today'],
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
            'race_type.required' => trans('api.validation.upcoming_race.store.race_type.required'),
            'race_type.Illuminate\Validation\Rules\Enum' => trans('api.validation.upcoming_race.store.race_type.enum'),
            'location.required' => trans('api.validation.upcoming_race.store.location.required'),
            'location.max' => trans('api.validation.upcoming_race.store.location.max'),
            'race_date.required' => trans('api.validation.upcoming_race.store.race_date.required'),
            'race_date.date' => trans('api.validation.upcoming_race.store.race_date.date'),
            'race_date.after' => trans('api.validation.upcoming_race.store.race_date.after'),
        ];
    }
}
