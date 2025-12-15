<?php

declare(strict_types=1);

namespace Tests\Feature\RaceResult;

use App\Models\RaceResult;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaceResultTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_all_race_results(): void
    {
        RaceResult::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/race-results');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'race_date',
                        'location',
                        'race_type',
                        'race_type_label',
                        'swim_time',
                        't1_time',
                        'bike_time',
                        't2_time',
                        'run_time',
                        'total_time',
                        'age_group',
                        'overall_position',
                        'age_group_position',
                    ],
                ],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_can_get_user_race_results(): void
    {
        $user = User::factory()->create();
        RaceResult::factory()->count(2)->create(['user_id' => $user->id]);
        RaceResult::factory()->count(3)->create(); // other users' results

        $response = $this->getJson("/api/v1/users/{$user->id}/race-results");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(2, 'data');
    }

    public function test_can_show_single_race_result(): void
    {
        $result = RaceResult::factory()->create();

        $response = $this->getJson("/api/v1/race-results/{$result->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.id', $result->id)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'user_id',
                    'race_date',
                    'location',
                    'race_type',
                    'swim_time',
                    'bike_time',
                    'run_time',
                    'total_time',
                ],
            ]);
    }

    public function test_authenticated_user_can_create_race_result(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'race_date' => '2024-10-13',
            'location' => 'Kona, Hawaii',
            'race_type' => 'ironman',
            'swim_time' => 4365,      // 1:12:45
            't1_time' => 330,         // 5:30
            'bike_time' => 18920,     // 5:15:20
            't2_time' => 195,         // 3:15
            'run_time' => 13530,      // 3:45:30
            'total_time' => 37340,    // 10:22:20
            'age_group' => 'M30-34',
            'overall_position' => 156,
            'age_group_position' => 12,
        ];

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/race-results', $data);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.location', 'Kona, Hawaii')
            ->assertJsonPath('data.race_type', 'ironman')
            ->assertJsonPath('data.swim_time', '01:12:45')
            ->assertJsonPath('data.total_time', '10:22:20');

        $this->assertDatabaseHas('race_results', [
            'user_id' => $user->id,
            'location' => 'Kona, Hawaii',
            'race_type' => 'ironman',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_race_result(): void
    {
        $data = [
            'race_date' => '2024-10-13',
            'location' => 'Kona, Hawaii',
            'race_type' => 'ironman',
            'swim_time' => 4365,
            't1_time' => 330,
            'bike_time' => 18920,
            't2_time' => 195,
            'run_time' => 13530,
            'total_time' => 37340,
        ];

        $response = $this->postJson('/api/v1/race-results', $data);

        $response->assertStatus(401);
    }

    public function test_user_can_update_own_race_result(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $result = RaceResult::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/v1/race-results/{$result->id}", [
                'location' => 'Updated Location',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.location', 'Updated Location');
    }

    public function test_user_cannot_update_others_race_result(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $result = RaceResult::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->putJson("/api/v1/race-results/{$result->id}", [
                'location' => 'Updated Location',
            ]);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_own_race_result(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $result = RaceResult::factory()->create(['user_id' => $user->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson("/api/v1/race-results/{$result->id}");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Результат удалён.');

        $this->assertDatabaseMissing('race_results', ['id' => $result->id]);
    }

    public function test_user_cannot_delete_others_race_result(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;
        $result = RaceResult::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson("/api/v1/race-results/{$result->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('race_results', ['id' => $result->id]);
    }

    public function test_validation_fails_with_invalid_race_type(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/race-results', [
                'race_date' => '2024-10-13',
                'location' => 'Test',
                'race_type' => 'invalid_type',
                'swim_time' => 4365,
                't1_time' => 330,
                'bike_time' => 18920,
                't2_time' => 195,
                'run_time' => 13530,
                'total_time' => 37340,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['race_type']);
    }

    public function test_validation_fails_without_required_fields(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/race-results', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'race_date',
                'location',
                'race_type',
                'swim_time',
                't1_time',
                'bike_time',
                't2_time',
                'run_time',
                'total_time',
            ]);
    }

    public function test_time_is_formatted_correctly_in_response(): void
    {
        $result = RaceResult::factory()->create([
            'swim_time' => 3661,     // 1:01:01
            't1_time' => 61,         // 0:01:01
            'bike_time' => 7322,     // 2:02:02
            't2_time' => 122,        // 0:02:02
            'run_time' => 10983,     // 3:03:03
            'total_time' => 22149,   // 6:09:09
        ]);

        $response = $this->getJson("/api/v1/race-results/{$result->id}");

        $response->assertOk()
            ->assertJsonPath('data.swim_time', '01:01:01')
            ->assertJsonPath('data.t1_time', '00:01:01')
            ->assertJsonPath('data.bike_time', '02:02:02')
            ->assertJsonPath('data.t2_time', '00:02:02')
            ->assertJsonPath('data.run_time', '03:03:03')
            ->assertJsonPath('data.total_time', '06:09:09');
    }

    public function test_race_results_are_ordered_by_date_descending(): void
    {
        $user = User::factory()->create();

        RaceResult::factory()->create([
            'user_id' => $user->id,
            'race_date' => '2023-01-01',
        ]);
        RaceResult::factory()->create([
            'user_id' => $user->id,
            'race_date' => '2024-06-15',
        ]);
        RaceResult::factory()->create([
            'user_id' => $user->id,
            'race_date' => '2022-05-20',
        ]);

        $response = $this->getJson("/api/v1/users/{$user->id}/race-results");

        $response->assertOk();
        $data = $response->json('data');

        $this->assertEquals('2024-06-15', $data[0]['race_date']);
        $this->assertEquals('2023-01-01', $data[1]['race_date']);
        $this->assertEquals('2022-05-20', $data[2]['race_date']);
    }
}
