<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Notification
 */
class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get user locale from request (authenticated user) or default
        $user = $request->user();
        
        // Always get fresh locale from database
        $locale = config('app.locale', 'en');
        if ($user) {
            try {
                // Reload user to get fresh locale from database
                $freshUser = \App\Models\User::withoutGlobalScopes()->find($user->id);
                $locale = $freshUser?->locale ?? config('app.locale', 'en');
            } catch (\Exception $e) {
                // If reload fails, use user's current locale or default
                $locale = $user->locale ?? config('app.locale', 'en');
            }
        }
        
        // Use translation from JSON if available, otherwise fallback to stored title/body
        $title = $this->getTitle($locale);
        $body = $this->getBody($locale);

        // Build translations object for all available locales
        $translations = [];
        if ($this->translations && is_array($this->translations)) {
            // Return all translations from JSON field
            foreach ($this->translations as $lang => $translation) {
                $translations[$lang] = [
                    'title' => $translation['title'] ?? $this->title ?? '',
                    'body' => $translation['body'] ?? $this->body ?? '',
                ];
            }
        } else {
            // Fallback: if no translations JSON, create from stored title/body for current locale
            $translations[$locale] = [
                'title' => $this->title ?? '',
                'body' => $this->body ?? '',
            ];
        }

        return [
            'id' => $this->id,
            'title' => $title, // Current user's locale (for backward compatibility)
            'body' => $body,   // Current user's locale (for backward compatibility)
            'translations' => $translations, // All available translations
            'type' => $this->type,
            'data' => $this->data ?? [],
            'read_at' => $this->read_at?->toIso8601String(),
            'is_read' => $this->isRead(),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
