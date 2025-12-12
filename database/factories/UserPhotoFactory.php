<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPhoto>
 */
class UserPhotoFactory extends Factory
{
    protected $model = UserPhoto::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = $this->faker->uuid() . '.jpg';

        return [
            'user_id' => User::factory(),
            'path' => "profile_photos/{$filename}",
            'filename' => $filename,
            'is_avatar' => false,
        ];
    }

    public function avatar(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_avatar' => true,
        ]);
    }
}
