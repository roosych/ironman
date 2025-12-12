<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_verify_email(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertOk()
            ->assertViewIs('auth.verify-email-success')
            ->assertViewHas('user')
            ->assertViewHas('message');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_user_profile_created_after_email_verification(): void
    {
        $user = User::factory()->unverified()->create();

        // Ensure no profile exists before verification
        $this->assertNull($user->profile);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->get($verificationUrl);

        // Refresh user and check profile was created
        $user->refresh();
        $this->assertNotNull($user->profile);
        $this->assertEquals('athlete', $user->profile->role);
        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'role' => 'athlete',
        ]);
    }

    public function test_already_verified_user_does_not_duplicate_profile(): void
    {
        $user = User::factory()->create(); // Already verified

        // Create profile manually (simulating already existing profile)
        \App\Models\UserProfile::create([
            'user_id' => $user->id,
            'role' => 'coach',
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->get($verificationUrl);

        // Profile should remain unchanged (coach role)
        $user->refresh();
        $this->assertEquals('coach', $user->profile->role);
        $this->assertDatabaseCount('user_profiles', 1);
    }

    public function test_email_verification_fails_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => 'invalid-hash']
        );

        $response = $this->get($verificationUrl);

        $response->assertOk()
            ->assertViewIs('auth.verify-email-error')
            ->assertViewHas('message');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_fails_with_invalid_signature(): void
    {
        $user = User::factory()->unverified()->create();

        // URL without valid signature
        $verificationUrl = route('verification.verify', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $response = $this->get($verificationUrl);

        $response->assertStatus(403); // Invalid signature returns 403
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_email_verification_fails_with_expired_link(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->subMinutes(5), // Expired
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertStatus(403); // Expired signature returns 403
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_already_verified_email_shows_success_page(): void
    {
        $user = User::factory()->create(); // Already verified

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->get($verificationUrl);

        $response->assertOk()
            ->assertViewIs('auth.verify-email-success')
            ->assertViewHas('user')
            ->assertViewHas('message');
        $this->assertStringContainsString('уже был подтверждён', $response->getContent());
    }

    public function test_user_can_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/email/resend-verification');

        $response->assertOk()
            ->assertJsonPath('success', true);

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_verified_user_cannot_resend_verification_email(): void
    {
        Notification::fake();

        $user = User::factory()->create(); // Already verified
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/email/resend-verification');

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonValidationErrors(['email']);

        Notification::assertNotSentTo($user, VerifyEmailNotification::class);
    }

    public function test_resend_verification_email_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/auth/email/resend-verification');

        $response->assertStatus(401);
    }

    public function test_mobile_client_shows_success_page(): void
    {
        $user = User::factory()->unverified()->create();

        // Include client=mobile in signed route parameters
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email), 'client' => 'mobile']
        );

        $response = $this->get($verificationUrl);

        // Теперь показываем view вместо редиректа
        $response->assertOk()
            ->assertViewIs('auth.verify-email-success');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}
