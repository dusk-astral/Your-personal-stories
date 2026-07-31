<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StoryStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): View
    {
        $usersCount = [
            'total' => User::count(),
            'user' => User::where('role', UserRole::User)->count(),
            'moderator' => User::where('role', UserRole::Moderator)->count(),
            'admin' => User::where('role', UserRole::Admin)->count(),
        ];

        $storyCount = [
            'total' => Story::count(),
            'pending' => Story::where('status', StoryStatus::Pending)->count(),
            'approved' => Story::where('status', StoryStatus::Approved)->count(),
            'rejected' => Story::where('status', StoryStatus::Rejected)->count(),
        ];

        $latestUsers = User::latest()->limit(5)->get();
        $latestStories = Story::latest()->limit(5)->get();

        return view('admin.dashboard', compact('latestUsers', 'latestStories', 'usersCount', 'storyCount'));
    }
}
