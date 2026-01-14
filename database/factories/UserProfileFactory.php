<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserProfile>
 */
class UserProfileFactory extends Factory
{
    protected $model = UserProfile::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'admin_full_name' => $this->faker->name(),
            'role' => $this->faker->randomElement(['athlete', 'coach', 'admin']),
            'ironman_number' => $this->faker->optional()->numberBetween(1, 10000),
            'bio' => $this->faker->optional()->sentence(),
            'social_links' => [
                'strava' => $this->faker->optional()->url(),
                'instagram' => $this->faker->optional()->userName(),
                'facebook' => $this->faker->optional()->url(),
            ],
        ];
    }

    /**
     * Create a profile without a linked user (data-first architecture).
     */
    public function withoutUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
        ]);
    }

    public function athlete(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'athlete',
        ]);
    }

    public function coach(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'coach',
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }
}
