<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class, // Создание пользователей
            StorySeeder::class, // Создание историй для пользователей
            TagSeeder::class, // Создание тегов для историй
            StoryTagSeeder::class, // Соединение историй и тегов (в промежуточной таблице) внешней связью через story_id, tag_id
        ]);
    }
}
