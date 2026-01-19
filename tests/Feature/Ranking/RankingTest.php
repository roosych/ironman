<?php

declare(strict_types=1);

namespace Tests\Feature\Ranking;

use App\Models\RaceResult;
use App\Models\User;
use App\Models\UserPhoto;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_rankings_by_race_type_and_discipline(): void
    {
        $athlete = UserProfile::factory()->athlete()->create();
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete->id,
            'total_time' => 36000,
        ]);

        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=total');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('meta.race_type', 'ironman')
            ->assertJsonPath('meta.discipline', 'total')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'position',
                        'athlete_id',
                        'name',
                        'avatar',
                        'time',
                        'seconds',
                        'race_date',
                        'location',
                    ],
                ],
                'meta' => ['race_type', 'discipline', 'total'],
            ]);
    }

    public function test_rankings_are_ordered_by_best_time(): void
    {
        $athlete1 = UserProfile::factory()->athlete()->create();
        $athlete2 = UserProfile::factory()->athlete()->create();
        $athlete3 = UserProfile::factory()->athlete()->create();

        // Athlete 1: slowest
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete1->id,
            'total_time' => 40000,
        ]);

        // Athlete 2: fastest
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete2->id,
            'total_time' => 32000,
        ]);

        // Athlete 3: middle
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete3->id,
            'total_time' => 36000,
        ]);

        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=total');

        $response->assertOk();
        $data = $response->json('data');

        $this->assertEquals($athlete2->id, $data[0]['athlete_id']);
        $this->assertEquals(1, $data[0]['position']);

        $this->assertEquals($athlete3->id, $data[1]['athlete_id']);
        $this->assertEquals(2, $data[1]['position']);

        $this->assertEquals($athlete1->id, $data[2]['athlete_id']);
        $this->assertEquals(3, $data[2]['position']);
    }

    public function test_rankings_use_best_time_when_multiple_races(): void
    {
        $athlete = UserProfile::factory()->athlete()->create();

        // Two races, different times
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete->id,
            'total_time' => 40000,
        ]);
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete->id,
            'total_time' => 35000, // Best time
        ]);

        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=total');

        $response->assertOk()
            ->assertJsonPath('data.0.seconds', 35000);
    }

    public function test_rankings_filter_by_discipline(): void
    {
        $athlete1 = UserProfile::factory()->athlete()->create();
        $athlete2 = UserProfile::factory()->athlete()->create();

        // Athlete 1: fast swim, slow bike
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete1->id,
            'swim_time' => 3000,
            'bike_time' => 20000,
        ]);

        // Athlete 2: slow swim, fast bike
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete2->id,
            'swim_time' => 4000,
            'bike_time' => 17000,
        ]);

        // Swim ranking: athlete1 first
        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=swim');
        $response->assertJsonPath('data.0.athlete_id', $athlete1->id);

        // Bike ranking: athlete2 first
        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=bike');
        $response->assertJsonPath('data.0.athlete_id', $athlete2->id);
    }

    public function test_rankings_filter_by_race_type(): void
    {
        $athlete = UserProfile::factory()->athlete()->create();

        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete->id,
            'total_time' => 36000,
        ]);
        RaceResult::factory()->ironman703()->create([
            'user_profile_id' => $athlete->id,
            'total_time' => 18000,
        ]);

        // Ironman rankings
        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=total');
        $response->assertOk()
            ->assertJsonPath('data.0.seconds', 36000);

        // 70.3 rankings
        $response = $this->getJson('/api/v1/rankings?race_type=ironman_70_3&discipline=total');
        $response->assertOk()
            ->assertJsonPath('data.0.seconds', 18000);
    }

    public function test_rankings_returns_avatar_when_exists(): void
    {
        $user = User::factory()->create();
        $athlete = UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        UserPhoto::factory()->avatar()->create(['user_id' => $user->id]);

        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete->id,
        ]);

        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=total');

        $response->assertOk();
        $this->assertNotNull($response->json('data.0.avatar'));
    }

    public function test_rankings_excludes_zero_times(): void
    {
        $athlete1 = UserProfile::factory()->athlete()->create();
        $athlete2 = UserProfile::factory()->athlete()->create();

        // Athlete 1: valid time
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete1->id,
            'swim_time' => 4000,
        ]);

        // Athlete 2: zero swim time
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete2->id,
            'swim_time' => 0,
        ]);

        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=swim');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.athlete_id', $athlete1->id);
    }

    public function test_rankings_validation_requires_race_type(): void
    {
        $response = $this->getJson('/api/v1/rankings?discipline=total');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['race_type']);
    }

    public function test_rankings_validation_requires_discipline(): void
    {
        $response = $this->getJson('/api/v1/rankings?race_type=ironman');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['discipline']);
    }

    public function test_rankings_validation_rejects_invalid_race_type(): void
    {
        $response = $this->getJson('/api/v1/rankings?race_type=invalid&discipline=total');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['race_type']);
    }

    public function test_rankings_validation_rejects_invalid_discipline(): void
    {
        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=invalid');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['discipline']);
    }

    public function test_rankings_returns_empty_array_when_no_results(): void
    {
        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=total');

        $response->assertOk()
            ->assertJsonPath('data', [])
            ->assertJsonPath('meta.total', 0);
    }

    public function test_rankings_includes_race_details(): void
    {
        $athlete = UserProfile::factory()->athlete()->create();
        RaceResult::factory()->ironman()->create([
            'user_profile_id' => $athlete->id,
            'total_time' => 36000,
            'race_date' => '2023-06-15',
            'location' => 'Hamburg',
        ]);

        $response = $this->getJson('/api/v1/rankings?race_type=ironman&discipline=total');

        $response->assertOk()
            ->assertJsonPath('data.0.race_date', '2023-06-15')
            ->assertJsonPath('data.0.location', 'Hamburg');
    }
}
