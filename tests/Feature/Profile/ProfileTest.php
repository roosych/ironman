<?php

declare(strict_types=1);

namespace Tests\Feature\Profile;

use App\Models\User;
use App\Models\UserPhoto;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function authenticatedUser(): array
    {
        $user = User::factory()->create();
        $token = $user->createToken('auth_token')->plainTextToken;

        return [$user, $token];
    }

    // ==================== SHOW PROFILE ====================

    public function test_user_can_get_profile(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user/profile');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'verified',
                    'profile',
                ],
            ]);
        
        // Проверяем, что если profile существует, то в нем есть нужные поля
        $profile = $response->json('data.profile');
        if ($profile !== null) {
            $response->assertJsonStructure([
                'data' => [
                    'profile' => ['role', 'ironman_number', 'bio', 'social_links', 'photos'],
                ],
            ]);
        }
    }

    public function test_user_can_get_profile_with_existing_data(): void
    {
        [$user, $token] = $this->authenticatedUser();

        UserProfile::create([
            'user_id' => $user->id,
            'role' => 'athlete',
            'ironman_number' => 'IM12345',
            'bio' => 'Test bio',
            'social_links' => ['strava' => 'https://strava.com/test'],
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user/profile');

        $response->assertOk()
            ->assertJsonPath('data.profile.role', 'athlete')
            ->assertJsonPath('data.profile.ironman_number', 'IM12345');
    }

    public function test_get_profile_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/user/profile');

        $response->assertStatus(401);
    }

    // ==================== UPDATE PROFILE ====================

    public function test_user_can_create_profile(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/user/profile', [
                'role' => 'athlete',
                'ironman_number' => 'IM12345',
                'bio' => 'Test athlete bio',
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.profile.role', 'athlete')
            ->assertJsonPath('data.profile.ironman_number', 'IM12345');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'role' => 'athlete',
            'ironman_number' => 'IM12345',
        ]);
    }

    public function test_user_can_update_existing_profile(): void
    {
        [$user, $token] = $this->authenticatedUser();

        UserProfile::create([
            'user_id' => $user->id,
            'role' => 'athlete',
            'ironman_number' => 'IM12345',
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/user/profile', [
                'role' => 'coach',
                'ironman_number' => null,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.profile.role', 'coach');

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'role' => 'coach',
        ]);
    }

    public function test_user_can_update_social_links(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/user/profile', [
                'social_links' => [
                    'strava' => 'https://strava.com/athletes/12345',
                    'instagram' => 'test_athlete',
                    'facebook' => 'https://facebook.com/test',
                ],
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
        ]);
    }

    public function test_update_profile_validates_role(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/user/profile', [
                'role' => 'invalid_role',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    public function test_update_profile_validates_social_links(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/v1/user/profile', [
                'social_links' => [
                    'strava' => 'not-a-valid-url',
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['social_links.strava']);
    }

    // ==================== GET PHOTOS ====================

    public function test_user_can_get_photos(): void
    {
        [$user, $token] = $this->authenticatedUser();

        // Create some photos
        $user->photos()->createMany([
            ['path' => 'photo1.jpg', 'filename' => 'photo1.jpg', 'is_avatar' => false],
            ['path' => 'photo2.jpg', 'filename' => 'photo2.jpg', 'is_avatar' => false],
            ['path' => 'photo3.jpg', 'filename' => 'photo3.jpg', 'is_avatar' => true],
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user/photos');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'url', 'filename', 'is_avatar', 'created_at'],
                ],
                'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
            ]);
    }

    public function test_get_photos_returns_paginated_results(): void
    {
        [$user, $token] = $this->authenticatedUser();

        // Create 25 photos
        for ($i = 1; $i <= 25; $i++) {
            $user->photos()->create([
                'path' => "photo{$i}.jpg",
                'filename' => "photo{$i}.jpg",
                'is_avatar' => false,
            ]);
        }

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user/photos?per_page=10');

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('pagination.per_page', 10)
            ->assertJsonPath('pagination.total', 25)
            ->assertJsonPath('pagination.last_page', 3);
    }

    public function test_get_photos_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/user/photos');

        $response->assertStatus(401);
    }

    public function test_get_photos_returns_only_user_photos(): void
    {
        [$user, $token] = $this->authenticatedUser();
        $otherUser = User::factory()->create();

        // Create photos for both users
        $user->photos()->create(['path' => 'user1.jpg', 'filename' => 'user1.jpg', 'is_avatar' => false]);
        $otherUser->photos()->create(['path' => 'user2.jpg', 'filename' => 'user2.jpg', 'is_avatar' => false]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/v1/user/photos');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.filename', 'user1.jpg');
    }

    // ==================== UPLOAD PHOTOS ====================

    public function test_user_can_upload_single_photo(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/photos', [
                'photos' => [
                    UploadedFile::fake()->image('photo.jpg', 800, 600),
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data.photos');

        $this->assertDatabaseCount('user_photos', 1);
        $this->assertDatabaseHas('user_photos', [
            'user_id' => $user->id,
            'is_avatar' => false,
        ]);
    }

    public function test_user_can_upload_multiple_photos(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/photos', [
                'photos' => [
                    UploadedFile::fake()->image('photo1.jpg'),
                    UploadedFile::fake()->image('photo2.png'),
                    UploadedFile::fake()->image('photo3.webp'),
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonCount(3, 'data.photos');

        $this->assertDatabaseCount('user_photos', 3);
    }

    public function test_upload_photo_validates_file_type(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/photos', [
                'photos' => [
                    UploadedFile::fake()->create('document.pdf', 1000),
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photos.0']);
    }

    public function test_upload_photo_validates_file_size(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/photos', [
                'photos' => [
                    UploadedFile::fake()->image('large.jpg')->size(6000), // 6MB
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photos.0']);
    }

    public function test_upload_photo_requires_at_least_one_photo(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/photos', [
                'photos' => [],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photos']);
    }

    // ==================== SET AVATAR ====================

    public function test_user_can_set_avatar(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $photo = UserPhoto::create([
            'user_id' => $user->id,
            'path' => 'profile_photos/test.jpg',
            'filename' => 'test.jpg',
            'is_avatar' => false,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/profile/avatar', [
                'photo_id' => $photo->id,
            ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.photo.is_avatar', true);

        $this->assertDatabaseHas('user_photos', [
            'id' => $photo->id,
            'is_avatar' => true,
        ]);
    }

    public function test_set_avatar_replaces_previous_avatar(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $oldAvatar = UserPhoto::create([
            'user_id' => $user->id,
            'path' => 'profile_photos/old.jpg',
            'filename' => 'old.jpg',
            'is_avatar' => true,
        ]);

        $newPhoto = UserPhoto::create([
            'user_id' => $user->id,
            'path' => 'profile_photos/new.jpg',
            'filename' => 'new.jpg',
            'is_avatar' => false,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/profile/avatar', [
                'photo_id' => $newPhoto->id,
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('user_photos', [
            'id' => $oldAvatar->id,
            'is_avatar' => false,
        ]);

        $this->assertDatabaseHas('user_photos', [
            'id' => $newPhoto->id,
            'is_avatar' => true,
        ]);
    }

    public function test_set_avatar_fails_for_other_users_photo(): void
    {
        [$user, $token] = $this->authenticatedUser();
        $otherUser = User::factory()->create();

        $otherPhoto = UserPhoto::create([
            'user_id' => $otherUser->id,
            'path' => 'profile_photos/other.jpg',
            'filename' => 'other.jpg',
            'is_avatar' => false,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/v1/user/profile/avatar', [
                'photo_id' => $otherPhoto->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['photo_id']);
    }

    // ==================== DELETE PHOTO ====================

    public function test_user_can_delete_photo(): void
    {
        [$user, $token] = $this->authenticatedUser();

        Storage::disk('public')->put('profile_photos/test.jpg', 'content');

        $photo = UserPhoto::create([
            'user_id' => $user->id,
            'path' => 'profile_photos/test.jpg',
            'filename' => 'test.jpg',
            'is_avatar' => false,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/user/photos/{$photo->id}");

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('user_photos', [
            'id' => $photo->id,
        ]);
    }

    public function test_delete_photo_fails_for_other_users_photo(): void
    {
        [$user, $token] = $this->authenticatedUser();
        $otherUser = User::factory()->create();

        $otherPhoto = UserPhoto::create([
            'user_id' => $otherUser->id,
            'path' => 'profile_photos/other.jpg',
            'filename' => 'other.jpg',
            'is_avatar' => false,
        ]);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson("/api/v1/user/photos/{$otherPhoto->id}");

        $response->assertStatus(404);

        $this->assertDatabaseHas('user_photos', [
            'id' => $otherPhoto->id,
        ]);
    }

    public function test_delete_photo_fails_for_nonexistent_photo(): void
    {
        [$user, $token] = $this->authenticatedUser();

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->deleteJson('/api/v1/user/photos/99999');

        $response->assertStatus(404);
    }
}
