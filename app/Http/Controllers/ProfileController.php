<?php

namespace App\Http\Controllers;

use App\Enums\StoryStatus;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();

        $stories = $user->stories()->latest()->simplePaginate();

        return view('profile.show', compact('user', 'stories'));
    }

    public function edit(): View
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar_path' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048']
        ]);

        if ($request->hasFile('avatar_path')) {
            $oldAvatar = $user->avatar_path;

            if ($oldAvatar && Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $extension = $user->id . '.' . $request->file('avatar_path')->extension();
            $path = $request->file('avatar_path')->storeAs('avatars', $extension, 'public');
            $validated['avatar_path'] = $path;
        }

        $user->update($validated);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /*
     * Method for public profile
     */
    public function publicShow(User $user): View
    {
        $stories = $user->stories()
            ->where('status', StoryStatus::Approved)
            ->latest()
            ->simplePaginate(5);

        return view('profile.public', compact('user', 'stories'));
    }
}
