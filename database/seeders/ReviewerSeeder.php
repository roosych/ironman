<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ReviewerSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = config('reviewer.email');
        $password = config('reviewer.password');
        $name = config('reviewer.name');

        if (!$email || !$password) {
            $this->command->error('REVIEWER_EMAIL и REVIEWER_PASSWORD должны быть установлены в .env файле!');
            return;
        }

        $reviewer = User::withoutGlobalScope('hide_reviewer')->firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_reviewer' => true,
                'email_verified_at' => now(),
            ]
        );

        // Обновляем пароль, если он изменился в конфиге
        if (!Hash::check($password, $reviewer->password)) {
            $reviewer->update([
                'password' => Hash::make($password),
            ]);
            $this->command->info('Пароль reviewer обновлён.');
        }

        // Убеждаемся, что is_reviewer установлен
        if (!$reviewer->is_reviewer) {
            $reviewer->update(['is_reviewer' => true]);
            $this->command->info('Флаг is_reviewer установлен.');
        }

        if ($reviewer->wasRecentlyCreated) {
            $this->command->info('Reviewer пользователь создан успешно!');
        } else {
            $this->command->info('Reviewer пользователь уже существует.');
        }
        
        $this->command->info('Email: ' . $email);
        $this->command->info('Name: ' . $name);

        // Создаем или находим профиль для reviewer
        $profileConfig = config('reviewer.profile');
        $externalId = $profileConfig['external_id'] ?? 'reviewer_user';
        $role = $profileConfig['role'] ?? 'athlete';
        $ironmanNumber = (int) ($profileConfig['ironman_number'] ?? 0);
        $profileName = $profileConfig['admin_full_name'] ?? $name;

        $profile = UserProfile::withoutGlobalScope('hide_reviewer_profiles')->firstOrCreate(
            [
                'user_id' => $reviewer->id,
            ],
            [
                'external_id' => $externalId,
                'admin_full_name' => $profileName,
                'role' => $role,
                'ironman_number' => $ironmanNumber,
                'bio' => null,
                'social_links' => null,
            ]
        );

        // Обновляем профиль, если он уже существовал, но не был привязан
        if (!$profile->wasRecentlyCreated) {
            $profile->update([
                'external_id' => $externalId,
                'admin_full_name' => $profileName,
                'role' => $role,
                'ironman_number' => $ironmanNumber,
            ]);
            $this->command->info('Профиль reviewer обновлён.');
        } else {
            $this->command->info('Профиль reviewer создан успешно!');
        }

        $this->command->info('Profile External ID: ' . $externalId);
        $this->command->info('Profile Role: ' . $role);
    }
}
