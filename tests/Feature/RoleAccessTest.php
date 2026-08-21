<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_see_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(302);
    }

    public function test_user_cannot_see_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_moderator_cannot_see_admin_dashboard(): void
    {
        $user = User::factory()->moderator()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_see_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(200)
            ->assertViewIs('admin.dashboard');
    }

    public function test_moderator_can_see_moderator_stories(): void
    {
        $user = User::factory()->moderator()->create();

        $response = $this->actingAs($user)->get(route('moderator.stories.index'));
        $response->assertStatus(200)
            ->assertViewIs('moderator.stories.index');
    }
}
