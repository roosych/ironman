<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RaceType;
use App\Models\UpcomingRace;
use App\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UpcomingRace>
 */
class UpcomingRaceFactory extends Factory
{
    protected $model = UpcomingRace::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_profile_id' => UserProfile::factory(),
            'race_type' => $this->faker->randomElement(RaceType::cases()),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'race_date' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'is_active' => true,
        ];
    }

    public function ironman(): static
    {
        return $this->state(fn (array $attributes) => [
            'race_type' => RaceType::Ironman,
        ]);
    }

    public function ironman703(): static
    {
        return $this->state(fn (array $attributes) => [
            'race_type' => RaceType::Ironman703,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'race_date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }
}
