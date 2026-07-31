<?php

namespace Database\Seeders;

use App\Models\Story;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoryTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
         *  К каждой истории мы прикрепляем от 1 до 3 - тэг(метку),
         *  метод attach(), автоматически определяет промежуточную таблицу и сводные поля, такие как story_id, tag_id
         *  и заполняет их.
         */

        $stories = Story::all();
        $tags = Tag::all();

        foreach ($stories as $story) {
            $story->tags()->attach(
                $tags->random(rand(1, 3))->pluck('id')->all()
            );
        }
    }
}
