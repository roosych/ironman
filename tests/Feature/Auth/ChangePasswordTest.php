<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_change_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'old_password_123',
                'new_password' => 'newStrongPassword123',
                'new_password_confirmation' => 'newStrongPassword123',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Password successfully changed.');

        // Verify password is actually updated
        $user->refresh();
        $this->assertTrue(Hash::check('newStrongPassword123', $user->password));
        $this->assertFalse(Hash::check('old_password_123', $user->password));
    }

    public function test_change_password_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'wrong_password',
                'new_password' => 'newStrongPassword123',
                'new_password_confirmation' => 'newStrongPassword123',
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('success', false)
            ->assertJsonPath('errors.current_password.0', 'Current password is incorrect.');

        // Verify password is not changed
        $user->refresh();
        $this->assertTrue(Hash::check('old_password_123', $user->password));
    }

    public function test_change_password_fails_when_new_password_same_as_current(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'old_password_123',
                'new_password' => 'old_password_123',
                'new_password_confirmation' => 'old_password_123',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_change_password_fails_when_confirmation_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'old_password_123',
                'new_password' => 'newStrongPassword123',
                'new_password_confirmation' => 'differentPassword123',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_change_password_fails_when_new_password_too_short(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'old_password_123',
                'new_password' => 'short',
                'new_password_confirmation' => 'short',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_change_password_fails_without_authentication(): void
    {
        $response = $this->putJson('/api/v1/user/password', [
            'current_password' => 'old_password_123',
            'new_password' => 'newStrongPassword123',
            'new_password_confirmation' => 'newStrongPassword123',
        ]);

        $response->assertStatus(401);
    }

    public function test_change_password_fails_without_required_fields(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', []);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['current_password', 'new_password']);
    }

    public function test_change_password_fails_without_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'old_password_123',
                'new_password' => 'newStrongPassword123',
            ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_current_session_remains_active_after_password_change(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'old_password_123',
                'new_password' => 'newStrongPassword123',
                'new_password_confirmation' => 'newStrongPassword123',
            ])
            ->assertOk();

        // Current token should still work
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/user');

        $response->assertOk()
            ->assertJsonPath('data.id', $user->id);
    }

    public function test_other_tokens_are_invalidated_after_password_change(): void
    {
        $user = User::factory()->create([
            'password' => 'old_password_123',
        ]);

        // Create multiple tokens (simulating multiple devices)
        $user->createToken('old_device_1');
        $user->createToken('old_device_2');
        $currentToken = $user->createToken('current_device')->plainTextToken;

        $this->assertDatabaseCount('personal_access_tokens', 3);

        // Change password using current token
        $this->withHeader('Authorization', 'Bearer ' . $currentToken)
            ->putJson('/api/v1/user/password', [
                'current_password' => 'old_password_123',
                'new_password' => 'newStrongPassword123',
                'new_password_confirmation' => 'newStrongPassword123',
            ])
            ->assertOk();

        // Only current token should remain (other tokens invalidated)
        $this->assertDatabaseCount('personal_access_tokens', 1);

        // Verify the remaining token belongs to the current session
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'current_device',
        ]);
    }
}
