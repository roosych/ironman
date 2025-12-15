<?php

declare(strict_types=1);

namespace App\Http\Requests\RaceResult;

use App\Enums\RaceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRaceResultRequest extends FormRequest
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
            'race_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'race_type' => ['required', Rule::enum(RaceType::class)],
            'swim_time' => ['required', 'integer', 'min:0'],
            't1_time' => ['required', 'integer', 'min:0'],
            'bike_time' => ['required', 'integer', 'min:0'],
            't2_time' => ['required', 'integer', 'min:0'],
            'run_time' => ['required', 'integer', 'min:0'],
            'total_time' => ['required', 'integer', 'min:0'],
            'age_group' => ['nullable', 'string', 'max:20'],
            'overall_position' => ['nullable', 'integer', 'min:1'],
            'age_group_position' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
