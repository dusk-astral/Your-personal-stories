<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoryFilterRequest;
use App\Http\Requests\StoryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Story;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Models\Tag;
use App\Enums\StoryStatus;
use Illuminate\Support\Facades\Gate;
use App\Enums\UserRole;
use App\Models\User;

class StoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(StoryFilterRequest $request): View
    {
        $stories = Story::query()
            ->where('status', StoryStatus::Approved)
            ->filter($request->safe()->only(['user_id', 'date_from', 'date_to', 'tags']))
            ->sort($request->validated('sort', 'latest'))
            ->with(['user', 'tags'])
            ->simplePaginate(5)
            ->withQueryString();

        $users = User::query()->orderBy('name')->get(['id', 'name']);
        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);

        $filtersApplied = $request->anyFilled(['user_id', 'date_from', 'date_to', 'tags']);

        return view('stories.index', compact('stories', 'users', 'filtersApplied', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        Gate::authorize('create', Story::class);

        $tags = Tag::all();

        return view('stories.create', compact('tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoryRequest $request): RedirectResponse
    {
        Gate::authorize('create', Story::class);

        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated) {

            $story = Story::query()->create([
                'user_id' => auth()->id(),
                'title' => $validated['title'],
                'body' => $validated['body'],
                'status' => StoryStatus::Pending,
                'image_path' => null
            ]);

            if ($request->hasFile('image_path')) {
                $extension = $story->id . '.' . $request->file('image_path')->extension();

                $path = $request->file('image_path')->storeAs('story_images', $extension, 'public');

                $story->update([
                    'image_path' => $path
                ]);
            }

            $story->tags()->attach($validated['tags']);
        });

        return redirect()->route('stories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Story $story): View
    {
        Gate::authorize('view', $story);

        $story->load(['user', 'tags']); // жадная пост-загрузка, после получения экземпляра родительской модели

        return view('stories.show', compact('story'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Story $story): View
    {
        Gate::authorize('update', $story);

        $tags = Tag::all();

        return view('stories.edit', compact('story', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoryRequest $request, Story $story): RedirectResponse
    {
        Gate::authorize('update', $story);

        $validated = $request->validated();

        if ($request->hasFile('image_path')) {
            $oldImage = $story->image_path;

            if ($story->image_path && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }

            $extension = $story->id . '.' . $request->file('image_path')->extension();
            $path = $request->file('image_path')->storeAs('story_images', $extension, 'public');
            $validated['image_path'] = $path;
        }

        $story->update([
            'title' => $validated['title'],
            'body' => $validated['body'],
            'status' => StoryStatus::Pending,
            'image_path' => $validated['image_path'] ?? null
        ]);

        $story->tags()->sync($validated['tags']);

        return match($request->user()->role) {
            UserRole::Moderator => redirect()->route('moderator.stories.index'),
            UserRole::Admin => redirect()->route('admin.stories.index'),
            default => redirect()->route('stories.index'),
        };

//        return redirect()->route('stories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Story $story): RedirectResponse
    {
        Gate::authorize('delete', $story);

        $story->delete();

        return back();
    }

    public function myStories(StoryFilterRequest $request): View
    {
        $stories = Story::query()
            ->where('user_id', auth()->id())
            ->filter($request->safe()->only(['date_from', 'date_to', 'tags', 'status']))
            ->sort($request->validated('sort', 'latest'))
            ->with(['user', 'tags', 'lastRejectionModeration.moderator'])
            ->latest()
            ->simplePaginate(5)
            ->withQueryString();

        $tags = Tag::query()->orderBy('name')->get(['id', 'name']);
        $filtersApplied = $request->anyFilled(['date_from', 'date_to', 'tags', 'status']);

        return view('stories.my-stories', compact('stories', 'tags', 'filtersApplied'));
    }
}
