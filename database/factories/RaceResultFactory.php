<?php

namespace Database\Factories;

use App\Enums\RaceType;
use App\Models\RaceResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RaceResult>
 */
class RaceResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $raceType = fake()->randomElement(RaceType::cases());

        // Генерируем реалистичные времена в зависимости от типа гонки
        $times = $this->generateTimes($raceType);

        return [
            'user_id' => User::factory(),
            'race_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'location' => fake()->city().', '.fake()->country(),
            'race_type' => $raceType,
            'swim_time' => $times['swim'],
            't1_time' => $times['t1'],
            'bike_time' => $times['bike'],
            't2_time' => $times['t2'],
            'run_time' => $times['run'],
            'total_time' => $times['total'],
            'age_group' => fake()->optional(0.8)->randomElement([
                'M18-24', 'M25-29', 'M30-34', 'M35-39', 'M40-44', 'M45-49', 'M50-54', 'M55-59', 'M60-64',
                'F18-24', 'F25-29', 'F30-34', 'F35-39', 'F40-44', 'F45-49', 'F50-54', 'F55-59', 'F60-64',
            ]),
            'overall_position' => fake()->optional(0.7)->numberBetween(1, 2500),
            'age_group_position' => fake()->optional(0.7)->numberBetween(1, 300),
        ];
    }

    /**
     * Generate realistic times based on race type.
     *
     * @return array<string, int>
     */
    private function generateTimes(RaceType $raceType): array
    {
        return match ($raceType) {
            RaceType::Ironman => $this->ironmanTimes(),
            RaceType::Ironman703 => $this->ironman703Times(),
            RaceType::Sprint5150 => $this->sprint5150Times(),
        };
    }

    /**
     * @return array<string, int>
     */
    private function ironmanTimes(): array
    {
        // Full Ironman: 3.8km swim, 180km bike, 42.2km run
        $swim = fake()->numberBetween(3600, 5400);     // 1:00 - 1:30
        $t1 = fake()->numberBetween(300, 900);         // 5-15 min
        $bike = fake()->numberBetween(18000, 28800);   // 5:00 - 8:00
        $t2 = fake()->numberBetween(300, 720);         // 5-12 min
        $run = fake()->numberBetween(12600, 21600);    // 3:30 - 6:00

        return [
            'swim' => $swim,
            't1' => $t1,
            'bike' => $bike,
            't2' => $t2,
            'run' => $run,
            'total' => $swim + $t1 + $bike + $t2 + $run,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function ironman703Times(): array
    {
        // Ironman 70.3: 1.9km swim, 90km bike, 21.1km run
        $swim = fake()->numberBetween(1800, 3000);     // 30-50 min
        $t1 = fake()->numberBetween(180, 480);         // 3-8 min
        $bike = fake()->numberBetween(9000, 14400);    // 2:30 - 4:00
        $t2 = fake()->numberBetween(180, 420);         // 3-7 min
        $run = fake()->numberBetween(5400, 9000);      // 1:30 - 2:30

        return [
            'swim' => $swim,
            't1' => $t1,
            'bike' => $bike,
            't2' => $t2,
            'run' => $run,
            'total' => $swim + $t1 + $bike + $t2 + $run,
        ];
    }

    /**
     * @return array<string, int>
     */
    private function sprint5150Times(): array
    {
        // 5150: 1.5km swim, 40km bike, 10km run
        $swim = fake()->numberBetween(1200, 2100);     // 20-35 min
        $t1 = fake()->numberBetween(60, 300);          // 1-5 min
        $bike = fake()->numberBetween(3600, 5400);     // 1:00 - 1:30
        $t2 = fake()->numberBetween(60, 240);          // 1-4 min
        $run = fake()->numberBetween(2400, 3600);      // 40-60 min

        return [
            'swim' => $swim,
            't1' => $t1,
            'bike' => $bike,
            't2' => $t2,
            'run' => $run,
            'total' => $swim + $t1 + $bike + $t2 + $run,
        ];
    }

    /**
     * Set race type to Ironman.
     */
    public function ironman(): static
    {
        return $this->state(function (array $attributes) {
            $times = $this->ironmanTimes();

            return [
                'race_type' => RaceType::Ironman,
                'swim_time' => $times['swim'],
                't1_time' => $times['t1'],
                'bike_time' => $times['bike'],
                't2_time' => $times['t2'],
                'run_time' => $times['run'],
                'total_time' => $times['total'],
            ];
        });
    }

    /**
     * Set race type to Ironman 70.3.
     */
    public function ironman703(): static
    {
        return $this->state(function (array $attributes) {
            $times = $this->ironman703Times();

            return [
                'race_type' => RaceType::Ironman703,
                'swim_time' => $times['swim'],
                't1_time' => $times['t1'],
                'bike_time' => $times['bike'],
                't2_time' => $times['t2'],
                'run_time' => $times['run'],
                'total_time' => $times['total'],
            ];
        });
    }

    /**
     * Set race type to 5150.
     */
    public function sprint5150(): static
    {
        return $this->state(function (array $attributes) {
            $times = $this->sprint5150Times();

            return [
                'race_type' => RaceType::Sprint5150,
                'swim_time' => $times['swim'],
                't1_time' => $times['t1'],
                'bike_time' => $times['bike'],
                't2_time' => $times['t2'],
                'run_time' => $times['run'],
                'total_time' => $times['total'],
            ];
        });
    }
}
