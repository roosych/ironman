<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\RaceType;
use App\Events\RaceApproved;
use App\Http\Controllers\Controller;
use App\Models\RaceResult;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminRaceResultController extends Controller
{
    /**
     * Show list of pending race results.
     */
    public function pending(): View
    {
        $results = RaceResult::with(['profile' => function ($query) {
                $query->withoutGlobalScope('hide_reviewer_profiles')
                    ->with('user');
            }, 'approver'])
            ->where('is_approved', false)
            ->orderBy('created_at')
            ->paginate(20);

        return view('admin.race-results.pending', compact('results'));
    }

    /**
     * Show form to create a new race result.
     */
    public function create(): View
    {
        $profiles = UserProfile::withoutGlobalScope('hide_reviewer_profiles')
            ->with('user')
            ->orderBy('admin_full_name')
            ->get();

        $raceTypes = RaceType::cases();

        return view('admin.race-results.create', compact('profiles', 'raceTypes'));
    }

    /**
     * Store a new race result.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'user_profile_id' => ['required', 'exists:user_profiles,id'],
            'race_date' => ['required', 'date'],
            'location' => ['required', 'string', 'max:255'],
            'race_type' => ['required', 'string', Rule::enum(RaceType::class)],
            'swim_time' => ['nullable', 'integer', 'min:0'],
            't1_time' => ['nullable', 'integer', 'min:0'],
            'bike_time' => ['nullable', 'integer', 'min:0'],
            't2_time' => ['nullable', 'integer', 'min:0'],
            'run_time' => ['nullable', 'integer', 'min:0'],
            'total_time' => ['required', 'integer', 'min:0'],
            'is_approved' => ['nullable', 'boolean'],
        ]);

        // Конвертируем время в секунды, если пришло в формате строки (на всякий случай)
        $timeFields = ['swim_time', 't1_time', 'bike_time', 't2_time', 'run_time', 'total_time'];
        foreach ($timeFields as $field) {
            if (isset($validated[$field]) && is_string($validated[$field]) && str_contains($validated[$field], ':')) {
                $parts = explode(':', $validated[$field]);
                if (count($parts) === 3) {
                    $validated[$field] = (int) $parts[0] * 3600 + (int) $parts[1] * 60 + (int) $parts[2];
                }
            }
        }

        // Админ может сразу одобрить результат
        $validated['is_approved'] = $request->boolean('is_approved', true);
        
        if ($validated['is_approved']) {
            $validated['approved_at'] = now();
            $validated['approved_by'] = auth()->id();
        }

        $raceResult = RaceResult::create($validated);
        $raceResult->load(['profile.user', 'approver']);

        // Dispatch event only for approved results
        if ($raceResult->is_approved) {
            event(new RaceApproved($raceResult));
        }

        $message = 'Результат успешно создан' . ($raceResult->is_approved ? ' и одобрен' : ' (ожидает одобрения)') . '.';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'redirect' => route('admin.race-results.pending'),
            ]);
        }

        return redirect()
            ->route('admin.race-results.pending')
            ->with('success', $message);
    }

    /**
     * Approve a race result.
     */
    public function approve(Request $request, RaceResult $raceResult): RedirectResponse|JsonResponse
    {
        if ($raceResult->is_approved) {
            $message = 'Результат уже подтверждён.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        $raceResult->update([
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        $raceResult->load(['profile.user', 'approver']);

        // Dispatch event for FCM notification
        event(new RaceApproved($raceResult));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Результат успешно подтверждён.',
                'reload' => true,
            ]);
        }

        return back()->with('success', 'Результат успешно подтверждён.');
    }

    /**
     * Reject a race result (delete it).
     */
    public function reject(Request $request, RaceResult $raceResult): RedirectResponse|JsonResponse
    {
        if ($raceResult->is_approved) {
            $message = 'Нельзя отклонить уже подтверждённый результат.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        $raceResult->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Результат отклонён и удалён.',
                'reload' => true,
            ]);
        }

        return back()->with('success', 'Результат отклонён и удалён.');
    }
}
