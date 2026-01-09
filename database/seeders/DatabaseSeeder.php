<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        User::factory()->create([
            'name'     => 'Super Admin',
            'email'    => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        User::factory()->create([
            'name'     => 'Writer Jane',
            'email'    => 'author@test.com',
            'password' => Hash::make('author123'),
            'role'     => 'author',
        ]);

        // 2. Create Categories
        collect(['News', 'Reviews', 'Manga', 'Movies', 'Trailers'])->each(function ($name) {
            Category::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'color' => '#4B0082',
            ]);
        });

        // 3. Hand off Post creation to the PostSeeder
        $this->call([
            PostSeeder::class,
        ]);
    }
}