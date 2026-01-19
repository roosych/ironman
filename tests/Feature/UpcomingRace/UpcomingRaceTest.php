<?php

declare(strict_types=1);

namespace Tests\Feature\UpcomingRace;

use App\Models\UpcomingRace;
use App\Models\User;
use App\Models\UserPhoto;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpcomingRaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_upcoming_races(): void
    {
        $profile = UserProfile::factory()->athlete()->create();
        UpcomingRace::factory()->count(3)->create([
            'user_profile_id' => $profile->id,
        ]);

        $response = $this->getJson('/api/v1/upcoming-races');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'race_type',
                        'race_type_label',
                        'location',
                        'race_date',
                        'is_active',
                        'created_by' => [
                            'id',
                            'name',
                            'avatar',
                        ],
                    ],
                ],
            ]);
    }

    public function test_upcoming_races_excludes_past_races(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        UpcomingRace::factory()->create([
            'user_profile_id' => $profile->id,
            'race_date' => now()->addMonth(),
        ]);
        UpcomingRace::factory()->past()->create([
            'user_profile_id' => $profile->id,
        ]);

        $response = $this->getJson('/api/v1/upcoming-races');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_only_future_parameter_filters_future_races(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        UpcomingRace::factory()->create([
            'user_profile_id' => $profile->id,
            'race_date' => now()->addMonth(),
        ]);
        UpcomingRace::factory()->past()->create([
            'user_profile_id' => $profile->id,
        ]);

        // Default behavior (only_future=true or not specified) - only future races
        $response = $this->getJson('/api/v1/upcoming-races?only_future=true');
        $response->assertOk()->assertJsonCount(1, 'data');

        // only_future=false - all races (future and past)
        $response = $this->getJson('/api/v1/upcoming-races?only_future=false');
        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_upcoming_races_excludes_inactive(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        UpcomingRace::factory()->create([
            'user_profile_id' => $profile->id,
        ]);
        UpcomingRace::factory()->inactive()->create([
            'user_profile_id' => $profile->id,
        ]);

        $response = $this->getJson('/api/v1/upcoming-races');

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_can_filter_by_user_profile_id(): void
    {
        $profile1 = UserProfile::factory()->athlete()->create();
        $profile2 = UserProfile::factory()->athlete()->create();

        UpcomingRace::factory()->count(2)->create(['user_profile_id' => $profile1->id]);
        UpcomingRace::factory()->count(3)->create(['user_profile_id' => $profile2->id]);

        $response = $this->getJson("/api/v1/upcoming-races?user_profile_id={$profile1->id}");

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_filter_by_race_type(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        UpcomingRace::factory()->ironman()->count(2)->create(['user_profile_id' => $profile->id]);
        UpcomingRace::factory()->ironman703()->create(['user_profile_id' => $profile->id]);

        $response = $this->getJson('/api/v1/upcoming-races?race_type=ironman');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_upcoming_races_ordered_by_date(): void
    {
        $profile = UserProfile::factory()->athlete()->create();

        UpcomingRace::factory()->create([
            'user_profile_id' => $profile->id,
            'race_date' => now()->addMonths(3),
        ]);
        UpcomingRace::factory()->create([
            'user_profile_id' => $profile->id,
            'race_date' => now()->addMonth(),
        ]);
        UpcomingRace::factory()->create([
            'user_profile_id' => $profile->id,
            'race_date' => now()->addMonths(2),
        ]);

        $response = $this->getJson('/api/v1/upcoming-races');

        $response->assertOk();
        $dates = collect($response->json('data'))->pluck('race_date')->toArray();

        $this->assertEquals($dates, collect($dates)->sort()->values()->toArray());
    }

    public function test_includes_creator_avatar_when_exists(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        UserPhoto::factory()->avatar()->create(['user_id' => $user->id]);

        UpcomingRace::factory()->create(['user_profile_id' => $profile->id]);

        $response = $this->getJson('/api/v1/upcoming-races');

        $response->assertOk();
        $this->assertNotNull($response->json('data.0.created_by.avatar'));
    }

    public function test_authenticated_user_can_create_upcoming_race(): void
    {
        $user = User::factory()->create();
        $profile = UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'race_type' => 'ironman',
            'location' => 'Hamburg, Germany',
            'race_date' => now()->addMonths(6)->toDateString(),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/upcoming-races', $data);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.race_type', 'ironman')
            ->assertJsonPath('data.location', 'Hamburg, Germany')
            ->assertJsonPath('data.created_by.id', $profile->id);

        $this->assertDatabaseHas('upcoming_races', [
            'user_profile_id' => $profile->id,
            'race_type' => 'ironman',
            'location' => 'Hamburg, Germany',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_upcoming_race(): void
    {
        $data = [
            'race_type' => 'ironman',
            'location' => 'Hamburg, Germany',
            'race_date' => now()->addMonths(6)->toDateString(),
        ];

        $response = $this->postJson('/api/v1/upcoming-races', $data);

        $response->assertStatus(401);
    }

    public function test_user_without_profile_cannot_create_upcoming_race(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $data = [
            'race_type' => 'ironman',
            'location' => 'Hamburg, Germany',
            'race_date' => now()->addMonths(6)->toDateString(),
        ];

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/upcoming-races', $data);

        $response->assertStatus(403);
    }

    public function test_validation_requires_race_type(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/upcoming-races', [
                'location' => 'Hamburg',
                'race_date' => now()->addMonth()->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['race_type']);
    }

    public function test_validation_requires_location(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/upcoming-races', [
                'race_type' => 'ironman',
                'race_date' => now()->addMonth()->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['location']);
    }

    public function test_validation_requires_future_date(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/upcoming-races', [
                'race_type' => 'ironman',
                'location' => 'Hamburg',
                'race_date' => now()->subDay()->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['race_date']);
    }

    public function test_validation_rejects_invalid_race_type(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->athlete()->create(['user_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/upcoming-races', [
                'race_type' => 'invalid',
                'location' => 'Hamburg',
                'race_date' => now()->addMonth()->toDateString(),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['race_type']);
    }
}
