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
use App\Models\UserProfile;
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
     * Get current user profile with photos.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['photos']);

        // Загружаем профиль без глобального scope для скрытого пользователя
        $profile = UserProfile::withoutGlobalScope('hide_reviewer_profiles')
            ->where('user_id', $user->id)
            ->first();
        
        if ($profile) {
            $user->setRelation('profile', $profile);
            // Load only approved race results
            $profile->load(['raceResults' => function ($query) {
                $query->where('is_approved', true)
                    ->orderByDesc('race_date');
            }]);
            $profile->setRelation('user', $user);
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

        // Загружаем фото для отображения в ответе
        $user->load('photos');
        $profile->setRelation('user', $user);

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
                'photo' => UserPhotoResource::make($photo),
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

        if (! $photo) {
            return $this->errorResponse([
                'photo_id' => ['Фотография не найдена или не принадлежит вам.'],
            ], 404);
        }

        $this->profileService->deletePhoto($user, $photoId);

        return $this->successResponse([
            'message' => 'Фотография успешно удалена.',
        ]);
    }


    /**
     * Link an existing profile to the authenticated user by code.
     */
    public function link(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:6', 'regex:/^[1-9A-HJ-NP-Za-hj-np-z]{6}$/i'],
        ]);

        $user = $request->user();

        // Проверяем, что у пользователя еще нет профиля
        $existingProfile = UserProfile::withoutGlobalScope('hide_reviewer_profiles')
            ->where('user_id', $user->id)
            ->first();

        if ($existingProfile) {
            return $this->errorResponse([
                'profile' => ['PROFILE_ALREADY_EXISTS'],
            ], 422, 'PROFILE_ALREADY_EXISTS');
        }

        // Приводим код к верхнему регистру для поиска
        $code = strtoupper($validated['code']);

        // Получаем профиль по коду
        $profile = UserProfile::withoutGlobalScope('hide_reviewer_profiles')
            ->where('code', $code)
            ->where('role', 'athlete')
            ->first();

        if (!$profile) {
            return $this->errorResponse([
                'code' => ['CODE_NOT_FOUND'],
            ], 404, 'CODE_NOT_FOUND');
        }

        // Проверяем, что код еще не использован
        if ($profile->code_used) {
            return $this->errorResponse([
                'code' => ['CODE_ALREADY_USED'],
            ], 422, 'CODE_ALREADY_USED');
        }

        // Проверяем, что профиль не привязан к другому пользователю
        if ($profile->user_id !== null) {
            return $this->errorResponse([
                'code' => ['CODE_ALREADY_LINKED'],
            ], 422, 'CODE_ALREADY_LINKED');
        }

        // Привязываем профиль к пользователю и отмечаем код как использованный
        $profile->update([
            'user_id' => $user->id,
            'code_used' => true,
        ]);

        // Обновляем admin_full_name из имени пользователя, если не установлено
        if (!$profile->admin_full_name) {
            $profile->update(['admin_full_name' => $user->name]);
        }

        // Загружаем профиль с проверенными результатами для ответа
        $profile->load(['raceResults' => function ($query) {
            $query->where('is_approved', true)
                ->orderByDesc('race_date');
        }]);
        $profile->setRelation('user', $user);
        $user->setRelation('profile', $profile);

        // Dispatch event for FCM notification
        event(new \App\Events\ProfileSynced($user, $profile));

        return $this->successResponse([
            'message' => 'PROFILE_LINKED_SUCCESS',
            'data' => [
                'profile' => UserProfileResource::make($profile),
            ],
        ]);
    }

    /**
     * Create a new empty profile for the authenticated user.
     */
    public function create(Request $request): JsonResponse
    {
        $user = $request->user();

        // Проверяем, что у пользователя еще нет профиля
        $existingProfile = UserProfile::withoutGlobalScope('hide_reviewer_profiles')
            ->where('user_id', $user->id)
            ->first();

        if ($existingProfile) {
            return $this->errorResponse([
                'profile' => ['PROFILE_ALREADY_EXISTS'],
            ], 422, 'PROFILE_ALREADY_EXISTS');
        }

        // Создаем новый пустой профиль
        $profile = UserProfile::create([
            'user_id' => $user->id,
            'admin_full_name' => $user->name,
            'role' => 'athlete',
        ]);

        $profile->setRelation('user', $user);
        $user->setRelation('profile', $profile);

        return $this->successResponse([
            'message' => 'PROFILE_CREATED_SUCCESS',
            'data' => [
                'profile' => UserProfileResource::make($profile),
            ],
        ], 201);
    }
}
