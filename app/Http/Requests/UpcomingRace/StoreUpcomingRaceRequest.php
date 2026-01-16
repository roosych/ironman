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
        return [
            'race_type.required' => 'Тип гонки обязателен.',
            'race_type.Illuminate\Validation\Rules\Enum' => 'Неверный тип гонки.',
            'location.required' => 'Локация обязательна.',
            'location.max' => 'Локация не должна превышать 255 символов.',
            'race_date.required' => 'Дата гонки обязательна.',
            'race_date.date' => 'Неверный формат даты.',
            'race_date.after' => 'Дата гонки должна быть в будущем.',
        ];
    }
}
