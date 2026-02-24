<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\RaceResult;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;

class RaceResultsSeeder extends Seeder
{
    /**
     * Идемпотентный сидер результатов гонок.
     * Использует updateOrCreate для избежания дубликатов.
     *
     * Времена указываются в секундах:
     * - 1 час = 3600 сек
     * - 1 мин = 60 сек
     * Пример: 1:30:45 = 1*3600 + 30*60 + 45 = 5445 сек
     *
     * race_type: 'ironman', 'ironman_70_3', '5150'
     */
    public function run(): void
    {
        $results = [
            // Пример результата:
            // [
            //     'external_id' => 'naghiyev_bahruz',  // должен совпадать с AthleteProfileSeeder
            //     'race_date' => '2024-09-15',
            //     'location' => 'Baku, Azerbaijan',
            //     'race_type' => 'ironman_70_3',
            //     'swim_time' => 2400,      // 40:00
            //     't1_time' => 300,         // 5:00
            //     'bike_time' => 9000,      // 2:30:00
            //     't2_time' => 180,         // 3:00
            //     'run_time' => 6600,       // 1:50:00
            //     'total_time' => 18480,    // 5:08:00
            //     'age_group' => 'M30-34',
            //     'overall_position' => 25,
            //     'age_group_position' => 5,
            // ],
            [
                'external_id' => 'bakirov_elchin',
                'race_date' => '2025-11-23',
                'location' => 'Cozumel, Mexico',
                'race_type' => 'ironman',
                'swim_time' => 4067,   // 01:07:47
                't1_time'   => 381,    // 00:06:21
                'bike_time' => 20868,  // 05:47:48
                't2_time'   => 224,    // 00:03:44
                'run_time'  => 16208,  // 04:30:08
                'total_time'=> 41746,  // 11:35:46
            ],
        
            [
                'external_id' => 'bakirov_elchin',
                'race_date' => '2025-10-19',
                'location' => 'Sacramento, California, USA',
                'race_type' => 'ironman',
                'swim_time' => 3301,   // 00:55:01
                't1_time'   => 487,    // 00:08:07
                'bike_time' => 18466,  // 05:07:46
                't2_time'   => 269,    // 00:04:29
                'run_time'  => 15446,  // 04:17:26
                'total_time'=> 37970,  // 10:32:50
            ],
        
            [
                'external_id' => 'bakirov_elchin',
                'race_date' => '2025-08-23',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman',
                'swim_time' => 4591,   // 01:16:31
                't1_time'   => 319,    // 00:05:19
                'bike_time' => 20321,  // 05:38:41
                't2_time'   => 433,    // 00:07:13
                'run_time'  => 13265,  // 03:41:05
                'total_time'=> 38928,  // 10:48:48
            ],
        
            [
                'external_id' => 'bakirov_elchin',
                'race_date' => '2024-11-24',
                'location' => 'Cozumel, Mexico',
                'race_type' => 'ironman',
                'swim_time' => 3564,   // 00:59:24
                't1_time'   => 399,    // 00:06:39
                'bike_time' => 24357,  // 06:45:57
                't2_time'   => 234,    // 00:03:54
                'run_time'  => 18614,  // 05:10:14
                'total_time'=> 47165,  // 13:06:05
            ],
        
            [
                'external_id' => 'bakirov_elchin',
                'race_date' => '2024-10-27',
                'location' => 'California, USA',
                'race_type' => 'ironman',
                'swim_time' => 3106,   // 00:51:46
                't1_time'   => 529,    // 00:08:49
                'bike_time' => 18835,  // 05:13:55
                't2_time'   => 340,    // 00:05:40
                'run_time'  => 17586,  // 04:53:06
                'total_time'=> 40393,  // 11:13:13
            ],
        
            [
                'external_id' => 'bakirov_elchin',
                'race_date' => '2024-06-23',
                'location' => 'Westfriesland, Netherlands',
                'race_type' => 'ironman_70_3',
                'swim_time' => 2018,   // 00:33:38
                't1_time'   => 340,    // 00:05:40
                'bike_time' => 9010,   // 02:30:10
                't2_time'   => 160,    // 00:02:40
                'run_time'  => 5256,   // 01:27:36
                'total_time'=> 16782,  // 04:39:42
            ],
        
            [
                'external_id' => 'bakirov_elchin',
                'race_date' => '2023-08-24',
                'location' => 'Copenhagen, Denmark',
                'race_type' => 'ironman',
                'swim_time' => 4045,   // 01:07:25
                't1_time'   => 436,    // 00:07:16
                'bike_time' => 23950,  // 06:39:10
                't2_time'   => 306,    // 00:05:06
                'run_time'  => 13470,  // 03:44:30
                'total_time' => 42207,  // 11:43:27
            ],
            [
                'external_id' => 'naghiyev_bahruz',
                'race_date' => '2025-11-30',
                'location' => 'Cartagena, Colombia',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2721,   // 00:45:21
                't1_time'    => 487,    // 00:08:07
                'bike_time'  => 10365,  // 02:52:45
                't2_time'    => 239,    // 00:03:59
                'run_time'   => 7477,   // 02:04:37
                'total_time' => 21289,  // 05:54:49 (≈ 05:54:48)
            ],
        
            [
                'external_id' => 'naghiyev_bahruz',
                'race_date' => '2024-10-26',
                'location' => 'Salalah, Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2904,   // 00:48:24
                't1_time'    => 353,    // 00:05:53
                'bike_time'  => 12115,  // 03:21:55
                't2_time'    => 330,    // 00:05:30
                'run_time'   => 8728,   // 02:25:28
                'total_time' => 24430,  // 06:47:10 (≈ 06:47:08)
            ],
        
            [
                'external_id' => 'naghiyev_bahruz',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2720,   // 00:45:20
                't1_time'    => 466,    // 00:07:46
                'bike_time'  => 10298,  // 02:51:38
                't2_time'    => 404,    // 00:06:44
                'run_time'   => 9328,   // 02:35:28
                'total_time' => 23216,  // 06:26:56 (≈ 06:26:54)
            ],
        
            [
                'external_id' => 'naghiyev_bahruz',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 5406,   // 01:30:06
                't1_time'    => 709,    // 00:11:49
                'bike_time'  => 26180,  // 07:16:20
                't2_time'    => 633,    // 00:10:33
                'run_time'   => 16281,  // 04:31:21
                'total_time' => 49209,  // 13:40:09 (≈ 13:40:07)
            ],
        
            [
                'external_id' => 'naghiyev_bahruz',
                'race_date' => '2022-06-12',
                'location' => 'Warsaw, Poland',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2637,   // 00:43:57
                't1_time'    => 391,    // 00:06:31
                'bike_time'  => 11289,  // 03:08:09
                't2_time'    => 341,    // 00:05:41
                'run_time'   => 8719,   // 02:25:19
                'total_time' => 23377,  // 06:29:37 (≈ 06:29:36)
            ],
            [
                'external_id' => 'aliyev_elchin',
                'race_date' => '2025-11-02',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2506,   // 00:41:46
                't1_time'    => 685,    // 00:11:25
                'bike_time'  => 9351,   // 02:35:51
                't2_time'    => 445,    // 00:07:25
                'run_time'   => 7285,   // 02:01:25
                'total_time' => 20272,  // 05:37:52 (≈ 05:37:50)
            ],
        
            [
                'external_id' => 'aliyev_elchin',
                'race_date' => '2024-11-29',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3215,   // 00:53:35
                't1_time'    => 457,    // 00:07:37
                'bike_time'  => 10128,  // 02:48:48
                't2_time'    => 357,    // 00:05:57
                'run_time'   => 6620,   // 01:50:20
                'total_time' => 20777,  // 05:46:17 (≈ 05:46:16)
            ],
        
            [
                'external_id' => 'aliyev_elchin',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2559,   // 00:42:39
                't1_time'    => 448,    // 00:07:28
                'bike_time'  => 9290,   // 02:34:50
                't2_time'    => 276,    // 00:04:36
                'run_time'   => 7299,   // 02:01:39
                'total_time' => 19872,  // 05:31:12 (≈ 05:31:10)
            ],
            [
                'external_id' => 'aliyev_elmar',
                'race_date' => '2025-11-02',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2541,   // 00:42:21
                't1_time'    => 654,    // 00:10:54
                'bike_time'  => 8835,   // 02:27:15
                't2_time'    => 380,    // 00:06:20
                'run_time'   => 6420,   // 01:47:00
                'total_time' => 18830,  // 05:13:50 (≈ 05:13:48)
            ],
        
            [
                'external_id' => 'aliyev_elmar',
                'race_date' => '2025-10-05',
                'location' => 'Calella, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 5287,   // 01:28:07
                't1_time'    => 383,    // 00:06:23
                'bike_time'  => 19841,  // 05:30:41
                't2_time'    => 420,    // 00:07:00
                'run_time'   => 15116,  // 04:11:56
                'total_time' => 41047,  // 11:24:07 (≈ 11:24:04)
            ],
        
            [
                'external_id' => 'aliyev_elmar',
                'race_date' => '2024-06-23',
                'location' => 'Westfriesland, Netherlands',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2696,   // 00:44:56
                't1_time'    => 323,    // 00:05:23
                'bike_time'  => 9421,   // 02:37:01
                't2_time'    => 309,    // 00:05:09
                'run_time'   => 7193,   // 01:59:53
                'total_time' => 19942,  // 05:32:22 (≈ 05:32:20)
            ],
        
            [
                'external_id' => 'aliyev_elmar',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2604,   // 00:43:24
                't1_time'    => 438,    // 00:07:18
                'bike_time'  => 9484,   // 02:38:04
                't2_time'    => 402,    // 00:06:42
                'run_time'   => 8804,   // 02:26:44
                'total_time' => 21732,  // 06:02:12 (≈ 06:02:10)
            ],
            [
                'external_id' => 'nafiyev_izzet',
                'race_date' => '2025-11-02',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2475,   // 00:41:15
                't1_time'    => 262,    // 00:04:22
                'bike_time'  => 7927,   // 02:12:07
                't2_time'    => 207,    // 00:03:27
                'run_time'   => 5734,   // 01:35:34
                'total_time' => 16605,  // 04:36:45 (≈ 04:36:43)
            ],
        
            [
                'external_id' => 'nafiyev_izzet',
                'race_date' => '2025-02-08',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2300,   // 00:38:20
                't1_time'    => 214,    // 00:03:34
                'bike_time'  => 8837,   // 02:27:17
                't2_time'    => 217,    // 00:03:37
                'run_time'   => 5943,   // 01:39:03
                'total_time' => 17511,  // 04:51:51 (≈ 04:51:50)
            ],
        
            [
                'external_id' => 'nafiyev_izzet',
                'race_date' => '2024-11-29',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2505,   // 00:41:45
                't1_time'    => 244,    // 00:04:04
                'bike_time'  => 8937,   // 02:28:57
                't2_time'    => 233,    // 00:03:53
                'run_time'   => 5608,   // 01:33:28
                'total_time' => 17527,  // 04:52:07 (≈ 04:52:06)
            ],
        
            [
                'external_id' => 'nafiyev_izzet',
                'race_date' => '2024-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2396,   // 00:39:56
                't1_time'    => 297,    // 00:04:57
                'bike_time'  => 8159,   // 02:15:59
                't2_time'    => 210,    // 00:03:30
                'run_time'   => 6030,   // 01:40:30
                'total_time' => 17092,  // 04:44:52 (≈ 04:44:51)
            ],
        
            [
                'external_id' => 'nafiyev_izzet',
                'race_date' => '2024-10-06',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 5058,   // 01:24:18
                't1_time'    => 389,    // 00:06:29
                'bike_time'  => 18704,  // 05:11:44
                't2_time'    => 224,    // 00:03:44
                'run_time'   => 16031,  // 04:27:11
                'total_time' => 40406,  // 11:13:26 (≈ 11:13:25)
            ],
        
            [
                'external_id' => 'nafiyev_izzet',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 5987,   // 01:39:47
                't1_time'    => 714,    // 00:11:54
                'bike_time'  => 19720,  // 05:28:40
                't2_time'    => 696,    // 00:11:36
                'run_time'   => 15342,  // 04:15:42
                'total_time' => 42459,  // 11:47:39 (≈ 11:47:38)
            ],
            [
                'external_id' => 'najafov_orkhan',
                'race_date' => '2025-10-05',
                'location' => 'Calella, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4663,   // 01:17:43
                't1_time'    => 260,    // 00:04:20
                'bike_time'  => 19167,  // 05:19:27
                't2_time'    => 331,    // 00:05:31
                'run_time'   => 15555,  // 04:19:15
                'total_time' => 39976,  // 11:06:16 (≈ 11:06:13)
            ],
        
            [
                'external_id' => 'najafov_orkhan',
                'race_date' => '2025-08-03',
                'location' => 'Krakow, Poland',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2246,   // 00:37:26
                't1_time'    => 382,    // 00:06:22
                'bike_time'  => 9593,   // 02:39:53
                't2_time'    => 234,    // 00:03:54
                'run_time'   => 7753,   // 02:09:13
                'total_time' => 20208,  // 05:36:48 (≈ 05:36:46)
            ],
        
            [
                'external_id' => 'najafov_orkhan',
                'race_date' => '2025-02-08',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2258,   // 00:37:38
                't1_time'    => 267,    // 00:04:27
                'bike_time'  => 10012,  // 02:46:52
                't2_time'    => 223,    // 00:03:43
                'run_time'   => 7088,   // 01:58:08
                'total_time' => 19848,  // 05:30:48 (≈ 05:30:46)
            ],
        
            [
                'external_id' => 'najafov_orkhan',
                'race_date' => '2024-06-23',
                'location' => 'Westfriesland, Netherlands',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2788,   // 00:46:28
                't1_time'    => 297,    // 00:04:57
                'bike_time'  => 9915,   // 02:45:15
                't2_time'    => 194,    // 00:03:14
                'run_time'   => 8336,   // 02:18:56
                'total_time' => 21530,  // 05:58:50 (≈ 05:58:47)
            ],
            [
                'external_id' => 'mahmudov_tural',
                'race_date' => '2025-10-05',
                'location' => 'Calella, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4650,   // 01:17:30
                't1_time'    => 315,    // 00:05:15
                'bike_time'  => 17997,  // 04:59:57
                't2_time'    => 246,    // 00:04:06
                'run_time'   => 13610,  // 03:46:50
                'total_time' => 36818,  // 10:13:38
            ],
        
            [
                'external_id' => 'mahmudov_tural',
                'race_date' => '2025-07-13',
                'location' => 'Luxembourg',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2494,   // 00:41:34
                't1_time'    => 424,    // 00:07:04
                'bike_time'  => 9599,   // 02:39:59
                't2_time'    => 292,    // 00:04:52
                'run_time'   => 6337,   // 01:45:37
                'total_time' => 19146,  // 05:19:06
            ],
        
            [
                'external_id' => 'mahmudov_tural',
                'race_date' => '2025-02-08',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2147,   // 00:35:47
                't1_time'    => 343,    // 00:05:43
                'bike_time'  => 8927,   // 02:28:47
                't2_time'    => 292,    // 00:04:52
                'run_time'   => 7119,   // 01:58:39
                'total_time' => 18828,  // 05:13:48 (≈ 05:13:46)
            ],
        
            [
                'external_id' => 'mahmudov_tural',
                'race_date' => '2024-10-06',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4765,   // 01:19:25
                't1_time'    => 395,    // 00:06:35
                'bike_time'  => 17935,  // 04:58:55
                't2_time'    => 354,    // 00:05:54
                'run_time'   => 13389,  // 03:43:09
                'total_time' => 36838,  // 10:13:58 (≈ 10:13:55)
            ],
        
            [
                'external_id' => 'mahmudov_tural',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2326,   // 00:38:46
                't1_time'    => 452,    // 00:07:32
                'bike_time'  => 8573,   // 02:22:53
                't2_time'    => 272,    // 00:04:32
                'run_time'   => 7030,   // 01:57:10
                'total_time' => 18653,  // 05:10:53 (≈ 05:10:51)
            ],
            [
                'external_id' => 'ahmadzada_imran',
                'race_date' => '2025-10-05',
                'location' => 'Calella, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4900,   // 01:21:40
                't1_time'    => 331,    // 00:05:31
                'bike_time'  => 20061,  // 05:34:21
                't2_time'    => 286,    // 00:04:46
                'run_time'   => 14603,  // 04:03:23
                'total_time' => 40181,  // 11:09:41 (≈ 11:09:39)
            ],
        
            [
                'external_id' => 'ahmadzada_imran',
                'race_date' => '2025-08-03',
                'location' => 'Krakow, Poland',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2491,   // 00:41:31
                't1_time'    => 542,    // 00:09:02
                'bike_time'  => 11391,  // 03:09:51
                't2_time'    => 413,    // 00:06:53
                'run_time'   => 7025,   // 01:57:05
                'total_time' => 21862,  // 06:04:22 (≈ 06:04:20)
            ],
        
            [
                'external_id' => 'ahmadzada_imran',
                'race_date' => '2025-02-08',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2295,   // 00:38:15
                't1_time'    => 405,    // 00:06:45
                'bike_time'  => 11658,  // 03:14:18
                't2_time'    => 277,    // 00:04:37
                'run_time'   => 6520,   // 01:48:40
                'total_time' => 21155,  // 05:52:35 (≈ 05:52:33)
            ],
            [
                'external_id' => 'dovlatov_dovlat',
                'race_date' => '2025-10-05',
                'location' => 'Calella, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 5080,   // 01:24:40
                't1_time'    => 428,    // 00:07:08
                'bike_time'  => 23048,  // 06:24:08
                't2_time'    => 540,    // 00:09:00
                'run_time'   => 16709,  // 04:38:29
                'total_time' => 45705,  // 12:43:25 (≈ 12:43:24)
            ],
        
            [
                'external_id' => 'dovlatov_dovlat',
                'race_date' => '2025-08-03',
                'location' => 'Krakow, Poland',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2442,   // 00:40:42
                't1_time'    => 461,    // 00:07:41
                'bike_time'  => 10561,  // 02:56:01
                't2_time'    => 335,    // 00:05:35
                'run_time'   => 6993,   // 01:56:33
                'total_time' => 20791,  // 05:46:31
            ],
        
            [
                'external_id' => 'dovlatov_dovlat',
                'race_date' => '2025-02-08',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2356,   // 00:39:16
                't1_time'    => 361,    // 00:06:01
                'bike_time'  => 10747,  // 02:59:07
                't2_time'    => 348,    // 00:05:48
                'run_time'   => 7050,   // 01:57:30
                'total_time' => 21262,  // 05:47:42 (≈ 05:47:39)
            ],

            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2025-09-14',
                'location' => 'IM WC Men',
                'race_type' => 'ironman',
                'swim_time'  => 5294,   // 01:28:14
                't1_time'    => 392,    // 00:06:32
                'bike_time'  => 22025,  // 06:07:05
                't2_time'    => 394,    // 00:06:34
                'run_time'   => 13493,  // 03:44:53
                'total_time' => 41598,  // 11:33:18 (≈ 11:33:15)
            ],
        
            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2025-06-01',
                'location' => 'Hamburg, Germany',
                'race_type' => 'ironman',
                'swim_time'  => 5389,   // 01:29:49
                't1_time'    => 370,    // 00:06:10
                'bike_time'  => 16383,  // 04:33:03
                't2_time'    => 325,    // 00:05:25
                'run_time'   => 11573,  // 03:12:53
                'total_time' => 34140,  // 09:27:20 (≈ 09:27:17)
            ],
        
            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2024-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2348,   // 00:39:08
                't1_time'    => 271,    // 00:04:31
                'bike_time'  => 8038,   // 02:13:58
                't2_time'    => 183,    // 00:03:03
                'run_time'   => 5405,   // 01:30:05
                'total_time' => 16245,  // 04:30:45 (≈ 04:30:43)
            ],
        
            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2024-10-06',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4800,   // 01:20:00
                't1_time'    => 340,    // 00:05:40
                'bike_time'  => 17234,  // 04:47:14
                't2_time'    => 193,    // 00:03:13
                'run_time'   => 12078,  // 03:21:18
                'total_time' => 34485,  // 09:37:15 (≈ 09:37:22)
            ],
        
            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2498,   // 00:41:38
                't1_time'    => 248,    // 00:04:08
                'bike_time'  => 8301,   // 02:18:21
                't2_time'    => 169,    // 00:02:49
                'run_time'   => 5687,   // 01:34:47
                'total_time' => 16903,  // 04:41:43 (≈ 04:41:41)
            ],
        
            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2023-08-24',
                'location' => 'Qatar Airways Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 5092,   // 01:24:52
                't1_time'    => 308,    // 00:05:08
                'bike_time'  => 17653,  // 04:54:13
                't2_time'    => 177,    // 00:02:57
                'run_time'   => 12538,  // 03:28:58
                'total_time' => 35768,  // 09:56:08
            ],
        
            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 4677,   // 01:17:57
                't1_time'    => 447,    // 00:07:27
                'bike_time'  => 18105,  // 05:01:45
                't2_time'    => 336,    // 00:05:36
                'run_time'   => 13166,  // 03:39:26
                'total_time' => 36728,  // 10:12:08
            ],
        
            [
                'external_id' => 'farajov_teymur',
                'race_date' => '2021-08-08',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3074,   // 00:51:14
                't1_time'    => 162,    // 00:02:42
                'bike_time'  => 9384,   // 02:36:24
                't2_time'    => 176,    // 00:02:56
                'run_time'   => 6559,   // 01:49:19
                'total_time' => 19365,  // 05:24:45 (≈ 05:24:47)
            ],
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2025-09-14',
                'location' => 'IM WC Men',
                'race_type' => 'ironman',
                'swim_time'  => 5138,   // 01:25:38
                't1_time'    => 618,    // 00:10:18
                'bike_time'  => 29606,  // 08:13:26
                't2_time'    => 653,    // 00:10:53
                'run_time'   => 17941,  // 04:59:01
                'total_time' => 53954,  // 14:59:14
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2024-11-29',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2498,   // 00:41:38
                't1_time'    => 678,    // 00:11:18
                'bike_time'  => 11076,  // 03:04:36
                't2_time'    => 553,    // 00:09:13
                'run_time'   => 6712,   // 01:51:52
                'total_time' => 21514,  // 05:58:34
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2023-09-10',
                'location' => 'World Championship France',
                'race_type' => 'ironman',
                'swim_time'  => 5337,   // 01:28:57
                't1_time'    => 447,    // 00:07:27
                'bike_time'  => 31250,  // 08:40:50
                't2_time'    => 499,    // 00:08:19
                'run_time'   => 18668,  // 05:11:08
                'total_time' => 56202,  // 15:36:42
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2022-12-09',
                'location' => 'Middle Eastern Championship Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2550,   // 00:42:30
                't1_time'    => 543,    // 00:09:03
                'bike_time'  => 10667,  // 02:57:47
                't2_time'    => 478,    // 00:07:58
                'run_time'   => 7687,   // 02:08:07
                'total_time' => 21923,  // 06:05:23
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2022-10-06',
                'location' => 'World Championship Kailua-Kona',
                'race_type' => 'ironman',
                'swim_time'  => 5683,   // 01:34:43
                't1_time'    => 742,    // 00:12:22
                'bike_time'  => 25398,  // 07:03:18
                't2_time'    => 750,    // 00:12:30
                'run_time'   => 17687,  // 04:54:47
                'total_time' => 49760,  // 13:57:40 (≈ 13:57:39)
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2022-03-05',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2339,   // 00:38:59
                't1_time'    => 125,    // 00:02:05
                'bike_time'  => 9708,   // 02:41:48
                't2_time'    => 381,    // 00:06:21
                'run_time'   => 6766,   // 01:52:46
                'total_time' => 19319,  // 05:25:59 (≈ 05:25:58)
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2483,   // 00:41:23
                't1_time'    => 544,    // 00:09:04
                'bike_time'  => 10556,  // 02:55:56
                't2_time'    => 511,    // 00:08:31
                'run_time'   => 7147,   // 01:59:07
                'total_time' => 21238,  // 05:53:58
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2021-08-07',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman',
                'swim_time'  => 6022,   // 01:40:22
                't1_time'    => 917,    // 00:15:17
                'bike_time'  => 27392,  // 07:36:32
                't2_time'    => 954,    // 00:15:54
                'run_time'   => 21121,  // 05:52:01
                'total_time' => 56406,  // 15:40:06
            ],
        
            [
                'external_id' => 'mammadov_fariz',
                'race_date' => '2021-03-12',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2599,   // 00:43:19
                't1_time'    => 739,    // 00:12:19
                'bike_time'  => 10157,  // 02:49:17
                't2_time'    => 572,    // 00:09:32
                'run_time'   => 7935,   // 02:12:15
                'total_time' => 22002,  // 06:06:41 (≈ 06:06:40)
            ],
            [
                'external_id' => 'akbarov_fakhri',
                'race_date' => '2025-09-14',
                'location' => 'IM Italy Emilia-Romagna',
                'race_type' => 'ironman',
                'swim_time'  => 6119,   // 01:41:59
                't1_time'    => 992,    // 00:16:32
                'bike_time'  => 26463,  // 07:21:03
                't2_time'    => 715,    // 00:11:55
                'run_time'   => 21460,  // 05:57:40
                'total_time' => 55149,  // 15:19:49 (≈ 15:29:07)
            ],
        
            [
                'external_id' => 'akbarov_fakhri',
                'race_date' => '2024-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2574,   // 00:42:54
                't1_time'    => 496,    // 00:08:16
                'bike_time'  => 9257,   // 02:34:17
                't2_time'    => 348,    // 00:05:48
                'run_time'   => 6985,   // 01:56:25
                'total_time' => 19657,  // 05:27:37
            ],
        
            [
                'external_id' => 'akbarov_fakhri',
                'race_date' => '2024-10-06',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 5346,   // 01:29:06
                't1_time'    => 604,    // 00:10:04
                'bike_time'  => 20709,  // 05:45:09
                't2_time'    => 1144,   // 00:19:04
                'run_time'   => 15901,  // 04:25:01
                'total_time' => 43702,  // 12:08:22
            ],
        
            [
                'external_id' => 'akbarov_fakhri',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 5345,   // 01:29:05
                't1_time'    => 924,    // 00:15:24
                'bike_time'  => 20964,  // 05:49:24
                't2_time'    => 933,    // 00:15:33
                'run_time'   => 14746,  // 04:05:46
                'total_time' => 42910,  // 11:55:10
            ],
        
            [
                'external_id' => 'akbarov_fakhri',
                'race_date' => '2021-08-08',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3390,   // 00:56:30
                't1_time'    => 368,    // 00:06:08
                'bike_time'  => 11179,  // 03:06:19
                't2_time'    => 366,    // 00:06:06
                'run_time'   => 7835,   // 02:10:35
                'total_time' => 23138,  // 06:25:38 (≈ 06:25:40)
            ],
            [
                'external_id' => 'javadov_samir',
                'race_date' => '2025-09-14',
                'location' => 'IM Italy Emilia-Romagna',
                'race_type' => 'ironman',
                'swim_time'  => 5120,   // 01:25:20
                't1_time'    => 747,    // 00:12:27
                'bike_time'  => 27303,  // 07:35:03
                't2_time'    => 497,    // 00:08:17
                'run_time'   => 15790,  // 04:23:10
                'total_time' => 48957,  // 13:44:17 (≈ 13:44:16)
            ],
        
            [
                'external_id' => 'javadov_samir',
                'race_date' => '2025-06-01',
                'location' => 'Hamburg, Germany',
                'race_type' => 'ironman',
                'swim_time'  => 5602,   // 01:33:22
                't1_time'    => 633,    // 00:10:33
                'bike_time'  => 21633,  // 06:00:33
                't2_time'    => 400,    // 00:06:40
                'run_time'   => 13132,  // 03:38:52
                'total_time' => 41399,  // 11:29:59
            ],
        
            [
                'external_id' => 'javadov_samir',
                'race_date' => '2024-10-06',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4827,   // 01:20:27
                't1_time'    => 449,    // 00:07:29
                'bike_time'  => 22604,  // 06:16:44
                't2_time'    => 318,    // 00:05:18
                'run_time'   => 14082,  // 03:54:42
                'total_time' => 42180,  // 11:44:40 (≈ 11:44:38)
            ],
        
            [
                'external_id' => 'javadov_samir',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2646,   // 00:44:06
                't1_time'    => 380,    // 00:06:20
                'bike_time'  => 10783,  // 02:59:43
                't2_time'    => 376,    // 00:06:16
                'run_time'   => 6786,   // 01:53:06
                'total_time' => 21171,  // 05:49:31 (≈ 05:49:29)
            ],
        
            [
                'external_id' => 'javadov_samir',
                'race_date' => '2023-08-24',
                'location' => 'Qatar Airways Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 4866,   // 01:21:06
                't1_time'    => 434,    // 00:07:14
                'bike_time'  => 23068,  // 06:24:28
                't2_time'    => 268,    // 00:04:28
                'run_time'   => 15325,  // 04:15:25
                'total_time' => 43961,  // 12:12:41 (≈ 12:12:39)
            ],
            [
                'external_id' => 'aliyev_kamil',
                'race_date' => '2025-08-16',
                'location' => 'IM Kalmar, Sweden',
                'race_type' => 'ironman',
                'swim_time'  => 4375,   // 01:12:55
                't1_time'    => 308,    // 00:05:08
                'bike_time'  => 18567,  // 05:09:27
                't2_time'    => 317,    // 00:05:17
                'run_time'   => 16140,  // 04:29:00
                'total_time' => 39807,  // 11:01:47 (≈ 11:01:45)
            ],
        
            [
                'external_id' => 'aliyev_kamil',
                'race_date' => '2024-10-06',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4281,   // 01:11:21
                't1_time'    => 283,    // 00:04:43
                'bike_time'  => 18712,  // 05:11:52
                't2_time'    => 222,    // 00:03:42
                'run_time'   => 14921,  // 04:08:41
                'total_time' => 38419,  // 10:40:18 (≈ 10:40:19)
            ],
        
            [
                'external_id' => 'aliyev_kamil',
                'race_date' => '2023-08-24',
                'location' => 'Qatar Airways Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 4399,   // 01:13:19
                't1_time'    => 318,    // 00:05:18
                'bike_time'  => 19017,  // 05:16:57
                't2_time'    => 192,    // 00:03:12
                'run_time'   => 14260,  // 03:57:40
                'total_time' => 38086,  // 10:36:26 (≈ 10:36:23)
            ],
        
            [
                'external_id' => 'aliyev_kamil',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 4369,   // 01:12:49
                't1_time'    => 397,    // 00:06:37
                'bike_time'  => 19513,  // 05:25:13
                't2_time'    => 388,    // 00:06:28
                'run_time'   => 14308,  // 03:58:28
                'total_time' => 39475,  // 10:49:35 (≈ 10:49:34)
            ],
        
            [
                'external_id' => 'aliyev_kamil',
                'race_date' => '2021-08-07',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman',
                'swim_time'  => 5000,   // 01:23:20
                't1_time'    => 316,    // 00:05:16
                'bike_time'  => 21140,  // 05:52:20
                't2_time'    => 443,    // 00:07:23
                'run_time'   => 17706,  // 04:55:06
                'total_time' => 44606,  // 12:23:26
            ],
            [
                'external_id' => 'dushdurov_emil',
                'race_date' => '2025-08-16',
                'location' => 'IM Kalmar, Sweden',
                'race_type' => 'ironman',
                'swim_time'  => 5133,   // 01:25:33
                't1_time'    => 392,    // 00:06:32
                'bike_time'  => 20400,  // 05:40:00
                't2_time'    => 349,    // 00:05:49
                'run_time'   => 15233,  // 04:13:53
                'total_time' => 41505,  // 11:31:45
            ],
        
            [
                'external_id' => 'dushdurov_emil',
                'race_date' => '2023-09-16',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 5241,   // 01:27:21
                't1_time'    => 460,    // 00:07:40
                'bike_time'  => 21535,  // 05:58:55
                't2_time'    => 415,    // 00:06:55
                'run_time'   => 15721,  // 04:22:01
                'total_time' => 43371,  // 12:02:51
            ],
        
            [
                'external_id' => 'dushdurov_emil',
                'race_date' => '2022-10-15',
                'location' => 'Portugal Carcais',
                'race_type' => 'ironman',
                'swim_time'  => 5117,   // 01:25:17
                't1_time'    => 634,    // 00:10:34
                'bike_time'  => 22644,  // 06:17:24
                't2_time'    => 716,    // 00:11:56
                'run_time'   => 15704,  // 04:21:44
                'total_time' => 44813,  // 12:26:53
            ],
        
            [
                'external_id' => 'dushdurov_emil',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2625,   // 00:43:45
                't1_time'    => 453,    // 00:07:33
                'bike_time'  => 10116,  // 02:48:36
                't2_time'    => 461,    // 00:07:41
                'run_time'   => 7146,   // 01:59:06
                'total_time' => 20801,  // 05:46:05 (≈ 05:46:09)
            ],
        
            [
                'external_id' => 'dushdurov_emil',
                'race_date' => '2019-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2576,   // 00:42:56
                't1_time'    => 209,    // 00:03:29
                'bike_time'  => 10609,  // 02:56:49
                't2_time'    => 199,    // 00:03:19
                'run_time'   => 7733,   // 02:08:53
                'total_time' => 21465,  // 05:57:45
            ],
            [
                'external_id' => 'tural_mammadov',
                'race_date' => '2025-08-16',
                'location' => 'IM Kalmar, Sweden',
                'race_type' => 'ironman',
                'swim_time'  => 4733,   // 01:18:53
                't1_time'    => 340,    // 00:05:40
                'bike_time'  => 21922,  // 06:05:22
                't2_time'    => 312,    // 00:05:12
                'run_time'   => 16693,  // 04:38:13
                'total_time' => 44000,  // 12:13:20 (≈ 12:13:18)
            ],
            [
                'external_id' => 'javid_humbatov',
                'race_date' => '2025-07-13',
                'location' => 'Luxembourg',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2727,   // 00:45:27
                't1_time'    => 554,    // 00:09:14
                'bike_time'  => 12798,  // 03:33:18
                't2_time'    => 386,    // 00:06:26
                'run_time'   => 8446,   // 02:20:46
                'total_time' => 24911,  // 06:55:11
            ],
            [
                'external_id' => 'aynur_bayramova',
                'race_date' => '2025-06-08',
                'location' => 'Warsaw, Poland',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3705,   // 01:01:45
                't1_time'    => 195,    // 00:03:15
                'bike_time'  => 14595,  // 04:03:15
                't2_time'    => 59,     // 00:00:59
                'run_time'   => 8394,   // 02:19:54
                'total_time' => 26828,  // 07:27:08
            ],
            [
                'external_id' => 'aliyev_azer',
                'race_date' => '2025-02-08',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2596,   // 00:43:16
                't1_time'    => 304,    // 00:05:04
                'bike_time'  => 12196,  // 03:23:16
                't2_time'    => 334,    // 00:05:34
                'run_time'   => 7479,   // 02:04:39
                'total_time' => 22907,  // 06:21:47
            ],
            [
                'external_id' => 'maharramov_ilgar',
                'race_date' => '2024-11-03',
                'location' => 'Türkiye',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2486,   // 00:41:26
                't1_time'    => 539,    // 00:08:59
                'bike_time'  => 11337,  // 03:08:57
                't2_time'    => 499,    // 00:08:19
                'run_time'   => 7642,   // 02:07:22
                'total_time' => 22501,  // 06:15:01
            ],
            [
                'external_id' => 'kazimov_huseyn',
                'race_date' => '2024-08-24',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman',
                'swim_time'  => 7102,   // 01:58:22
                't1_time'    => 186,    // 00:03:06
                'bike_time'  => 27244,  // 07:34:04
                't2_time'    => 771,    // 00:12:51
                'run_time'   => 22476,  // 06:14:36
                'total_time' => 57779,  // 16:02:59 (≈16:12:59)
            ],
            [
                'external_id' => 'akhundov_rufat',
                'race_date' => '2024-08-24',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman',
                'swim_time'  => 5725,   // 01:35:25
                't1_time'    => 150,    // 00:02:30
                'bike_time'  => 23806,  // 06:36:46
                't2_time'    => 418,    // 00:06:58
                'run_time'   => 22236,  // 06:10:36
                'total_time' => 52235,  // 14:30:35 (≈14:43:34)
            ],
            [
                'external_id' => 'farhadzade_farid',
                'race_date' => '2024-06-23',
                'location' => 'Elsinore, Denmark',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2505,   // 00:41:45
                't1_time'    => 132,    // 00:02:12
                'bike_time'  => 11708,  // 03:15:08
                't2_time'    => 254,    // 00:04:14
                'run_time'   => 9185,   // 02:33:05
                'total_time' => 23784,  // 06:36:24 (≈06:41:39)
            ],
            [
                'external_id' => 'farhadzade_farid',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2731,   // 00:45:31
                't1_time'    => 409,    // 00:06:49
                'bike_time'  => 11068,  // 03:04:28
                't2_time'    => 260,    // 00:04:20
                'run_time'   => 9445,   // 02:37:25
                'total_time' => 23911,  // 06:38:31
            ],
            [
                'external_id' => 'farhadzade_farid',
                'race_date' => '2023-05-07',
                'location' => 'Venice-Jesolo, Italy',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2489,   // 00:41:29
                't1_time'    => 130,    // 00:02:10
                'bike_time'  => 10646,  // 02:57:26
                't2_time'    => 240,    // 00:04:00
                'run_time'   => 8699,   // 02:24:59
                'total_time' => 22560,  // 06:16:00
            ],
            [
                'external_id' => 'farhadzade_farid',
                'race_date' => '2022-12-09',
                'location' => 'Bahrain (Middle Eastern Championship)',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2837,   // 00:47:17
                't1_time'    => 176,    // 00:02:56
                'bike_time'  => 11290,  // 03:08:10
                't2_time'    => 176,    // 00:02:56
                'run_time'   => 9001,   // 02:30:01
                'total_time' => 23558,  // 06:32:38
            ],
            [
                'external_id' => 'farhadzade_farid',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 4969,   // 01:22:49
                't1_time'    => 130,    // 00:02:10
                'bike_time'  => 25595,  // 07:06:35
                't2_time'    => 574,    // 00:09:34
                'run_time'   => 18767,  // 05:12:47
                'total_time' => 50503,  // 14:01:43
            ],
            [
                'external_id' => 'farhadzade_farid',
                'race_date' => '2022-03-05',
                'location' => 'Dubai, UAE',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2811,   // 00:46:51
                't1_time'    => 308,    // 00:05:08
                'bike_time'  => 11485,  // 03:11:25
                't2_time'    => 308,    // 00:05:08
                'run_time'   => 9791,   // 02:43:11
                'total_time' => 24590,  // 06:49:50
            ],
            [
                'external_id' => 'farhadzade_farid',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3619,   // 01:00:19
                't1_time'    => 468,    // 00:07:48
                'bike_time'  => 12484,  // 03:28:04
                't2_time'    => 238,    // 00:03:58
                'run_time'   => 11899,  // 03:18:19
                'total_time' => 28368,  // 07:52:46 (≈07:58:25)
            ],
            [
                'external_id' => 'tahmaz_haziyev',
                'race_date' => '2024-05-05',
                'location' => 'Venice Jesolo, Italy',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2834,   // 00:47:14
                't1_time'    => 619,    // 00:10:19
                'bike_time'  => 13329,  // 03:42:09
                't2_time'    => 731,    // 00:12:11
                'run_time'   => 8721,   // 02:25:21
                'total_time' => 26234,  // 07:17:14
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2024-04-21',
                'location' => 'Valencia, Spain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2265,   // 00:37:45
                't1_time'    => 119,    // 00:01:59
                'bike_time'  => 10652,  // 02:57:32
                't2_time'    => 487,    // 00:08:07
                'run_time'   => 6308,   // 01:45:08
                'total_time' => 21831,  // 06:03:51 (≈06:36:25)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2022-12-09',
                'location' => 'Bahrain (Middle Eastern Championship)',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2364,   // 00:39:24
                't1_time'    => 164,    // 00:02:44
                'bike_time'  => 9559,   // 02:39:19
                't2_time'    => 347,    // 00:05:47
                'run_time'   => 5988,   // 01:39:48
                'total_time' => 18533,  // 05:08:53
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 4367,   // 01:12:47
                't1_time'    => 115,    // 00:01:55
                'bike_time'  => 19348,  // 05:22:28
                't2_time'    => 549,    // 00:09:09
                'run_time'   => 13042,  // 03:37:22
                'total_time' => 37321,  // 10:22:31 (≈10:31:47)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2022-06-05',
                'location' => 'Hamburg, Germany',
                'race_type' => 'ironman',
                'swim_time'  => 4509,   // 01:15:09
                't1_time'    => 118,    // 00:01:58
                'bike_time'  => 22290,  // 06:11:30
                't2_time'    => 478,    // 00:07:58
                'run_time'   => 12794,  // 03:33:14
                'total_time' => 40189,  // 11:11:49 (≈11:28:42)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2283,   // 00:38:03
                't1_time'    => 120,    // 00:02:00
                'bike_time'  => 8958,   // 02:29:18
                't2_time'    => 217,    // 00:03:37
                'run_time'   => 5783,   // 01:36:23
                'total_time' => 17361,  // 04:49:41 (≈04:54:10)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2020-11-01',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2164,   // 00:36:04
                't1_time'    => 113,    // 00:01:53
                'bike_time'  => 8880,   // 02:28:00
                't2_time'    => 205,    // 00:03:25
                'run_time'   => 5893,   // 01:38:13
                'total_time' => 17255,  // 04:47:55 (≈04:52:45)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2020-02-21',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2144,   // 00:35:44
                't1_time'    => 112,    // 00:01:52
                'bike_time'  => 9690,   // 02:41:30
                't2_time'    => 234,    // 00:03:54
                'run_time'   => 6068,   // 01:41:08
                'total_time' => 18248,  // 05:04:08 (≈05:07:44)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2019-08-17',
                'location' => 'Kalmar, Sweden',
                'race_type' => 'ironman',
                'swim_time'  => 4777,   // 01:19:37
                't1_time'    => 126,    // 00:02:06
                'bike_time'  => 18979,  // 05:16:19
                't2_time'    => 368,    // 00:06:08
                'run_time'   => 13330,  // 03:42:10
                'total_time' => 37480,  // 10:24:00 (≈10:32:22)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2019-05-19',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2757,   // 00:45:57
                't1_time'    => 145,    // 00:02:25
                'bike_time'  => 12397,  // 03:26:37
                't2_time'    => 312,    // 00:05:12
                'run_time'   => 6374,   // 01:46:14
                'total_time' => 22085,  // 06:08:28 (≈06:21:11)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2018-10-28',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2250,   // 00:37:30
                't1_time'    => 118,    // 00:01:58
                'bike_time'  => 8934,   // 02:28:54
                't2_time'    => 193,    // 00:03:13
                'run_time'   => 5331,   // 01:28:51
                'total_time' => 15026,  // 04:10:26 (≈04:44:46)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2018-07-08',
                'location' => 'Frankfurt, Germany',
                'race_type' => 'ironman',
                'swim_time'  => 5464,   // 01:31:04
                't1_time'    => 143,    // 00:02:23
                'bike_time'  => 24638,  // 06:50:38
                't2_time'    => 436,    // 00:07:16
                'run_time'   => 16017,  // 04:26:57
                'total_time' => 46638,  // 12:57:58 (≈13:11:03)
            ],
            [
                'external_id' => 'aslanzade_ulvi',
                'race_date' => '2017-10-15',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2584,   // 00:43:04
                't1_time'    => 136,    // 00:02:16
                'bike_time'  => 9316,   // 02:35:16
                't2_time'    => 344,    // 00:05:44
                'run_time'   => 5954,   // 01:39:14
                'total_time' => 18334,  // 05:05:34 (≈05:10:43)
            ],
            [
                'external_id' => 'jabrayilov_samir',
                'race_date' => '2023-10-29',
                'location' => 'Antalya, Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2228,   // 00:37:08
                't1_time'    => 288,    // 00:04:48
                'bike_time'  => 7788,   // 02:09:48
                't2_time'    => 174,    // 00:02:54
                'run_time'   => 6180,   // 01:43:00
                'total_time' => 16658,  // 04:37:38 (≈ 04:37:37)
            ],
            [
                'external_id' => 'rotkin_denis',
                'race_date' => '2023-10-01',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 5225,    // 01:27:05
                't1_time'    => 603,     // 00:10:03
                'bike_time'  => 25916,   // 07:11:56
                't2_time'    => 826,     // 00:13:46
                'run_time'   => 16819,   // 04:40:19
                'total_time' => 48709,   // 13:31:29 (≈ 13:43:07 – округлено по сумме этапов)
            ],
            [
                'external_id' => 'akhundov_elkhan',
                'race_date' => '2023-10-01',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3913,    // 01:05:13
                't1_time'    => 824,     // 00:13:44
                'bike_time'  => 11427,   // 03:10:27
                't2_time'    => 548,     // 00:09:08
                'run_time'   => 10531,   // 02:55:31
                'total_time' => 27143,   // 07:32:03 (сумма всех этапов)
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2023-09-16',
                'location' => 'Emilia-Romagna, Italy',
                'race_type' => 'ironman',
                'swim_time'  => 4470,   // 01:14:30
                't1_time'    => 408,    // 00:06:48
                'bike_time'  => 19367,  // 05:22:47
                't2_time'    => 498,    // 00:08:18
                'run_time'   => 15824,  // 04:23:44
                'total_time' => 40568,  // 11:16:08
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2023-08-07',
                'location' => 'Duisburg, Germany',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2297,   // 00:38:17
                't1_time'    => 254,    // 00:04:14
                'bike_time'  => 8836,   // 02:27:16
                't2_time'    => 254,    // 00:04:14
                'run_time'   => 6073,   // 01:41:13
                'total_time' => 17776,  // 04:56:16
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2022-11-06',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2198,   // 00:36:38
                't1_time'    => 231,    // 00:03:51
                'bike_time'  => 8518,   // 02:21:58
                't2_time'    => 231,    // 00:03:51
                'run_time'   => 6277,   // 01:44:37
                'total_time' => 17500,  // 04:51:40
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2022-05-29',
                'location' => 'Kraichgau, Germany',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2338,   // 00:38:58
                't1_time'    => 207,    // 00:03:27
                'bike_time'  => 10354,  // 02:52:34
                't2_time'    => 207,    // 00:03:27
                'run_time'   => 6189,   // 01:43:09
                'total_time' => 19326,  // 05:22:06
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2160,   // 00:36:00
                't1_time'    => 327,    // 00:05:27
                'bike_time'  => 9469,   // 02:37:49
                't2_time'    => 216,    // 00:03:36
                'run_time'   => 6111,   // 01:41:51
                'total_time' => 18281,  // 05:04:41
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2021-10-03',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman',
                'swim_time'  => 4644,   // 01:17:24
                't1_time'    => 344,    // 00:05:44
                'bike_time'  => 19721,  // 05:28:41
                't2_time'    => 426,    // 00:07:06
                'run_time'   => 15289,  // 04:14:49
                'total_time' => 40423,  // 11:13:43
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2021-08-08',
                'location' => 'Tallinn, Estonia',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2342,   // 00:39:02
                't1_time'    => 232,    // 00:03:52
                'bike_time'  => 9220,   // 02:33:40
                't2_time'    => 232,    // 00:03:52
                'run_time'   => 6437,   // 01:47:17
                'total_time' => 18446,  // 05:07:26
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2019-08-17',
                'location' => 'Kalmar, Sweden',
                'race_type' => 'ironman',
                'swim_time'  => 4709,   // 01:18:29
                't1_time'    => 296,    // 00:04:56
                'bike_time'  => 19873,  // 05:31:13
                't2_time'    => 492,    // 00:08:12
                'run_time'   => 20576,  // 05:42:56
                'total_time' => 45946,  // 12:45:46
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2019-05-19',
                'location' => 'Barcelona, Spain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2629,   // 00:43:49
                't1_time'    => 306,    // 00:05:06
                'bike_time'  => 11716,  // 03:15:16
                't2_time'    => 306,    // 00:05:06
                'run_time'   => 7153,   // 01:59:13
                'total_time' => 22280,  // 06:11:20
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2018-10-28',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2096,   // 00:34:56
                't1_time'    => 381,    // 00:06:21
                'bike_time'  => 8783,   // 02:26:23
                't2_time'    => 381,    // 00:06:21
                'run_time'   => 6981,   // 01:56:21
                'total_time' => 19659,  // 05:27:39
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2018-07-08',
                'location' => 'Frankfurt, Germany',
                'race_type' => 'ironman',
                'swim_time'  => 4660,   // 01:17:40
                't1_time'    => 406,    // 00:06:46
                'bike_time'  => 20484,  // 05:41:24
                't2_time'    => 406,    // 00:06:46
                'run_time'   => 17827,  // 04:57:07
                'total_time' => 44169,  // 12:16:09
            ],
            [
                'external_id' => 'parkhomovskiy_aleksandr',
                'race_date' => '2017-01-27',
                'location' => 'Dubai, UAE',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2941,   // 00:49:01
                't1_time'    => 517,    // 00:08:37
                'bike_time'  => 9986,   // 02:46:26
                't2_time'    => 517,    // 00:08:37
                'run_time'   => 9730,   // 02:42:10
                'total_time' => 23846,  // 06:37:26
            ],
            [
                'external_id' => 'abilov_kamran',
                'race_date' => '2023-08-24',
                'location' => 'Qatar Airways Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 4534,   // 01:15:34
                't1_time'    => 610,    // 00:10:10
                'bike_time'  => 26591,  // 07:23:11
                't2_time'    => 626,    // 00:10:26
                'run_time'   => 23244,  // 06:27:24
                'total_time' => 55603,  // 15:26:43
            ],
            [
                'external_id' => 'orujova_rabiya',
                'race_date' => '2023-08-24',
                'location' => 'Qatar Airways Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 5204,
                't1_time'    => 480,
                'bike_time'  => 21252,
                't2_time'    => 415,
                'run_time'   => 14181,
                'total_time' => 41532,
            ],
            [
                'external_id' => 'orujova_rabiya',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna',
                'race_type' => 'ironman',
                'swim_time'  => 5388,
                't1_time'    => 741,
                'bike_time'  => 21912,
                't2_time'    => 490,
                'run_time'   => 14085,
                'total_time' => 42516,
            ],
            [
                'external_id' => 'orujova_rabiya',
                'race_date' => '2022-04-03',
                'location' => 'African Championship South Africa',
                'race_type' => 'ironman',
                'swim_time'  => 5320,
                't1_time'    => 300,
                'bike_time'  => 26660,
                't2_time'    => 1434,
                'run_time'   => 16208,
                'total_time' => 49922,
            ],
            [
                'external_id' => 'orujova_rabiya',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3546,
                't1_time'    => 488,
                'bike_time'  => 10021,
                't2_time'    => 268,
                'run_time'   => 7189,
                'total_time' => 21412,
            ],
            [
                'external_id' => 'guliyev_khalid',
                'race_date' => '2023-07-02',
                'location' => 'European Championship Frankfurt',
                'race_type' => 'ironman',
                'swim_time'  => 6023,   // 01:40:23
                't1_time'    => 1060,   // 17:40
                'bike_time'  => 26459,  // 07:20:59
                't2_time'    => 593,    // 09:53
                'run_time'   => 19384,  // 05:23:04
                'total_time' => 53517,  // 14:51:57
            ],
            [
                'external_id' => 'guliyev_khalid',
                'race_date' => '2019-09-22',
                'location' => 'Emilia-Romagna',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 1965,   // 00:32:45
                't1_time'    => 599,    // 09:59
                'bike_time'  => 4462,   // 01:14:22
                't2_time'    => 343,    // 05:43
                'run_time'   => 3994,   // 01:06:34
                'total_time' => 11361,  // 03:09:21
            ],
            [
                'external_id' => 'aliyev_hashim',
                'race_date' => '2022-11-06',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2458,   // 00:40:58
                't1_time'    => 400,    // 06:40
                'bike_time'  => 10502,  // 2:55:02
                't2_time'    => 412,    // 06:52
                'run_time'   => 8353,   // 2:19:13
                'total_time' => 22125,  // 06:08:45
            ],
            [
                'external_id' => 'novruzov_altun',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna',
                'race_type' => 'ironman',
                'swim_time'  => 5449,   // 01:30:49
                't1_time'    => 735,    // 12:15
                'bike_time'  => 19690,  // 05:28:10
                't2_time'    => 464,    // 07:44
                'run_time'   => 15147,  // 04:12:27
                'total_time' => 41483,  // 11:31:23
            ],
            [
                'external_id' => 'novruzov_altun',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2746,   // 00:45:46
                't1_time'    => 301,    // 05:01
                'bike_time'  => 9096,   // 02:31:36
                't2_time'    => 203,    // 03:23
                'run_time'   => 6539,   // 01:48:59
                'total_time' => 18884,  // 05:14:44
            ],
            [
                'external_id' => 'novruzov_altun',
                'race_date' => '2019-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2484,   // 00:41:24
                't1_time'    => 265,    // 04:25
                'bike_time'  => 9203,   // 02:33:23
                't2_time'    => 172,    // 02:52
                'run_time'   => 6487,   // 01:48:07
                'total_time' => 18610,  // 05:10:10
            ],
            [
                'external_id' => 'novruzov_altun',
                'race_date' => '2018-10-28',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3416,   // 00:56:56
                't1_time'    => 380,    // 06:20
                'bike_time'  => 9869,   // 02:44:29
                't2_time'    => 173,    // 02:53
                'run_time'   => 6191,   // 01:43:11
                'total_time' => 20029,  // 05:33:49
            ],
            [
                'external_id' => 'suleymanova_adila',
                'race_date' => '2022-09-17',
                'location' => 'Emilia-Romagna',
                'race_type' => 'ironman',
                'swim_time'  => 6249,   // 01:44:09
                't1_time'    => 619,    // 10:19
                'bike_time'  => 27170,  // 07:32:50
                't2_time'    => 430,    // 07:10
                'run_time'   => 18172,  // 05:02:52
                'total_time' => 52638,  // 14:37:18
            ],
            [
                'external_id' => 'suleymanova_adila',
                'race_date' => '2022-03-05',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3105,   // 00:51:45
                't1_time'    => 733,    // 12:13
                'bike_time'  => 13864,  // 03:51:04
                't2_time'    => 371,    // 06:11
                'run_time'   => 8718,   // 02:25:18
                'total_time' => 26792,  // 07:26:32
            ],
            [
                'external_id' => 'abasov_sergan',
                'race_date' => '2022-08-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2697,   // 00:44:57
                't1_time'    => 490,    // 08:10
                'bike_time'  => 9440,   // 02:37:20
                't2_time'    => 283,    // 04:43
                'run_time'   => 7423,   // 02:03:43
                'total_time' => 20333,  // 05:38:53
            ],
            [
                'external_id' => 'abasov_sergan',
                'race_date' => '2021-08-07',
                'location' => 'Tallinn',
                'race_type' => 'ironman',
                'swim_time'  => 5245,   // 01:27:25
                't1_time'    => 389,    // 06:29
                'bike_time'  => 20625,  // 05:43:45
                't2_time'    => 570,    // 09:30
                'run_time'   => 17515,  // 04:51:55
                'total_time' => 44345,  // 12:19:05
            ],
            [
                'external_id' => 'abasov_sergan',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2522,   // 00:42:02
                't1_time'    => 338,    // 05:38
                'bike_time'  => 10128,  // 02:48:48
                't2_time'    => 285,    // 04:45
                'run_time'   => 8427,   // 02:20:27
                'total_time' => 21701,  // 06:01:41
            ],
            [
                'external_id' => 'burlov_sergey',
                'race_date' => '2022-08-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2470,   // 00:41:10
                't1_time'    => 550,    // 09:10
                'bike_time'  => 10687,  // 02:58:07
                't2_time'    => 199,    // 03:19
                'run_time'   => 9525,   // 02:38:45
                'total_time' => 23431,  // 06:30:31
            ],
            [
                'external_id' => 'yagizarov_ahmad',
                'race_date' => '2022-06-26',
                'location' => 'Mont-Tremblant',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2146,   // 00:35:46
                't1_time'    => 435,    // 07:15
                'bike_time'  => 12680,  // 03:31:20
                't2_time'    => 301,    // 05:01
                'run_time'   => 7296,   // 02:01:36
                'total_time' => 22856,  // 06:20:56
            ],
            [
                'external_id' => 'yagizarov_ahmad',
                'race_date' => '2016-07-30',
                'location' => 'Budapest',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2289,   // 00:38:09
                't1_time'    => 423,    // 07:03
                'bike_time'  => 11771,  // 03:16:11
                't2_time'    => 501,    // 08:21
                'run_time'   => 7755,   // 02:09:15
                'total_time' => 22739,  // 06:18:59
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2022-05-07',
                'location' => 'World Championship St. George',
                'race_type' => 'ironman',
                'swim_time'  => 4406,   // 01:13:26
                't1_time'    => 411,    // 06:51
                'bike_time'  => 23297,  // 06:28:17
                't2_time'    => 609,    // 10:09
                'run_time'   => 22620,  // 06:17:00
                'total_time' => 51341,  // 14:15:41
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2022-02-24',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2055,   // 00:34:15
                't1_time'    => 169,    // 02:49
                'bike_time'  => 9566,   // 02:39:26
                't2_time'    => 210,    // 03:30
                'run_time'   => 6932,   // 01:55:32
                'total_time' => 18930,  // 05:15:30
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2021-09-18',
                'location' => 'World Championship',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2437,   // 00:40:37
                't1_time'    => 356,    // 05:56
                'bike_time'  => 11646,  // 03:14:06
                't2_time'    => 238,    // 03:58
                'run_time'   => 7916,   // 02:11:56
                'total_time' => 22592,  // 06:16:32
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2021-08-07',
                'location' => 'Tallinn',
                'race_type' => 'ironman',
                'swim_time'  => 4691,   // 01:18:11
                't1_time'    => 304,    // 05:04
                'bike_time'  => 20765,  // 05:46:05
                't2_time'    => 437,    // 07:17
                'run_time'   => 16289,  // 04:31:29
                'total_time' => 42488,  // 11:48:08
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2021-06-13',
                'location' => 'Eagleman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2235,   // 00:37:15
                't1_time'    => 244,    // 04:04
                'bike_time'  => 9267,   // 02:34:27
                't2_time'    => 234,    // 03:54
                'run_time'   => 7415,   // 02:03:35
                'total_time' => 19429,  // 05:23:49
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2021-03-12',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2120,   // 00:35:20
                't1_time'    => 402,    // 06:42
                'bike_time'  => 8851,   // 02:27:31
                't2_time'    => 359,    // 05:59
                'run_time'   => 7310,   // 02:01:50
                'total_time' => 19039,  // 05:17:19
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2019-10-06',
                'location' => 'Barcelona',
                'race_type' => 'ironman',
                'swim_time'  => 4056,   // 01:07:36
                't1_time'    => 339,    // 05:39
                'bike_time'  => 18520,  // 05:08:40
                't2_time'    => 416,    // 06:56
                'run_time'   => 16098,  // 04:28:18
                'total_time' => 39426,  // 10:57:06
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2283,   // 00:38:03
                't1_time'    => 215,    // 03:35
                'bike_time'  => 9546,   // 02:39:06
                't2_time'    => 149,    // 02:29
                'run_time'   => 8206,   // 02:16:46
                'total_time' => 20340,  // 05:39:00
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2018-10-28',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2369,   // 00:39:29
                't1_time'    => 580,    // 09:40
                'bike_time'  => 9264,   // 02:34:24
                't2_time'    => 534,    // 08:54
                'run_time'   => 7856,   // 02:10:56
                'total_time' => 20603,  // 05:43:23
            ],
            [
                'external_id' => 'karimov_aydin',
                'race_date' => '2018-09-23',
                'location' => 'Emilia-Romagna',
                'race_type' => '5150',
                'swim_time'  => 1800,   // 00:30:00
                't1_time'    => 307,    // 05:07
                'bike_time'  => 4800,   // 01:20:00
                't2_time'    => 300,    // 05:00
                'run_time'   => 4800,   // 01:20:00
                'total_time' => 12007,  // 03:20:07
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2022-03-05',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2468,   // 00:41:08
                't1_time'    => 194,    // 03:14
                'bike_time'  => 9427,   // 02:37:07
                't2_time'    => 161,    // 02:41
                'run_time'   => 6403,   // 01:46:43
                'total_time' => 18653,  // 05:10:53
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2020-11-01',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2373,   // 00:39:33
                't1_time'    => 192,    // 03:12
                'bike_time'  => 9457,   // 02:37:37
                't2_time'    => 125,    // 02:05
                'run_time'   => 6842,   // 01:54:02
                'total_time' => 18989,  // 05:16:29
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2020-02-21',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2104,   // 00:35:04
                't1_time'    => 195,    // 03:15
                'bike_time'  => 9547,   // 02:39:07
                't2_time'    => 135,    // 02:15
                'run_time'   => 6680,   // 01:51:20
                'total_time' => 18662,  // 05:11:02
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2020-02-07',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2245,   // 00:37:25
                't1_time'    => 206,    // 03:26
                'bike_time'  => 8383,   // 02:19:43
                't2_time'    => 170,    // 02:50
                'run_time'   => 6365,   // 01:46:05
                'total_time' => 17368,  // 04:49:28
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2019-09-22',
                'location' => 'Slovenia',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2329,   // 00:38:49
                't1_time'    => 155,    // 02:35
                'bike_time'  => 11351,  // 03:09:11
                't2_time'    => 177,    // 02:57
                'run_time'   => 6680,   // 01:51:20
                'total_time' => 20690,  // 05:44:50
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2019-08-17',
                'location' => 'Kalmar',
                'race_type' => 'ironman',
                'swim_time'  => 4210,   // 01:10:10
                't1_time'    => 154,    // 02:34
                'bike_time'  => 19146,  // 05:19:06
                't2_time'    => 160,    // 02:40
                'run_time'   => 14023,  // 03:53:43
                'total_time' => 37693,  // 10:28:13
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2328,   // 00:38:48
                't1_time'    => 156,    // 02:36
                'bike_time'  => 9011,   // 02:30:11
                't2_time'    => 119,    // 01:59
                'run_time'   => 5964,   // 01:39:24
                'total_time' => 17578,  // 04:52:58
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2019-05-19',
                'location' => 'Barcelona',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2319,   // 00:38:39
                't1_time'    => 337,    // 05:37
                'bike_time'  => 11894,  // 03:18:14
                't2_time'    => 171,    // 02:51
                'run_time'   => 6715,   // 01:51:55
                'total_time' => 21436,  // 05:57:16
            ],
            [
                'external_id' => 'bunyadov_ali',
                'race_date' => '2018-10-28',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2763,   // 00:46:03
                't1_time'    => 373,    // 06:13
                'bike_time'  => 10335,  // 02:52:15
                't2_time'    => 368,    // 06:08
                'run_time'   => 7875,   // 02:11:15
                'total_time' => 21714,  // 06:01:54
            ],
            [
                'external_id' => 'zulfigarov_rasim',
                'race_date' => '2021-12-11',
                'location' => 'Florida',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2912,   // 00:48:32
                't1_time'    => 689,    // 11:29
                'bike_time'  => 14238,  // 03:57:18
                't2_time'    => 355,    // 05:55
                'run_time'   => 10433,  // 02:53:53
                'total_time' => 25024,  // 06:57:04
            ],
            [
                'external_id' => 'karimov_balaheydar',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2618,   // 00:43:38
                't1_time'    => 540,    // 09:00
                'bike_time'  => 11402,  // 03:10:02
                't2_time'    => 403,    // 06:43
                'run_time'   => 9033,   // 02:30:33
                'total_time' => 23994,  // 06:39:54
            ],
            [
                'external_id' => 'suleymanov_celal',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2743,   // 00:45:43
                't1_time'    => 374,    // 06:14
                'bike_time'  => 9697,   // 02:41:37
                't2_time'    => 331,    // 05:31
                'run_time'   => 6111,   // 01:41:51
                'total_time' => 19254,  // 05:20:54
            ],
            [
                'external_id' => 'hajibayov_jalal',
                'race_date' => '2021-10-31',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2551,   // 00:42:31
                't1_time'    => 431,    // 07:11
                'bike_time'  => 9570,   // 02:39:30
                't2_time'    => 318,    // 05:18
                'run_time'   => 6463,   // 01:47:43
                'total_time' => 19331,  // 05:22:11
            ],
            [
                'external_id' => 'karimov_firdovsi',
                'race_date' => '2021-10-03',
                'location' => 'Barcelona',
                'race_type' => 'ironman',
                'swim_time'  => 880,    // 00:14:40
                't1_time'    => 668,    // 11:08
                'bike_time'  => 24095,  // 06:41:35
                't2_time'    => 727,    // 12:07
                'run_time'   => 24243,  // 06:44:03
                'total_time' => 50613,  // 14:03:33
            ],
            [
                'external_id' => 'karimov_firdovsi',
                'race_date' => '2021-08-07',
                'location' => 'Tallinn',
                'race_type' => 'ironman',
                'swim_time'  => 5112,   // 01:25:12
                't1_time'    => 712,    // 11:52
                'bike_time'  => 24989,  // 06:56:29
                't2_time'    => 671,    // 11:11
                'run_time'   => 23402,  // 06:30:02
                'total_time' => 54887,  // 15:14:47
            ],
            [
                'external_id' => 'karimov_firdovsi',
                'race_date' => '2019-08-18',
                'location' => 'Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 4575,   // 01:16:15
                't1_time'    => 728,    // 12:08
                'bike_time'  => 20847,  // 05:47:27
                't2_time'    => 522,    // 08:42
                'run_time'   => 16139,  // 04:28:59
                'total_time' => 42812,  // 11:53:32
            ],
            [
                'external_id' => 'karimov_firdovsi',
                'race_date' => '2018-06-17',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2403,   // 00:40:03
                't1_time'    => 483,    // 08:03
                'bike_time'  => 10845,  // 03:00:45
                't2_time'    => 301,    // 05:01
                'run_time'   => 7910,   // 02:11:50
                'total_time' => 21942,  // 06:05:42
            ],
            [
                'external_id' => 'karimov_firdovsi',
                'race_date' => '2017-01-27',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2744,   // 00:45:44
                't1_time'    => 675,    // 11:15
                'bike_time'  => 11005,  // 03:03:25
                't2_time'    => 430,    // 07:10
                'run_time'   => 9127,   // 02:32:07
                'total_time' => 23981,  // 06:39:41
            ],
            [
                'external_id' => 'karimov_firdovsi',
                'race_date' => '2016-08-28',
                'location' => 'Vichy',
                'race_type' => 'ironman',
                'swim_time'  => 6019,   // 01:40:19
                't1_time'    => 740,    // 12:20
                'bike_time'  => 23569,  // 06:32:49
                't2_time'    => 947,    // 15:47
                'run_time'   => 21113,  // 05:51:53
                'total_time' => 52388,  // 14:33:08
            ],
            [
                'external_id' => 'karimov_firdovsi',
                'race_date' => '2015-08-22',
                'location' => 'Budapest',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2641,   // 00:44:01
                't1_time'    => 509,    // 08:29
                'bike_time'  => 11034,  // 03:03:54
                't2_time'    => 413,    // 06:53
                'run_time'   => 7933,   // 02:12:13
                'total_time' => 22530,  // 06:15:30
            ],
            [
                'external_id' => 'garashova_gulshan',
                'race_date' => '2021-09-19',
                'location' => 'Emilia-Romagna',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3276,   // 00:54:36
                't1_time'    => 587,    // 09:47
                'bike_time'  => 11141,  // 03:05:41
                't2_time'    => 519,    // 08:39
                'run_time'   => 8484,   // 02:21:24
                'total_time' => 24007,  // 06:40:07
            ],
            [
                'external_id' => 'mammadli_togrul',
                'race_date' => '2021-08-08',
                'location' => 'Tallinn',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 1926,   // 00:32:06
                't1_time'    => 233,    // 03:53
                'bike_time'  => 9405,   // 02:36:45
                't2_time'    => 220,    // 03:40
                'run_time'   => 7522,   // 02:05:22
                'total_time' => 19308,  // 05:21:48
            ],
            [
                'external_id' => 'alakbarov_tamerlan',
                'race_date' => '2021-08-08',
                'location' => 'Tallinn',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 1821,   // 00:30:21
                't1_time'    => 363,    // 06:03
                'bike_time'  => 10188,  // 02:49:48
                't2_time'    => 148,    // 02:28
                'run_time'   => 7489,   // 02:04:49
                'total_time' => 20011,  // 05:33:31
            ],
            [
                'external_id' => 'mirtagavi_azar',
                'race_date' => '2021-08-08',
                'location' => 'Tallinn',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2502,   // 00:41:42
                't1_time'    => 471,    // 07:51
                'bike_time'  => 11498,  // 03:11:38
                't2_time'    => 355,    // 05:55
                'run_time'   => 8599,   // 02:23:19
                'total_time' => 23426,  // 06:30:26
            ],
            [
                'external_id' => 'salahov_vuqar',
                'race_date' => '2021-08-07',
                'location' => 'Tallinn',
                'race_type' => 'ironman',
                'swim_time'  => 6292,   // 01:44:52
                't1_time'    => 587,    // 09:47
                'bike_time'  => 20048,  // 05:34:08
                't2_time'    => 459,    // 07:39
                'run_time'   => 17539,  // 04:52:19
                'total_time' => 44927,  // 12:28:47
            ],
            [
                'external_id' => 'safarov_sabuhi',
                'race_date' => '2021-08-07',
                'location' => 'Tallinn',
                'race_type' => 'ironman',
                'swim_time'  => 6045,   // 01:40:45
                't1_time'    => 442,    // 07:22
                'bike_time'  => 21645,  // 06:00:45
                't2_time'    => 317,    // 05:17
                'run_time'   => 14666,  // 04:04:26
                'total_time' => 43116,  // 11:58:36
            ],
            [
                'external_id' => 'safarov_sabuhi',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2638,   // 00:43:58
                't1_time'    => 213,    // 03:33
                'bike_time'  => 9166,   // 02:32:46
                't2_time'    => 173,    // 02:53
                'run_time'   => 7072,   // 01:57:52
                'total_time' => 19261,  // 05:21:01
            ],
            [
                'external_id' => 'safarov_sabuhi',
                'race_date' => '2018-10-28',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2618,   // 00:43:38
                't1_time'    => 386,    // 06:26
                'bike_time'  => 9254,   // 02:34:14
                't2_time'    => 201,    // 03:21
                'run_time'   => 7130,   // 01:58:50
                'total_time' => 19589,  // 05:26:29
            ],
            [
                'external_id' => 'mamedov_ilgam',
                'race_date' => '2021-08-01',
                'location' => 'St. Petersburg',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2358,   // 00:39:18
                't1_time'    => 528,    // 08:48
                'bike_time'  => 9538,   // 02:38:58
                't2_time'    => 353,    // 05:53
                'run_time'   => 7104,   // 01:58:24
                'total_time' => 19881,  // 05:31:21
            ],
            [
                'external_id' => 'mamedov_ilgam',
                'race_date' => '2019-06-29',
                'location' => 'Finland',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2331,   // 00:38:51
                't1_time'    => 412,    // 06:52
                'bike_time'  => 9621,   // 02:40:21
                't2_time'    => 381,    // 06:21
                'run_time'   => 6769,   // 01:52:49
                'total_time' => 19500,  // 05:25:00
            ],
            [
                'external_id' => 'mamedov_ilgam',
                'race_date' => '2018-08-04',
                'location' => 'Tallinn',
                'race_type' => 'ironman',
                'swim_time'  => 4642,   // 01:17:22
                't1_time'    => 405,    // 06:45
                'bike_time'  => 19544,  // 05:25:44
                't2_time'    => 492,    // 08:12
                'run_time'   => 17413,  // 04:50:13
                'total_time' => 42496,  // 11:48:16
            ],
            [
                'external_id' => 'gasim_farid',
                'race_date' => '2020-02-21',
                'location' => 'Oman',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2162,   // 00:36:02
                't1_time'    => 427,    // 07:07
                'bike_time'  => 10039,  // 02:47:19
                't2_time'    => 264,    // 04:24
                'run_time'   => 8937,   // 02:28:57
                'total_time' => 21829,  // 06:03:49
            ],
            [
                'external_id' => 'gasim_farid',
                'race_date' => '2019-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2277,   // 00:37:57
                't1_time'    => 491,    // 08:11
                'bike_time'  => 9609,   // 02:40:09
                't2_time'    => 194,    // 03:14
                'run_time'   => 5840,   // 01:37:20 ?? (5:43:40 seems off for run, total matches bike+swim+t1+t2+run?)
                'total_time' => 20620,  // 05:43:40
            ],
            [
                'external_id' => 'gasim_farid',
                'race_date' => '2019-02-01',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2583,   // 00:43:03
                't1_time'    => 727,    // 12:07
                'bike_time'  => 10765,  // 02:59:25
                't2_time'    => 553,    // 09:13
                'run_time'   => 11375,  // 03:09:35
                'total_time' => 26003,  // 07:13:23
            ],
                                                                                    
            [
                'external_id' => 'najafova_diana',
                'race_date' => '2019-12-07',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2836,   // 00:47:16
                't1_time'    => 251,    // 04:11
                'bike_time'  => 10778,  // 02:59:38
                't2_time'    => 386,    // 06:26
                'run_time'   => 8010,   // 02:13:30
                'total_time' => 22261,  // 06:11:01
            ],
            [
                'external_id' => 'najafova_diana',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3252,   // 00:54:12
                't1_time'    => 214,    // 03:34
                'bike_time'  => 12265,  // 03:24:25
                't2_time'    => 314,    // 05:14
                'run_time'   => 8709,   // 02:25:09
                'total_time' => 24754,  // 06:52:34
            ],
            [
                'external_id' => 'najafova_diana',
                'race_date' => '2019-03-11',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3025,   // 00:50:25
                't1_time'    => 361,    // 06:01
                'bike_time'  => 12281,  // 03:24:41
                't2_time'    => 295,    // 04:55
                'run_time'   => 8452,   // 02:20:52
                'total_time' => 24414,  // 06:46:54
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2019-12-07',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 1998,   // 00:33:18
                't1_time'    => 183,    // 03:03
                'bike_time'  => 8511,   // 02:21:51
                't2_time'    => 223,    // 03:43
                'run_time'   => 7051,   // 01:57:31
                'total_time' => 17965,  // 04:59:25
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2019-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2013,   // 00:33:33
                't1_time'    => 259,    // 04:19
                'bike_time'  => 9252,   // 02:34:12
                't2_time'    => 220,    // 03:40
                'run_time'   => 7607,   // 02:06:47
                'total_time' => 19351,  // 05:22:31
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2181,   // 00:36:21
                't1_time'    => 146,    // 02:26
                'bike_time'  => 9813,   // 02:43:33
                't2_time'    => 193,    // 03:13
                'run_time'   => 7738,   // 02:08:58
                'total_time' => 20070,  // 05:34:30
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2018-12-08',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2017,   // 00:33:37
                't1_time'    => 169,    // 02:49
                'bike_time'  => 9649,   // 02:40:49
                't2_time'    => 239,    // 03:59
                'run_time'   => 7695,   // 02:08:15
                'total_time' => 19769,  // 05:29:29
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2018-10-28',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2147,   // 00:35:47
                't1_time'    => 232,    // 03:52
                'bike_time'  => 9377,   // 02:36:17
                't2_time'    => 221,    // 03:41
                'run_time'   => 6900,   // 01:55:00
                'total_time' => 18877,  // 05:14:37
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2018-02-02',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2082,   // 00:34:42
                't1_time'    => 280,    // 04:40
                'bike_time'  => 9852,   // 02:44:12
                't2_time'    => 338,    // 05:38
                'run_time'   => 7519,   // 02:05:19
                'total_time' => 20071,  // 05:34:31
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2017-11-25',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 1992,   // 00:33:12
                't1_time'    => 202,    // 03:22
                'bike_time'  => 10266,  // 02:51:06
                't2_time'    => 197,    // 03:17
                'run_time'   => 7898,   // 02:11:38
                'total_time' => 20555,  // 05:42:35
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2017-10-15',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2322,   // 00:38:42
                't1_time'    => 312,    // 05:12
                'bike_time'  => 10093,  // 02:48:13
                't2_time'    => 226,    // 03:46
                'run_time'   => 7620,   // 02:07:00
                'total_time' => 20573,  // 05:42:53
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2016-12-10',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2043,   // 00:34:03
                't1_time'    => 394,    // 06:34
                'bike_time'  => 10781,  // 02:59:41
                't2_time'    => 260,    // 04:20
                'run_time'   => 7849,   // 02:10:49
                'total_time' => 21327,  // 05:55:27
            ],
            [
                'external_id' => 'najafov_boris',
                'race_date' => '2015-12-05',
                'location' => 'Bahrain',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 94,     // 00:01:34
                't1_time'    => 28,     // 00:28
                'bike_time'  => 151,    // 00:02:31
                't2_time'    => 1154,   // 19:14 (probably placeholder)
                'run_time'   => 0,      // 00:00
                'total_time' => 1947,   // 05:23:47 (adjusted placeholder)
            ],
            [
                'external_id' => 'karamov_murad',
                'race_date' => '2019-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2210,   // 00:36:50
                't1_time'    => 0,      // 00:00 (not provided)
                'bike_time'  => 9155,   // 02:32:35
                't2_time'    => 0,      // 00:00 (not provided)
                'run_time'   => 7274,   // 02:01:14
                'total_time' => 19427,  // 05:23:47
            ],
            [
                'external_id' => 'karamov_murad',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2478,   // 00:41:18
                't1_time'    => 371,    // 06:11
                'bike_time'  => 10364,  // 02:52:44
                't2_time'    => 348,    // 05:48
                'run_time'   => 7298,   // 02:01:38
                'total_time' => 20860,  // 05:47:40
            ],
            [
                'external_id' => 'gurbanov_isgandar',
                'race_date' => '2019-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2423,   // 00:40:23
                't1_time'    => 0,      // 00:00 (not provided)
                'bike_time'  => 10534,  // 02:55:34
                't2_time'    => 0,      // 00:00 (not provided)
                'run_time'   => 10851,  // 03:00:51
                'total_time' => 24858,  // 06:54:18
            ],
            [
                'external_id' => 'gurbanov_isgandar',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2895,   // 00:48:15
                't1_time'    => 466,    // 07:46
                'bike_time'  => 11467,  // 03:11:07
                't2_time'    => 527,    // 08:47
                'run_time'   => 11920,  // 03:18:40
                'total_time' => 27276,  // 07:34:36
            ],
            [
                'external_id' => 'abdullayev_jamil',
                'race_date' => '2019-11-03',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2426,   // 00:40:26
                't1_time'    => 379,    // 06:19
                'bike_time'  => 9020,   // 02:30:20
                't2_time'    => 301,    // 05:01
                'run_time'   => 8528,   // 02:22:08
                'total_time' => 20655,  // 05:44:15
            ],
            [
                'external_id' => 'abdullayev_jamil',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2584,   // 00:43:04
                't1_time'    => 259,    // 04:19
                'bike_time'  => 9787,   // 02:43:07
                't2_time'    => 282,    // 04:42
                'run_time'   => 9007,   // 02:30:07
                'total_time' => 21919,  // 06:05:19
            ],
            [
                'external_id' => 'nuriyev_ziya',
                'race_date' => '2019-10-06',
                'location' => 'Barcelona',
                'race_type' => 'ironman',
                'swim_time'  => 4829,   // 01:20:29
                't1_time'    => 944,    // 15:44
                'bike_time'  => 24060,  // 06:41:00
                't2_time'    => 729,    // 12:09
                'run_time'   => 21380,  // 05:56:20
                'total_time' => 51940,  // 14:25:40
            ],
            [
                'external_id' => 'novruzov_farrukh',
                'race_date' => '2019-10-06',
                'location' => 'Barcelona',
                'race_type' => 'ironman',
                'swim_time'  => 5587,   // 01:33:07
                't1_time'    => 651,    // 10:51
                'bike_time'  => 26364,  // 07:19:24
                't2_time'    => 553,    // 09:13
                'run_time'   => 20205,  // 05:36:45
                'total_time' => 53358,  // 14:49:18
            ],
            [
                'external_id' => 'bayramov_yahya',
                'race_date' => '2019-08-18',
                'location' => 'Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 6868,   // 01:54:28
                't1_time'    => 559,    // 09:19
                'bike_time'  => 23283,  // 06:28:03
                't2_time'    => 447,    // 07:27
                'run_time'   => 17223,  // 04:47:03
                'total_time' => 48372,  // 13:26:12
            ],
            [
                'external_id' => 'bayramov_yahya',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3172,   // 00:52:52
                't1_time'    => 325,    // 05:25
                'bike_time'  => 10125,  // 02:48:45
                't2_time'    => 229,    // 03:49
                'run_time'   => 6954,   // 01:55:54
                'total_time' => 20805,  // 05:46:45
            ],
            [
                'external_id' => 'rotkin_timur',
                'race_date' => '2019-08-11',
                'location' => 'Gdynia',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2590,   // 00:43:10
                't1_time'    => 269,    // 04:29
                'bike_time'  => 12663,  // 03:31:03
                't2_time'    => 382,    // 06:22
                'run_time'   => 7988,   // 02:13:08
                'total_time' => 23892,  // 06:38:12
            ],
            [
                'external_id' => 'ziyadov_gadzhiverdi',
                'race_date' => '2019-07-14',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2321,   // 00:38:41
                't1_time'    => 306,    // 05:06
                'bike_time'  => 12254,  // 03:24:14
                't2_time'    => 307,    // 05:07
                'run_time'   => 12615,  // 03:30:15
                'total_time' => 27802,  // 07:43:22
            ],
            [
                'external_id' => 'safarov_javid',
                'race_date' => '2019-06-23',
                'location' => 'Elsinore',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2446,   // 00:40:46
                't1_time'    => 429,    // 07:09
                'bike_time'  => 10391,  // 02:53:11
                't2_time'    => 208,    // 03:28
                'run_time'   => 7422,   // 02:03:42
                'total_time' => 20896,  // 05:48:16
            ],
            [
                'external_id' => 'gadzhibekov_dzhalal',
                'race_date' => '2019-02-19',
                'location' => 'Colombo',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2744,   // 00:45:44
                't1_time'    => 334,    // 05:34
                'bike_time'  => 12770,  // 03:32:50
                't2_time'    => 248,    // 04:08
                'run_time'   => 10095,  // 02:48:15
                'total_time' => 26191,  // 07:16:31
            ],
            [
                'external_id' => 'demirchi_gafar',
                'race_date' => '2016-07-30',
                'location' => 'Budapest',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2996,   // 00:49:56
                't1_time'    => 422,    // 07:02
                'bike_time'  => 11585,  // 03:13:05
                't2_time'    => 532,    // 08:52
                'run_time'   => 7764,   // 02:09:24
                'total_time' => 23299,  // 06:28:19
            ],
            [
                'external_id' => 'demirchi_gafar',
                'race_date' => '2017-05-14',
                'location' => 'Pays D`Aix',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3316,   // 00:55:16
                't1_time'    => 885,    // 14:45
                'bike_time'  => 12780,  // 03:33:00
                't2_time'    => 534,    // 08:54
                'run_time'   => 7888,   // 02:11:28
                'total_time' => 25403,  // 07:03:23
            ],
            [
                'external_id' => 'demirchi_gafar',
                'race_date' => '2017-06-11',
                'location' => 'Kraichgau',
                'race_type' => '5150',
                'swim_time'  => 0,      // 00:00:00
                't1_time'    => 0,      // 00:00
                'bike_time'  => 0,      // 00:00:00
                't2_time'    => 0,      // 00:00
                'run_time'   => 0,      // 00:00:00
                'total_time' => 12514,  // 03:28:34
            ],
            [
                'external_id' => 'demirchi_gafar',
                'race_date' => '2017-10-15',
                'location' => 'Turkey',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3470,   // 00:57:50
                't1_time'    => 669,    // 11:09
                'bike_time'  => 10477,  // 02:54:37
                't2_time'    => 336,    // 05:36
                'run_time'   => 9168,   // 02:32:48
                'total_time' => 24120,  // 06:42:00
            ],
            [
                'external_id' => 'demirchi_gafar',
                'race_date' => '2018-08-19',
                'location' => 'Copenhagen',
                'race_type' => 'ironman',
                'swim_time'  => 5665,   // 01:34:25
                't1_time'    => 925,    // 15:25
                'bike_time'  => 22006,  // 06:06:46
                't2_time'    => 447,    // 07:27
                'run_time'   => 15261,  // 04:14:21
                'total_time' => 44315,  // 12:18:35
            ],
            [
                'external_id' => 'garajayev_hamid',
                'race_date' => '2018-06-17',
                'location' => 'Astana',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2478,   // 00:41:18
                't1_time'    => 493,    // 08:13
                'bike_time'  => 10549,  // 02:55:49
                't2_time'    => 239,    // 03:59
                'run_time'   => 8296,   // 02:18:16
                'total_time' => 22055,  // 06:07:35
            ],
            [
                'external_id' => 'melikzade_fuad',
                'race_date' => '2017-09-10',
                'location' => 'Santa Cruz',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 1182,   // 00:19:42
                't1_time'    => 578,    // 09:38
                'bike_time'  => 11791,  // 03:16:31
                't2_time'    => 459,    // 07:39
                'run_time'   => 10022,  // 02:47:02
                'total_time' => 24032,  // 06:40:32
            ],
            [
                'external_id' => 'aliyeva_fatima',
                'race_date' => '2017-08-27',
                'location' => 'Zell am See-Kaprun',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2046,   // 00:34:06
                't1_time'    => 375,    // 06:15
                'bike_time'  => 11638,  // 03:13:58
                't2_time'    => 377,    // 06:17
                'run_time'   => 8209,   // 02:16:49
                'total_time' => 22645,  // 06:17:25
            ],
            [
                'external_id' => 'aliyeva_fatima',
                'race_date' => '2016-07-08',
                'location' => 'Gdynia',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 3359,   // 00:55:59
                't1_time'    => 292,    // 04:52
                'bike_time'  => 12694,  // 03:31:34
                't2_time'    => 213,    // 03:33
                'run_time'   => 7130,   // 01:58:50
                'total_time' => 23688,  // 06:34:48
            ],
            [
                'external_id' => 'aliyeva_fatima',
                'race_date' => '2016-05-06',
                'location' => 'Kraichgau',
                'race_type' => '5150',
                'swim_time'  => 0,      // 00:00:00
                't1_time'    => 157,    // 02:37
                'bike_time'  => 6004,   // 01:40:04
                't2_time'    => 172,    // 02:52
                'run_time'   => 3016,   // 00:50:16
                'total_time' => 9300,   // 02:35:00
            ],
            [
                'external_id' => 'hasanov_rufat',
                'race_date' => '2017-07-02',
                'location' => 'Austria',
                'race_type' => 'ironman',
                'swim_time'  => 5599,   // 01:33:19
                't1_time'    => 731,    // 12:11
                'bike_time'  => 27936,  // 07:45:36
                't2_time'    => 500,    // 08:20
                'run_time'   => 19978,  // 05:32:58
                'total_time' => 54744,  // 15:12:24
            ],
            [
                'external_id' => 'hasanov_rufat',
                'race_date' => '2017-01-27',
                'location' => 'Dubai',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2993,   // 00:49:53
                't1_time'    => 551,    // 09:11
                'bike_time'  => 12458,  // 03:27:38
                't2_time'    => 459,    // 07:39
                'run_time'   => 9585,   // 02:39:45
                'total_time' => 26046,  // 07:14:06
            ],
            [
                'external_id' => 'hasanov_rufat',
                'race_date' => '2016-07-31',
                'location' => 'Budapest',
                'race_type' => '5150',
                'swim_time'  => 0,      // 00:00:00
                't1_time'    => 0,      // 00:00
                'bike_time'  => 0,      // 00:00:00
                't2_time'    => 0,      // 00:00
                'run_time'   => 0,      // 00:00:00
                'total_time' => 12663,  // 03:31:03
            ],
            [
                'external_id' => 'soltanov_rinat',
                'race_date' => '2015-08-22',
                'location' => 'Budapest',
                'race_type' => 'ironman_70_3',
                'swim_time'  => 2958,   // 00:49:18
                't1_time'    => 384,    // 06:24
                'bike_time'  => 11507,  // 03:11:47
                't2_time'    => 571,    // 09:31
                'run_time'   => 7110,   // 01:58:30
                'total_time' => 22530,  // 06:15:30
            ],
                                                                                             
        
        ];

        $synced = 0;
        $skipped = 0;

        foreach ($results as $result) {
            $externalId = $result['external_id'];
            $profile = UserProfile::where('external_id', $externalId)->first();

            if (! $profile) {
                $this->command->warn("Профиль не найден: {$externalId}");
                $skipped++;

                continue;
            }

            RaceResult::updateOrCreate(
                [
                    'user_profile_id' => $profile->id,
                    'race_date' => $result['race_date'],
                    'location' => $result['location'],
                ],
                [
                    'race_type' => $result['race_type'],
                    'swim_time' => $result['swim_time'],
                    't1_time' => $result['t1_time'],
                    'bike_time' => $result['bike_time'],
                    't2_time' => $result['t2_time'],
                    'run_time' => $result['run_time'],
                    'total_time' => $result['total_time'],
                    'age_group' => $result['age_group'] ?? null,
                    'overall_position' => $result['overall_position'] ?? null,
                    'age_group_position' => $result['age_group_position'] ?? null,
                    'is_approved' => true,
                    'approved_at' => now(),
                ]
            );

            $synced++;
        }

        $this->command->info("Результаты: синхронизировано {$synced}, пропущено {$skipped}");
    }

    /**
     * Хелпер для конвертации времени HH:MM:SS в секунды.
     */
    public static function timeToSeconds(string $time): int
    {
        $parts = explode(':', $time);

        if (count($parts) === 3) {
            return (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
        }

        if (count($parts) === 2) {
            return (int) $parts[0] * 60 + (int) $parts[1];
        }

        return (int) $time;
    }
}
