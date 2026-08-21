<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Story;
use App\Models\User;

class StoryIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_see_stories_index(): void
    {
        Story::factory()->count(3)->approved()->create();

        $response = $this->get(route('stories.index'));

        $response->assertStatus(200)
        ->assertViewIs('stories.index')
        ->assertViewHas('stories');

        $stories = $response->viewData('stories');
        $this->assertCount(3, $stories);
    }

    public function test_user_can_see_stories_index(): void
    {
        Story::factory()->count(3)->approved()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('stories.index'));

        $response->assertStatus(200)
        ->assertViewIs('stories.index')
        ->assertViewHasAll(['stories', 'users','filtersApplied', 'tags']);

        $stories = $response->viewData('stories');
        $this->assertCount(3, $stories);
    }

    public function test_stories_are_paginated(): void
    {
        Story::factory()->count(12)->approved()->create();

        $response = $this->get(route('stories.index'));

        $response->assertStatus(200)
        ->assertViewHas('stories');

        $stories = $response->viewData('stories');
        $this->assertCount(5, $stories);
    }
}
