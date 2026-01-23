<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RaceResult;
use App\Models\UserProfile;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     */
    public function index(): View
    {
        $stats = [
            'pending_race_results' => RaceResult::where('is_approved', false)->count(),
            'total_profiles' => UserProfile::count(),
            'total_users' => \App\Models\User::withoutGlobalScope('hide_reviewer')->count(),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
