<?php

namespace Tests\Feature;

use App\Enums\StoryStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tag;
use App\Models\Story;

class StoryFilterRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_filter_parameters(): void
    {
        $user = User::factory()->create();

        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $response = $this->get(route('stories.index', [
            'user_id' => $user->id,
            'date_from' => '2019-01-01',
            'date_to' => '2019-02-01',
            'tags' => [$tag1->id, $tag2->id],
            'sort' => 'latest'
            ]));

        $response->assertStatus(200);
    }

    public function test_invalid_status_value_redirects_back_with_errors(): void
    {
        $response = $this->from(route('stories.index'))
            ->get(route('stories.index', [
                'status' => 'invalid_status',
            ]));

        $response
            ->assertRedirect(route('stories.index'))
            ->assertSessionHasErrors(['status']);
    }

    public function test_invalid_sort_value_redirects_back_with_errors(): void
    {
        $response = $this->from(route('stories.index'))
            ->get(route('stories.index', [
                'sort' => 'invalid_sort'
            ]));

        $response->assertRedirect(route('stories.index'))
            ->assertSessionHasErrors(['sort']);
    }

    public function test_invalid_user_id_value_redirects_back_with_errors(): void
    {
        $response = $this->from(route('stories.index'))
            ->get(route('stories.index', [
                'user_id' => 999999,
            ]));

        $response->assertRedirect(route('stories.index'))
            ->assertSessionHasErrors(['user_id']);
    }
}
