<?php

namespace Tests\Feature;

use App\Models\Story;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StorySortTest extends TestCase
{
    use RefreshDatabase;

    public function test_sort_latest(): void
    {
        $old = Story::factory()->approved()->create([
            'created_at' => now()->subDays(2)
        ]);

        $new = Story::factory()->approved()->create([
            'created_at' => now()
        ]);

        $response = $this->get(route('stories.index', ['sort' => 'latest']));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $this->assertEquals($new->id, $stories->first()->id);
    }

    public function test_sort_oldest(): void
    {
        $old = Story::factory()->approved()->create([
            'created_at' => now()->subDays(2),
        ]);

        $new = Story::factory()->approved()->create([
            'created_at' => now()
        ]);

        $response = $this->get(route('stories.index', ['sort' => 'oldest']));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $this->assertEquals($old->id, $stories->first()->id);
    }

    public function test_sort_title_asc(): void
    {
        Story::factory()->approved()->create(['title' => 'Zebra']);
        Story::factory()->approved()->create(['title' => 'Leon']);
        Story::factory()->approved()->create(['title' => 'Beer']);

        $response = $this->get(route('stories.index', ['sort' => 'title_asc']));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $titles = $stories->pluck('title')->toArray();
        $this->assertEquals(['Beer', 'Leon' ,'Zebra'], $titles);
    }

    public function test_sort_title_desc(): void
    {
        Story::factory()->approved()->create(['title' => 'Apple']);
        Story::factory()->approved()->create(['title' => 'Banana']);
        Story::factory()->approved()->create(['title' => 'Cherry']);

        $response = $this->get(route('stories.index', ['sort' => 'title_desc']));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $titles = $stories->pluck('title')->toArray();
        $this->assertEquals(['Cherry', 'Banana', 'Apple'], $titles);
    }

    public function test_sort_author_asc(): void
    {
        $user1 = User::factory()->create(['name' => 'John']);
        $user2 = User::factory()->create(['name' => 'Bred']);

        Story::factory()->approved()->create(['user_id' => $user1->id]);
        Story::factory()->approved()->create(['user_id' => $user2->id]);

        $response = $this->get(route('stories.index', ['sort' => 'author_asc']));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $authors = $stories->pluck('user.name')->toArray();
        $this->assertEquals(['Bred', 'John'], $authors);
    }

    public function test_sort_author_desc(): void
    {
        $user1 = User::factory()->create(['name' => 'Alex']);
        $user2 = User::factory()->create(['name' => 'Bill']);

        Story::factory()->approved()->create(['user_id' => $user1->id]);
        Story::factory()->approved()->create(['user_id' => $user2->id]);

        $response = $this->get(route('stories.index', ['sort' => 'author_desc']));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $authors = $stories->pluck('user.name')->toArray();
        $this->assertEquals(['Bill', 'Alex'], $authors);
    }
}
