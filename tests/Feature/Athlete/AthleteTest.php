<?php

declare(strict_types=1);

namespace Tests\Feature\Athlete;

use App\Models\RaceResult;
use App\Models\User;
use App\Models\UserPhoto;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AthleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_athletes(): void
    {
        UserProfile::factory()->athlete()->count(3)->create();

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'avatar',
                        'race_counts' => [
                            'ironman',
                            'ironman_70_3',
                            '5150',
                        ],
                    ],
                ],
            ]);
    }

    public function test_only_athletes_are_returned(): void
    {
        UserProfile::factory()->athlete()->count(2)->create();
        UserProfile::factory()->coach()->create();
        UserProfile::factory()->admin()->create();

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_returns_avatar_url_when_exists(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        UserPhoto::factory()->avatar()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk();
        $data = $response->json('data.0');

        $this->assertNotNull($data['avatar']);
        $this->assertStringContainsString('storage/', $data['avatar']);
    }

    public function test_returns_null_avatar_when_not_exists(): void
    {
        UserProfile::factory()->athlete()->create();

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk()
            ->assertJsonPath('data.0.avatar', null);
    }

    public function test_race_counts_are_calculated_correctly(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        RaceResult::factory()->ironman()->count(3)->create(['user_profile_id' => $profile->id]);
        RaceResult::factory()->ironman703()->count(5)->create(['user_profile_id' => $profile->id]);
        RaceResult::factory()->sprint5150()->count(1)->create(['user_profile_id' => $profile->id]);

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk()
            ->assertJsonPath('data.0.race_counts.ironman', 3)
            ->assertJsonPath('data.0.race_counts.ironman_70_3', 5)
            ->assertJsonPath('data.0.race_counts.5150', 1);
    }

    public function test_race_counts_default_to_zero(): void
    {
        UserProfile::factory()->athlete()->create();

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk()
            ->assertJsonPath('data.0.race_counts.ironman', 0)
            ->assertJsonPath('data.0.race_counts.ironman_70_3', 0)
            ->assertJsonPath('data.0.race_counts.5150', 0);
    }

    public function test_uses_user_name_when_linked(): void
    {
        $user = User::factory()->create(['name' => 'John Doe']);
        UserProfile::factory()->athlete()->create([
            'user_id' => $user->id,
            'admin_full_name' => 'Admin Name',
        ]);

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'John Doe');
    }

    public function test_uses_admin_full_name_when_no_linked_user(): void
    {
        UserProfile::factory()->athlete()->withoutUser()->create([
            'admin_full_name' => 'Orphan Athlete',
        ]);

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Orphan Athlete');
    }

    public function test_endpoint_is_public(): void
    {
        UserProfile::factory()->athlete()->create();

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk();
    }

    public function test_endpoint_is_throttled(): void
    {
        UserProfile::factory()->athlete()->create();

        // Make 61 requests (limit is 60 per minute)
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/v1/athletes');
        }

        $response = $this->getJson('/api/v1/athletes');

        $response->assertStatus(429);
    }

    public function test_can_get_single_athlete(): void
    {
        $profile = UserProfile::factory()->athlete()->create([
            'ironman_number' => 12345,
            'bio' => 'Test bio',
            'social_links' => ['instagram' => '@test'],
        ]);

        $response = $this->getJson("/api/v1/athletes/{$profile->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $profile->id)
            ->assertJsonPath('data.ironman_number', 12345)
            ->assertJsonPath('data.bio', 'Test bio')
            ->assertJsonPath('data.social_links.instagram', '@test')
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'avatar',
                    'race_counts' => [
                        'ironman',
                        'ironman_70_3',
                        '5150',
                    ],
                    'ironman_number',
                    'bio',
                    'social_links',
                    'ranking',
                ],
            ]);
    }

    public function test_single_athlete_returns_404_when_not_found(): void
    {
        $response = $this->getJson('/api/v1/athletes/999');

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_single_athlete_returns_404_for_non_athlete_profile(): void
    {
        $coach = UserProfile::factory()->coach()->create();

        $response = $this->getJson("/api/v1/athletes/{$coach->id}");

        $response->assertStatus(404)
            ->assertJsonPath('success', false);
    }

    public function test_single_athlete_has_correct_race_counts(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        RaceResult::factory()->ironman()->count(2)->create(['user_profile_id' => $profile->id]);
        RaceResult::factory()->ironman703()->count(4)->create(['user_profile_id' => $profile->id]);

        $response = $this->getJson("/api/v1/athletes/{$profile->id}");

        $response->assertOk()
            ->assertJsonPath('data.race_counts.ironman', 2)
            ->assertJsonPath('data.race_counts.ironman_70_3', 4)
            ->assertJsonPath('data.race_counts.5150', 0);
    }

    public function test_single_athlete_returns_avatar_when_exists(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        UserPhoto::factory()->avatar()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/v1/athletes/{$profile->id}");

        $response->assertOk();
        $this->assertNotNull($response->json('data.avatar'));
    }

    public function test_list_does_not_include_detailed_fields(): void
    {
        UserProfile::factory()->athlete()->create([
            'ironman_number' => 12345,
            'bio' => 'Test bio',
        ]);

        $response = $this->getJson('/api/v1/athletes');

        $response->assertOk();
        $data = $response->json('data.0');

        $this->assertArrayNotHasKey('ironman_number', $data);
        $this->assertArrayNotHasKey('bio', $data);
        $this->assertArrayNotHasKey('social_links', $data);
        $this->assertArrayNotHasKey('ranking', $data);
    }

    public function test_ranking_is_calculated_correctly(): void
    {
        // Create 3 athletes with different best times for ironman
        $athlete1 = UserProfile::factory()->athlete()->create();
        $athlete2 = UserProfile::factory()->athlete()->create();
        $athlete3 = UserProfile::factory()->athlete()->create();

        // Athlete 1: best time 36000 (10 hours)
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete1->id,
            'total_time' => 36000,
        ]);

        // Athlete 2: best time 32000 (best)
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete2->id,
            'total_time' => 32000,
        ]);

        // Athlete 3: best time 34000 (middle)
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete3->id,
            'total_time' => 34000,
        ]);

        // Athlete 2 should be #1 of 3
        $response = $this->getJson("/api/v1/athletes/{$athlete2->id}");
        $response->assertJsonPath('data.ranking.ironman.position', 1)
            ->assertJsonPath('data.ranking.ironman.total', 3);

        // Athlete 3 should be #2 of 3
        $response = $this->getJson("/api/v1/athletes/{$athlete3->id}");
        $response->assertJsonPath('data.ranking.ironman.position', 2)
            ->assertJsonPath('data.ranking.ironman.total', 3);

        // Athlete 1 should be #3 of 3
        $response = $this->getJson("/api/v1/athletes/{$athlete1->id}");
        $response->assertJsonPath('data.ranking.ironman.position', 3)
            ->assertJsonPath('data.ranking.ironman.total', 3);
    }

    public function test_ranking_is_null_when_no_results(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        $response = $this->getJson("/api/v1/athletes/{$profile->id}");

        $response->assertOk()
            ->assertJsonPath('data.ranking.ironman', null)
            ->assertJsonPath('data.ranking.ironman_70_3', null);
    }

    public function test_ranking_excludes_incomplete_results(): void
    {
        $athlete1 = UserProfile::factory()->athlete()->create();
        $athlete2 = UserProfile::factory()->athlete()->create();

        // Athlete 1: complete result
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete1->id,
            'total_time' => 36000,
            'swim_time' => 4000,
            't1_time' => 300,
            'bike_time' => 18000,
            't2_time' => 200,
            'run_time' => 13500,
        ]);

        // Athlete 2: incomplete result (swim_time = 0)
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete2->id,
            'total_time' => 32000,
            'swim_time' => 0,
            't1_time' => 300,
            'bike_time' => 18000,
            't2_time' => 200,
            'run_time' => 13500,
        ]);

        // Athlete 1 should be #1 of 1 (athlete 2 excluded)
        $response = $this->getJson("/api/v1/athletes/{$athlete1->id}");
        $response->assertJsonPath('data.ranking.ironman.position', 1)
            ->assertJsonPath('data.ranking.ironman.total', 1);

        // Athlete 2 should have no ranking (incomplete result)
        $response = $this->getJson("/api/v1/athletes/{$athlete2->id}");
        $response->assertJsonPath('data.ranking.ironman', null);
    }

    public function test_can_get_athlete_records(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        RaceResult::factory()->ironman()->approved()->create([
            'user_profile_id' => $profile->id,
            'swim_time' => 4000,
            't1_time' => 300,
            'bike_time' => 18000,
            't2_time' => 200,
            'run_time' => 13500,
            'total_time' => 36000,
            'race_date' => '2023-06-15',
            'location' => 'Hamburg',
        ]);

        $response = $this->getJson("/api/v1/athletes/{$profile->id}/records");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'ironman' => [
                        'swim' => ['time', 'seconds', 'race_date', 'location'],
                        't1',
                        'bike',
                        't2',
                        'run',
                        'total',
                    ],
                    'ironman_70_3',
                    '5150',
                ],
            ]);
    }

    public function test_records_returns_best_time_per_discipline(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        // First race - slower swim, faster bike
        RaceResult::factory()->ironman()->approved()->create([
            'user_profile_id' => $profile->id,
            'swim_time' => 5000,
            'bike_time' => 17000,
            'race_date' => '2023-01-01',
            'location' => 'Race 1',
        ]);

        // Second race - faster swim, slower bike
        RaceResult::factory()->ironman()->approved()->create([
            'user_profile_id' => $profile->id,
            'swim_time' => 4000,
            'bike_time' => 19000,
            'race_date' => '2023-06-01',
            'location' => 'Race 2',
        ]);

        $response = $this->getJson("/api/v1/athletes/{$profile->id}/records");

        $response->assertOk()
            ->assertJsonPath('data.ironman.swim.seconds', 4000)
            ->assertJsonPath('data.ironman.swim.location', 'Race 2')
            ->assertJsonPath('data.ironman.bike.seconds', 17000)
            ->assertJsonPath('data.ironman.bike.location', 'Race 1');
    }

    public function test_records_returns_null_for_missing_disciplines(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        // Only ironman results, no 70.3
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $profile->id,
        ]);

        $response = $this->getJson("/api/v1/athletes/{$profile->id}/records");

        $response->assertOk()
            ->assertJsonPath('data.ironman_70_3.swim', null)
            ->assertJsonPath('data.ironman_70_3.bike', null);
    }

    public function test_records_returns_404_for_non_athlete(): void
    {
        $coach = UserProfile::factory()->coach()->create();

        $response = $this->getJson("/api/v1/athletes/{$coach->id}/records");

        $response->assertStatus(404);
    }

    public function test_records_formats_time_correctly(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        RaceResult::factory()->ironman()->approved()->create([
            'user_profile_id' => $profile->id,
            'swim_time' => 3661, // 1:01:01
        ]);

        $response = $this->getJson("/api/v1/athletes/{$profile->id}/records");

        $response->assertOk()
            ->assertJsonPath('data.ironman.swim.time', '01:01:01')
            ->assertJsonPath('data.ironman.swim.seconds', 3661);
    }
}
