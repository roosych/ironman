<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AthleteProfileSeeder extends Seeder
{
    /**
     * Идемпотентный сидер профилей атлетов.
     * Использует firstOrCreate для избежания дубликатов.
     */
    public function run(): void
    {
        $athletes = [
            ['admin_full_name' => 'Naghiyev Bahruz', 'ironman_number' => 0],
            ['admin_full_name' => 'Bakirov Elchin', 'ironman_number' => 0],
            ['admin_full_name' => 'Aliyev Elchin', 'ironman_number' => 0],
            ['admin_full_name' => 'Aliyev Elmar', 'ironman_number' => 0],
            ['admin_full_name' => 'Nafiyev Izzet', 'ironman_number' => 0],
            ['admin_full_name' => 'Najafov Orkhan', 'ironman_number' => 0],
            ['admin_full_name' => 'Mahmudov Tural', 'ironman_number' => 0],
            ['admin_full_name' => 'Ahmadzada Imran', 'ironman_number' => 0],
            ['admin_full_name' => 'Dovlatov Dovlat', 'ironman_number' => 0],
            ['admin_full_name' => 'Farajov Teymur', 'ironman_number' => 0],
            ['admin_full_name' => 'Mammadov Fariz', 'ironman_number' => 0],
            ['admin_full_name' => 'Akbarov Fakhri', 'ironman_number' => 0],
            ['admin_full_name' => 'Javadov Samir', 'ironman_number' => 0],
            ['admin_full_name' => 'Aliyev Kamil', 'ironman_number' => 0],
            ['admin_full_name' => 'Dushdurov Emil', 'ironman_number' => 0],
            ['admin_full_name' => 'Tural Mammadov', 'ironman_number' => 0],
            ['admin_full_name' => 'Javid Humbatov', 'ironman_number' => 0],
            ['admin_full_name' => 'Aynur Bayramova', 'ironman_number' => 0],
            ['admin_full_name' => 'Aliyev Azer', 'ironman_number' => 0],
            ['admin_full_name' => 'Maharramov İlgar', 'ironman_number' => 0],
            ['admin_full_name' => 'Kazimov Huseyn', 'ironman_number' => 0],
            ['admin_full_name' => 'Akhundov Rufat', 'ironman_number' => 0],
            ['admin_full_name' => 'Farhadzade Farid', 'ironman_number' => 0],
            ['admin_full_name' => 'Tahmaz Haziyev', 'ironman_number' => 0],
            ['admin_full_name' => 'Aslanzade Ulvi', 'ironman_number' => 0],
            ['admin_full_name' => 'Jabrayilov Samir', 'ironman_number' => 0],
            ['admin_full_name' => 'Rotkin Denis', 'ironman_number' => 0],
            ['admin_full_name' => 'Akhundov Elkhan', 'ironman_number' => 0],
            ['admin_full_name' => 'Parkhomovskiy Aleksandr', 'ironman_number' => 0],
            ['admin_full_name' => 'Abilov Kamran', 'ironman_number' => 0],
            ['admin_full_name' => 'Orujova Rabiya', 'ironman_number' => 0],
            ['admin_full_name' => 'Guliyev Khalid', 'ironman_number' => 0],
            ['admin_full_name' => 'Aliyev Hashim', 'ironman_number' => 0],
            ['admin_full_name' => 'Novruzov Altun', 'ironman_number' => 0],
            ['admin_full_name' => 'Suleymanova Adila', 'ironman_number' => 0],
            ['admin_full_name' => 'Abasov Sergan', 'ironman_number' => 0],
            ['admin_full_name' => 'Burlov Sergey', 'ironman_number' => 0],
            ['admin_full_name' => 'Yagizarov Ahmad', 'ironman_number' => 0],
            ['admin_full_name' => 'Karimov Aydin', 'ironman_number' => 0],
            ['admin_full_name' => 'Bunyadov Ali', 'ironman_number' => 0],
            ['admin_full_name' => 'Zulfigarov Rasim', 'ironman_number' => 0],
            ['admin_full_name' => 'Karimov Balaheydar', 'ironman_number' => 0],
            ['admin_full_name' => 'Suleymanov Celal', 'ironman_number' => 0],
            ['admin_full_name' => 'Hajibayov Jalal', 'ironman_number' => 0],
            ['admin_full_name' => 'Karimov Firdovsi', 'ironman_number' => 0],
            ['admin_full_name' => 'Garashova Gulshan', 'ironman_number' => 0],
            ['admin_full_name' => 'Mammadli Togrul', 'ironman_number' => 0],
            ['admin_full_name' => 'Alakbarov Tamerlan', 'ironman_number' => 0],
            ['admin_full_name' => 'Mirtagavi Azar', 'ironman_number' => 0],
            ['admin_full_name' => 'Salahov Vuqar', 'ironman_number' => 0],
            ['admin_full_name' => 'Safarov Sabuhi', 'ironman_number' => 0],
            ['admin_full_name' => 'Mamedov Ilgam', 'ironman_number' => 0],
            ['admin_full_name' => 'Gasim Farid', 'ironman_number' => 0],
            ['admin_full_name' => 'Najafova Diana', 'ironman_number' => 0],
            ['admin_full_name' => 'Najafov Boris', 'ironman_number' => 0],
            ['admin_full_name' => 'Karamov Murad', 'ironman_number' => 0],
            ['admin_full_name' => 'Gurbanov Isgandar', 'ironman_number' => 0],
            ['admin_full_name' => 'Abdullayev Jamil', 'ironman_number' => 0],
            ['admin_full_name' => 'Nuriyev Ziya', 'ironman_number' => 0],
            ['admin_full_name' => 'Novruzov Farrukh', 'ironman_number' => 0],
            ['admin_full_name' => 'Bayramov Yahya', 'ironman_number' => 0],
            ['admin_full_name' => 'Rotkin Timur', 'ironman_number' => 0],
            ['admin_full_name' => 'Ziyadov Gadzhiverdi', 'ironman_number' => 0],
            ['admin_full_name' => 'Safarov Javid', 'ironman_number' => 0],
            ['admin_full_name' => 'Gadzhibekov Dzhalal', 'ironman_number' => 0],
            ['admin_full_name' => 'Demirchi Gafar', 'ironman_number' => 0],
            ['admin_full_name' => 'Garajayev Hamid', 'ironman_number' => 0],
            ['admin_full_name' => 'Melikzade Fuad', 'ironman_number' => 0],
            ['admin_full_name' => 'Aliyeva Fatima', 'ironman_number' => 0],
            ['admin_full_name' => 'Hasanov Rufat', 'ironman_number' => 0],
            ['admin_full_name' => 'Soltanov Rinat', 'ironman_number' => 0],
        ];

        // Генерируем уникальные external_id для всех атлетов
        $athletesWithIds = [];
        $usedIds = [];

        foreach ($athletes as $athlete) {
            $baseId = Str::slug($athlete['admin_full_name'], '_');
            $externalId = $baseId;

            $counter = 2;
            while (in_array($externalId, $usedIds)) {
                $externalId = $baseId.'_'.$counter;
                $counter++;
            }

            $usedIds[] = $externalId;
            $athletesWithIds[] = [
                ...$athlete,
                'external_id' => $externalId,
            ];
        }

        $created = 0;
        $existing = 0;

        foreach ($athletesWithIds as $athlete) {
            $profile = UserProfile::firstOrCreate(
                ['external_id' => $athlete['external_id']],
                [
                    'user_id' => null,
                    'admin_full_name' => $athlete['admin_full_name'],
                    'role' => 'athlete',
                    'ironman_number' => $athlete['ironman_number'],
                    'bio' => null,
                    'social_links' => null,
                ]
            );

            if ($profile->wasRecentlyCreated) {
                $created++;
            } else {
                $existing++;
            }
        }

        $this->command->info("Профили атлетов: создано {$created}, уже существовало {$existing}");
    }
}
