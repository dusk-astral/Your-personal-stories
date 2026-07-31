<?php

namespace Database\Factories;

use App\Models\Story;
use App\Models\User;
use App\Enums\StoryStatus;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends Factory<Story>
 */
class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(6),
            'body' => fake()->paragraph(3),
            'status' => StoryStatus::Pending,
            'rejected_reason' => null,
            'moderated_by' => null,
            'published_at' => null
        ];
    }

    /**
     *  States
     */
    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => StoryStatus::Approved,
            'rejected_reason' => null,
            'published_at' => now()
        ]);
    }

    public function rejected(string $reason = 'Не соответствует правилам'): static
    {
        return $this->state(fn (): array => [
            'status' => StoryStatus::Rejected,
            'rejected_reason' => $reason,
            'published_at' => now()
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => StoryStatus::Pending,
            'rejected_reason' => null,
            'published_at' => null
        ]);
    }
}
