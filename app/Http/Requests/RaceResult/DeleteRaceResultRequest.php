<?php

declare(strict_types=1);

namespace App\Http\Requests\RaceResult;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRaceResultRequest extends FormRequest
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
        return [];
    }
}
