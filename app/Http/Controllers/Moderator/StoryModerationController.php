<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoryFilterRequest;
use App\Models\Story;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Enums\StoryStatus;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class StoryModerationController extends Controller
{
    public function index(StoryFilterRequest $request): View
    {
        $stories = Story::query()
            ->where('status', StoryStatus::Pending )
            ->filter($request->safe()->only(['user_id', 'date_from', 'date_to', 'tags']))
            ->sort($request->validated('sort', 'latest'))
            ->with(['user', 'tags'])
            ->latest()
            ->simplePaginate(5)
            ->withQueryString();

        $users = User::query()->orderBy('name')->get(['id', 'name']);
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);

        $filtersApplied = $request->anyFilled(['user_id', 'date_from', 'date_to', 'tags']);

        return view('moderator.stories.index', compact('stories', 'users', 'tags', 'filtersApplied'));
    }

    public function show(Story $story): View
    {
        Gate::authorize('view', $story);

        $story->load(['user', 'tags']);

        return view('moderator.stories.show', compact('story'));
    }

    public function approve(Story $story): RedirectResponse
    {
        Gate::authorize('approve', $story);

        $story->update([
            'status' => StoryStatus::Approved,
            'published_at' => now(),
        ]);

        $story->moderations()->create([
            'moderator_id' => auth()->id(),
            'action' => StoryStatus::Approved,
            'reason' => null
        ]);

        return back();
    }

    public function reject(Request $request, Story $story): RedirectResponse
    {
        Gate::authorize('reject', $story);

        if ($request->input('reason')) {
            $validated = $request->validate(['reason' => ['required', 'string', 'max:255']]);
        }

        $story->update(['status' => StoryStatus::Rejected]);

        $story->moderations()->create([
            'moderator_id' => auth()->id(),
            'reason' => $validated['reason'] ?? null,
            'action' => StoryStatus::Rejected,
        ]);

        return back()->with('success', 'История отклонена');
    }
}
