<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\SetAvatarRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UploadPhotoRequest;
use App\Http\Resources\UserPhotoResource;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\UserResource;
use App\Services\UserProfileService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly UserProfileService $profileService
    ) {}

    /**
     * Get current user profile with photos and avatar.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        // Загружаем профиль и связи пользователя (photos, avatar)
        $user->load(['profile', 'photos', 'avatar']);
        
        // Устанавливаем user для profile с загруженными связями
        // Это нужно для UserProfileResource, который обращается к $this->user->photos и $this->user->avatar
        if ($user->profile) {
            $user->profile->setRelation('user', $user);
        }

        return $this->successResponse([
            'data' => UserResource::make($user),
        ]);
    }

    /**
     * Create or update user profile.
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $profile = $this->profileService->updateProfile($user, $request->safe()->all());

        return $this->successResponse([
            'data' => [
                'profile' => UserProfileResource::make($profile),
            ],
        ]);
    }

    /**
     * Get user photos with pagination.
     */
    public function getPhotos(Request $request): JsonResponse
    {
        $user = $request->user();
        $perPage = (int) $request->get('per_page', 15);
        $perPage = min(max($perPage, 1), 50); // Limit between 1 and 50

        $photos = $user->photos()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->successResponse([
            'data' => UserPhotoResource::collection($photos->items()),
            'pagination' => [
                'current_page' => $photos->currentPage(),
                'per_page' => $photos->perPage(),
                'total' => $photos->total(),
                'last_page' => $photos->lastPage(),
            ],
        ]);
    }

    /**
     * Upload photos.
     */
    public function uploadPhotos(UploadPhotoRequest $request): JsonResponse
    {
        $user = $request->user();
        $photos = $this->profileService->uploadPhotos($user, $request->file('photos'));

        return $this->successResponse([
            'data' => [
                'photos' => UserPhotoResource::collection($photos),
            ],
        ], 201);
    }

    /**
     * Set photo as avatar.
     */
    public function setAvatar(SetAvatarRequest $request): JsonResponse
    {
        $user = $request->user();
        $photo = $this->profileService->setAvatar($user, (int) $request->safe()->photo_id);

        return $this->successResponse([
            'data' => [
                'avatar' => UserPhotoResource::make($photo),
            ],
        ]);
    }

    /**
     * Delete photo.
     */
    public function deletePhoto(Request $request, int $photoId): JsonResponse
    {
        $user = $request->user();

        // Check if photo belongs to user
        $photo = $user->photos()->find($photoId);

        if (!$photo) {
            return $this->errorResponse([
                'photo_id' => ['Фотография не найдена или не принадлежит вам.'],
            ], 404);
        }

        $this->profileService->deletePhoto($user, $photoId);

        return $this->successResponse([
            'message' => 'Фотография успешно удалена.',
        ]);
    }
}
