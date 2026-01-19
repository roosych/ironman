<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\RaceResult;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaceResultApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_get_pending_race_results(): void
    {
        $admin = User::factory()->create();
        $adminProfile = UserProfile::factory()->create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $token = $admin->createToken('auth_token')->plainTextToken;

        RaceResult::factory()->count(3)->create(['is_approved' => false]);
        RaceResult::factory()->approved()->count(2)->create(); // Should not appear

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/race-results/pending');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'id',
                        'is_approved',
                        'race_date',
                        'location',
                        'race_type',
                    ],
                ],
                'meta',
            ]);
    }

    public function test_non_admin_cannot_get_pending_race_results(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'role' => 'athlete',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/v1/admin/race-results/pending');

        $response->assertStatus(403)
            ->assertJsonPath('success', false);
    }

    public function test_unauthenticated_user_cannot_get_pending_race_results(): void
    {
        $response = $this->getJson('/api/v1/admin/race-results/pending');

        $response->assertStatus(401);
    }

    public function test_admin_can_approve_race_result(): void
    {
        $admin = User::factory()->create();
        $adminProfile = UserProfile::factory()->create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $result = RaceResult::factory()->create(['is_approved' => false]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/admin/race-results/{$result->id}/approve");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_approved', true)
            ->assertJsonPath('message', 'Результат успешно подтверждён.');

        $this->assertDatabaseHas('race_results', [
            'id' => $result->id,
            'is_approved' => true,
            'approved_by' => $admin->id,
        ]);

        $this->assertNotNull($result->fresh()->approved_at);
    }

    public function test_admin_cannot_approve_already_approved_result(): void
    {
        $admin = User::factory()->create();
        $adminProfile = UserProfile::factory()->create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $result = RaceResult::factory()->approved()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/admin/race-results/{$result->id}/approve");

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('errors.message.0', 'Результат уже подтверждён.');
    }

    public function test_non_admin_cannot_approve_race_result(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'role' => 'athlete',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $result = RaceResult::factory()->create(['is_approved' => false]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/admin/race-results/{$result->id}/approve");

        $response->assertStatus(403);
    }

    public function test_admin_can_reject_race_result(): void
    {
        $admin = User::factory()->create();
        $adminProfile = UserProfile::factory()->create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $result = RaceResult::factory()->create(['is_approved' => false]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson("/api/v1/admin/race-results/{$result->id}/reject");

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Результат отклонён и удалён.');

        $this->assertDatabaseMissing('race_results', ['id' => $result->id]);
    }

    public function test_admin_cannot_reject_already_approved_result(): void
    {
        $admin = User::factory()->create();
        $adminProfile = UserProfile::factory()->create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $result = RaceResult::factory()->approved()->create();

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson("/api/v1/admin/race-results/{$result->id}/reject");

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonPath('errors.message.0', 'Нельзя отклонить уже подтверждённый результат.');
    }

    public function test_non_admin_cannot_reject_race_result(): void
    {
        $user = User::factory()->create();
        UserProfile::factory()->create([
            'user_id' => $user->id,
            'role' => 'athlete',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $result = RaceResult::factory()->create(['is_approved' => false]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->deleteJson("/api/v1/admin/race-results/{$result->id}/reject");

        $response->assertStatus(403);
    }

    public function test_approved_result_includes_approver_info(): void
    {
        $admin = User::factory()->create(['name' => 'Admin User']);
        $adminProfile = UserProfile::factory()->create([
            'user_id' => $admin->id,
            'role' => 'admin',
        ]);
        $token = $admin->createToken('auth_token')->plainTextToken;

        $result = RaceResult::factory()->create(['is_approved' => false]);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson("/api/v1/admin/race-results/{$result->id}/approve");

        $response->assertOk()
            ->assertJsonPath('data.approved_by.id', $admin->id)
            ->assertJsonPath('data.approved_by.name', 'Admin User')
            ->assertJsonPath('data.approved_at', fn ($value) => $value !== null);
    }
}

