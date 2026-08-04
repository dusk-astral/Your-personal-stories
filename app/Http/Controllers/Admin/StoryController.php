<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StoryStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoryFilterRequest;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoryRequest;
use App\Models\Story;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;

class StoryController extends Controller
{
    public function index(StoryFilterRequest $request): View
    {
        $stories = Story::query()
            ->with(['user', 'tags'])
            ->filter($request->safe()->only(['status', 'user_id', 'date_from', 'date_to', 'tags']))
            ->sort($request->validated('sort', 'latest'))
            ->simplePaginate()
            ->withQueryString();

        $users = User::query()->orderBy('name')->get(['id', 'name']);
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);

        $filtersApplied = $request->anyFilled(['status', 'user_id', 'date_from', 'date_to']);

        return view('admin.stories.index', compact('stories', 'users', 'filtersApplied', 'tags'));
    }

    public function show(Story $story): View
    {
        Gate::authorize('view', $story);

        $story->load(['user', 'tags']);

        return view('admin.stories.show', compact('story'));
    }

    public function edit(Story $story): View
    {
        Gate::authorize('update', $story);

        $tags = Tag::all();

        return view('admin.stories.edit', compact('tags', 'story'));
    }

    public function update(StoryRequest $request, Story $story): RedirectResponse
    {
        Gate::authorize('update', $story);

        $validated = $request->validated();

        $story->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => StoryStatus::Approved,
            'published_at' => now()
        ]);

        $story->tags()->sync($validated['tags']);

        $story->moderations()->create([
            'moderator_id' => auth()->id(),
            'reason' => $validated['reason'] ?? null,
            'action' => StoryStatus::Approved
        ]);

        return redirect()->route('admin.stories.index')->with('status', 'Story updated successfully!');
    }

    public function destroy(Story $story): RedirectResponse
    {
        Gate::authorize('delete', $story);

        $story->delete();

        return redirect()->route('admin.stories.index')->with('status', 'Story deleted successfully!');
    }

    public function approve(Story $story): RedirectResponse
    {
        Gate::authorize('approve', $story);

        $story->update([
            'status' => StoryStatus::Approved,
            'published_at' => now()
        ]);

        return redirect()->route('admin.stories.index')->with('status', 'Story approved successfully!');
    }

    public function reject(Request $request, Story $story): RedirectResponse
    {
        Gate::authorize('reject', $story);

        if ($request->input('reason')) {
            $validated = $request->validate(['reason' => ['required', 'string', 'max:255']]);
        }

        $story->update([
            'status' => StoryStatus::Rejected,
        ]);

        $story->moderations()->create([
            'moderator_id' => auth()->id(),
            'reason' => $validated['reason'] ?? null,
            'action' => StoryStatus::Rejected,
        ]);

        return redirect()->route('admin.stories.index')->with('status', 'Story rejected successfully!');
    }
}
