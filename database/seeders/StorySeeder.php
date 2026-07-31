<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

use App\Models\Story;

class StorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all(); // Это коллекция юзеров, для связи с историями методом recycle

        Story::factory()->count(4)->recycle($users)->create();
        Story::factory()->count(8)->approved()->recycle($users)->create();
        Story::factory()->count(1)->rejected('Спам и реклама')->recycle($users)->create();
        Story::factory()->count(1)->rejected()->recycle($users)->create();
    }
}
