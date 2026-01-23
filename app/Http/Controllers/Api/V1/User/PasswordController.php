<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\User;

use App\Events\PasswordChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    use ApiResponse;

    /**
     * Change authenticated user's password.
     *
     * Keeps current session active and invalidates all other tokens.
     */
    public function update(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();
        $locale = $user->locale ?? config('app.locale', 'en');
        $originalLocale = App::getLocale();
        App::setLocale($locale);

        if (! Hash::check($request->current_password, $user->password)) {
            $errorMessage = trans('api.password.invalid_credentials');
            App::setLocale($originalLocale);

            return $this->errorResponse([
                'current_password' => [$errorMessage],
            ], 403);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Invalidate all tokens except the current one
        $currentTokenId = $user->currentAccessToken()->id;
        $user->tokens()->where('id', '!=', $currentTokenId)->delete();

        // Dispatch event for FCM notification
        event(new PasswordChanged($user));

        $successMessage = trans('api.password.changed_success');
        App::setLocale($originalLocale);

        return $this->successResponse([
            'message' => $successMessage,
        ]);
    }
}
