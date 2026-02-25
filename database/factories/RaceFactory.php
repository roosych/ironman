<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\RaceType;
use App\Models\Race;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Race>
 */
class RaceFactory extends Factory
{
    protected $model = Race::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'source_id' => $this->faker->optional(0.5)->numberBetween(10000, 999999),
            'type' => $this->faker->randomElement(RaceType::cases()),
            'location' => $this->faker->city().', '.$this->faker->country(),
            'date' => $this->faker->dateTimeBetween('+1 month', '+1 year'),
            'country_iso' => strtoupper($this->faker->countryCode()),
            'is_active' => true,
        ];
    }

    public function ironman(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => RaceType::Ironman,
        ]);
    }

    public function ironman703(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => RaceType::Ironman703,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => $this->faker->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }
}
