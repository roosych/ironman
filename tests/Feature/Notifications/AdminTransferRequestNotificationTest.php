<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Events\TransferRequestCreated;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\Admin\NewTransferRequestSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminTransferRequestNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_request_created_event_is_dispatched(): void
    {
        Event::fake();

        $athlete = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $athlete->id]);
        // Source profile must be unlinked (no user_id), role=athlete, results_transferred=false
        $sourceProfile = UserProfile::factory()->create([
            'user_id' => null,
            'role' => 'athlete',
            'results_transferred' => false,
        ]);
        $token = $athlete->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/transfer/request', [
                'source_athlete_id' => $sourceProfile->id,
            ])
            ->assertCreated();

        Event::assertDispatched(TransferRequestCreated::class);
    }

    public function test_admins_receive_email_when_transfer_request_created(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['is_admin' => true]);
        $athlete = User::factory()->create(['is_admin' => false]);
        UserProfile::factory()->create(['user_id' => $athlete->id]);
        $sourceProfile = UserProfile::factory()->create([
            'user_id' => null,
            'role' => 'athlete',
            'results_transferred' => false,
        ]);
        $token = $athlete->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/transfer/request', [
                'source_athlete_id' => $sourceProfile->id,
            ])
            ->assertCreated();

        Notification::assertSentTo($admin, NewTransferRequestSubmittedNotification::class);
        Notification::assertNotSentTo($athlete, NewTransferRequestSubmittedNotification::class);
    }

    public function test_no_notification_sent_when_no_admins_exist(): void
    {
        Notification::fake();

        $athlete = User::factory()->create(['is_admin' => false]);
        UserProfile::factory()->create(['user_id' => $athlete->id]);
        $sourceProfile = UserProfile::factory()->create(['user_id' => null, 'role' => 'athlete', 'results_transferred' => false]);
        $token = $athlete->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/transfer/request', [
                'source_athlete_id' => $sourceProfile->id,
            ])
            ->assertCreated();

        Notification::assertNothingSent();
    }
}