<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications;

use App\Events\RaceCreated;
use App\Models\User;
use App\Models\UserProfile;
use App\Notifications\Admin\NewRaceResultSubmittedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminRaceResultNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function raceResultData(): array
    {
        return [
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
    }

    public function test_race_created_event_is_dispatched_when_athlete_submits_result(): void
    {
        Event::fake();

        $user = User::factory()->create();
        UserProfile::factory()->create(['user_id' => $user->id]);
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/race-results', $this->raceResultData())
            ->assertCreated();

        Event::assertDispatched(RaceCreated::class);
    }

    public function test_admins_receive_email_notification_when_race_result_submitted(): void
    {
        Notification::fake();

        $admin1 = User::factory()->create(['is_admin' => true]);
        $admin2 = User::factory()->create(['is_admin' => true]);
        $athlete = User::factory()->create(['is_admin' => false]);
        UserProfile::factory()->create(['user_id' => $athlete->id]);
        $token = $athlete->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/race-results', $this->raceResultData())
            ->assertCreated();

        Notification::assertSentTo($admin1, NewRaceResultSubmittedNotification::class);
        Notification::assertSentTo($admin2, NewRaceResultSubmittedNotification::class);
        Notification::assertNotSentTo($athlete, NewRaceResultSubmittedNotification::class);
    }

    public function test_non_admin_users_do_not_receive_race_result_notification(): void
    {
        Notification::fake();

        $regularUser = User::factory()->create(['is_admin' => false]);
        $athlete = User::factory()->create(['is_admin' => false]);
        UserProfile::factory()->create(['user_id' => $athlete->id]);
        $token = $athlete->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/race-results', $this->raceResultData())
            ->assertCreated();

        Notification::assertNotSentTo($regularUser, NewRaceResultSubmittedNotification::class);
    }

    public function test_no_notification_sent_when_no_admins_exist(): void
    {
        Notification::fake();

        $athlete = User::factory()->create(['is_admin' => false]);
        UserProfile::factory()->create(['user_id' => $athlete->id]);
        $token = $athlete->createToken('auth_token')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/race-results', $this->raceResultData())
            ->assertCreated();

        Notification::assertNothingSent();
    }
}
