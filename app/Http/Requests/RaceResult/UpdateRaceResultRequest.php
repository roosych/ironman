<?php

declare(strict_types=1);

namespace App\Http\Requests\RaceResult;

use App\Enums\RaceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRaceResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->id === $this->route('raceResult')->user_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'race_date' => ['sometimes', 'date'],
            'location' => ['sometimes', 'string', 'max:255'],
            'race_type' => ['sometimes', Rule::enum(RaceType::class)],
            'swim_time' => ['sometimes', 'integer', 'min:0'],
            't1_time' => ['sometimes', 'integer', 'min:0'],
            'bike_time' => ['sometimes', 'integer', 'min:0'],
            't2_time' => ['sometimes', 'integer', 'min:0'],
            'run_time' => ['sometimes', 'integer', 'min:0'],
            'total_time' => ['sometimes', 'integer', 'min:0'],
            'age_group' => ['nullable', 'string', 'max:20'],
            'overall_position' => ['nullable', 'integer', 'min:1'],
            'age_group_position' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
