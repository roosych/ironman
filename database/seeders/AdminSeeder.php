<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('admin.email');
        $password = config('admin.password');
        $name = config('admin.name');

        if (!$email || !$password) {
            $this->command->error('ADMIN_EMAIL и ADMIN_PASSWORD должны быть установлены в .env файле!');
            return;
        }

        $admin = User::withoutGlobalScope('hide_reviewer')->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        // Обновляем пароль, если он изменился в конфиге
        if (!Hash::check($password, $admin->password)) {
            $admin->update([
                'password' => Hash::make($password),
            ]);
            $this->command->info('Пароль админа обновлён.');
        }

        // Убеждаемся, что is_admin установлен
        if (!$admin->is_admin) {
            $admin->update(['is_admin' => true]);
            $this->command->info('Права администратора установлены.');
        }

        if ($admin->wasRecentlyCreated) {
            $this->command->info('Админ создан успешно!');
        } else {
            $this->command->info('Админ уже существует.');
        }
        
        $this->command->info('Email: ' . $email);
        $this->command->info('Name: ' . $name);
    }
}
