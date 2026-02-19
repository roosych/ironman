<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RaceType;
use App\Models\Race;
use Illuminate\Database\Seeder;

class RaceSeeder extends Seeder
{
    /**
     * Заполнить базу данных гонками.
     */
    public function run(): void
    {
        Race::create([
            'date' => '2026-06-14',
            'location' => 'Hamburg',
            'country_iso' => 'DE',
            'type' => RaceType::Ironman,
        ]);

        Race::create([
            'date' => '2026-05-20',
            'location' => 'Warsaw',
            'country_iso' => 'PL',
            'type' => RaceType::Ironman703,
        ]);

        Race::create([
            'date' => '2026-09-27',
            'location' => 'Berlin',
            'country_iso' => 'DE',
            'type' => RaceType::Sprint5150,
        ]);

        Race::create([
            'date' => '2026-07-10',
            'location' => 'Paris',
            'country_iso' => 'FR',
            'type' => RaceType::Ironman,
        ]);

        Race::create([
            'date' => '2026-08-18',
            'location' => 'Innsbruck',
            'country_iso' => 'AT',
            'type' => RaceType::Ironman703,
        ]);
    }
}
