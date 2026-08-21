<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Story;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_correct_counts(): void
    {
        $admin = User::factory()->admin()->create();
        $users = User::factory()->count(3)->create();

        User::factory()->moderator()->count(2)->create(); // moderators

        foreach ($users as $user) {
            Story::factory()
                ->approved()
                ->for($user, 'user')
                ->create();
        }

//        Story::factory()->approved()->recycle($users)->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertViewIs('admin.dashboard');

        $usersCount = $response->viewData('usersCount');
        $storyCount = $response->viewData('storyCount');

        $this->assertEquals(6, $usersCount['total']);
        $this->assertEquals(3, $usersCount['user']);
        $this->assertEquals(2, $usersCount['moderator']);
        $this->assertEquals(1, $usersCount['admin']);

        $this->assertEquals(3, $storyCount['total']);
    }

    public function test_dashboard_shows_latest_users_and_stories(): void
    {
        $admin = User::factory()->admin()->create();
        $users = User::factory()->count(7)->create();

        foreach ($users as $user) {
            Story::factory()
                ->approved()
                ->for($user, 'user')
                ->create();
        }

        $response = $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertStatus(200);

        $latestUsers = $response->viewData('latestUsers');
        $latestStories = $response->viewData('latestStories');

        $this->assertCount(5, $latestUsers);
        $this->assertCount(5, $latestStories);
    }
}
