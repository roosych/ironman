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
}
