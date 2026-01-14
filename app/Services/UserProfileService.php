<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\UserPhoto;
use App\Models\UserProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserProfileService
{
    /**
     * Create or update user profile.
     *
     * @param  array<string, mixed>  $data
     */
    public function updateProfile(User $user, array $data): UserProfile
    {
        return DB::transaction(function () use ($user, $data) {
            // Если передано имя, обновляем его в таблице users и admin_full_name в user_profiles
            if (isset($data['name'])) {
                $user->update(['name' => $data['name']]);
                $data['admin_full_name'] = $data['name'];
                unset($data['name']); // Удаляем из данных, так как это поле для users, а не user_profiles
            } elseif (! $user->profile) {
                // Set admin_full_name from user's name if creating new profile
                $data['admin_full_name'] = $user->name;
            }

            return $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $data
            );
        });
    }

    /**
     * Upload photos for user.
     *
     * @param  array<UploadedFile>  $files
     * @return array<UserPhoto>
     */
    public function uploadPhotos(User $user, array $files): array
    {
        $photos = [];

        foreach ($files as $file) {
            $filename = $this->generateFilename($file);
            $path = $file->storeAs(
                "profile_photos/{$user->id}",
                $filename,
                'public'
            );

            $photos[] = $user->photos()->create([
                'path' => $path,
                'filename' => $filename,
                'is_avatar' => false,
            ]);
        }

        return $photos;
    }

    /**
     * Set photo as avatar.
     */
    public function setAvatar(User $user, int $photoId): UserPhoto
    {
        return DB::transaction(function () use ($user, $photoId) {
            // Reset current avatar
            $user->photos()->where('is_avatar', true)->update(['is_avatar' => false]);

            // Set new avatar
            $photo = $user->photos()->findOrFail($photoId);
            $photo->update(['is_avatar' => true]);

            return $photo->fresh();
        });
    }

    /**
     * Delete photo.
     */
    public function deletePhoto(User $user, int $photoId): bool
    {
        return DB::transaction(function () use ($user, $photoId) {
            $photo = $user->photos()->findOrFail($photoId);

            // Delete file from storage
            if (Storage::disk('public')->exists($photo->path)) {
                Storage::disk('public')->delete($photo->path);
            }

            return $photo->delete();
        });
    }

    /**
     * Generate unique filename for uploaded file.
     */
    private function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $hash = substr(md5(uniqid((string) mt_rand(), true)), 0, 16);

        return "{$hash}.{$extension}";
    }
}
