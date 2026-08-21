<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Story;
use App\Models\User;
use App\Models\Tag;
use App\Enums\StoryStatus;

class StoryFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_sees_only_approved_stories(): void
    {
        Story::factory()->create();
        Story::factory()->approved()->create();
        Story::factory()->rejected()->create();

        $response = $this->get(route('stories.index'));

        $response->assertStatus(200)
            ->assertViewIs('stories.index');

        $stories = $response->viewData('stories');

        $this->assertCount(1, $stories);
        $this->assertEquals(StoryStatus::Approved, $stories->first()->status);
    }

    public function test_filter_by_user_id(): void
    {
        $author1 = User::factory()->has(Story::factory()->approved()->count(2))->create();
        $author2 = User::factory()->has(Story::factory()->approved()->count(3))->create();

        $response = $this->get(route('stories.index', ['user_id' => $author1->id]));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $this->assertCount(2, $stories);
        $this->assertTrue($stories->every(fn (Story $s): bool => $s->user_id === $author1->id));
    }

    public function test_filter_by_tags(): void
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $story1 = Story::factory()->approved()->create();
        $story1->tags()->attach($tag1->id);

        $story2 = Story::factory()->approved()->create();
        $story2->tags()->attach($tag2->id);

        $story3 = Story::factory()->approved()->create();
        $story3->tags()->attach([$tag1->id, $tag2->id]);

        $response = $this->get(route('stories.index', ['tags' => [$tag1->id]]));
        $response->assertStatus(200);

        $stories = $response->viewData('stories');

        $this->assertCount(2, $stories);
        $this->assertTrue($stories->contains($story1), 'This element doesn\'t exist');
        $this->assertTrue($stories->contains($story3), 'This element doesn\'t exist');
        $this->assertFalse($stories->contains($story2),'This element shouldn’t be here');
    }

    public function test_filter_by_date_from_and_date_to(): void
    {
        $old = Story::factory()->approved()->create(['created_at' => now()->subDays(5)]);
        $middle = Story::factory()->approved()->create(['created_at' => now()->subDays(2)]);
        $new = Story::factory()->approved()->create();

        $response = $this->get(route('stories.index', [
            'date_from' => now()->subDays(3)->format('Y-m-d'),
            'date_to' => now()->format('Y-m-d'),
        ]));

        $response->assertStatus(200);

        $stories = $response->viewData('stories');
        $ids = $stories->pluck('id')->toArray();

        $this->assertContains($middle->id, $ids);
        $this->assertContains($new->id, $ids);
        $this->assertNotContains($old->id, $ids);
    }
}
