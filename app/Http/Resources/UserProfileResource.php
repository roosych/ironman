<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\UserProfile
 */
class UserProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->relationLoaded('user') ? $this->user : null;

        return [
            'role' => $this->role,
            'ironman_number' => $this->ironman_number,
            'bio' => $this->bio,
            'social_links' => $this->social_links ?? [
                'strava' => null,
                'instagram' => null,
                'facebook' => null,
            ],
            'avatar' => $this->when(
                $user && $user->relationLoaded('avatar'),
                fn () => $user->avatar ? UserPhotoResource::make($user->avatar) : null,
                null
            ),
            'photos' => $this->when(
                $user && $user->relationLoaded('photos'),
                fn () => UserPhotoResource::collection($user->photos),
                []
            ),
        ];
    }
}
