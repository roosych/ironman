<?php

namespace Database\Seeders;

use App\Models\RaceResult;
use App\Models\User;
use Illuminate\Database\Seeder;

class RaceResultSeeder extends Seeder
{
    public function run(): void
    {
        // Используем только существующих юзеров, не создаём новых
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('Нет юзеров в базе. Сначала создайте юзеров.');

            return;
        }

        // Создаём результаты для каждого существующего юзера
        foreach ($users as $user) {
            // 2-5 случайных результатов на юзера
            RaceResult::factory()
                ->count(fake()->numberBetween(2, 5))
                ->create(['user_id' => $user->id]);
        }

        $this->command->info("Создано результатов для {$users->count()} юзеров.");
    }
}
