<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StoryStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use App\Enums\UserRole;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->withCount('stories')
            ->oldest()
            ->simplePaginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function editRole(User $user): View
    {
        $roles = UserRole::cases();

        return view('admin.users.role', compact('user' , 'roles'));
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        Gate::authorize('update', $user);

        $newRole = $request->validate([
            'role' => ['required', 'string', 'in:user,moderator,admin']
        ]);

        $user->update([
            'role' => $newRole['role']
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User role updated!');
    }

    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted!');
    }

    public function block(User $user): RedirectResponse
    {
        Gate::authorize('block', $user);

        $user->update(['is_blocked' => true]);

        return redirect()->route('admin.users.index')->with('success', 'User blocked!');
    }

    public function unblock(User $user): RedirectResponse
    {
        Gate::authorize('unblock', $user);

        $user->update(['is_blocked' => false]);

        return redirect()->route('admin.users.index')->with('success', 'User unblocked!');
    }
}
