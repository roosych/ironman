<?php

declare(strict_types=1);

namespace App\Http\Requests\RaceResult;

use Illuminate\Foundation\Http\FormRequest;

class DeleteRaceResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        $profile = $this->user()->profile;

        return $profile && $profile->id === $this->route('raceResult')->user_profile_id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
