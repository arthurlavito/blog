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
        // 1. Create System Users
        // Using updateOrCreate ensures you can run this multiple times without duplicates
        User::updateOrCreate(
            ['email' => 'admin@anim24.com'],
            [
                'name' => 'Super Admin',
                'email_verified_at' => now(),
                'password' => Hash::make('Arthuradmin@anim24.com'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'author@anim24.com'],
            [
                'name' => 'Lead Reporter',
                'email_verified_at' => now(),
                'password' => Hash::make('Arthurauthor@anim24.com'),
                'role' => 'author',
            ]
        );

        // 2. Global News Categories
        // Added 'Security' and 'Politics' for your Nigerian news focus
        $categories = [
            ['name' => 'Global News', 'color' => '#1e3a8a'],
            ['name' => 'Security & Politics', 'color' => '#b91c1c'], // Red for urgent news
            ['name' => 'Reviews', 'color' => '#4338ca'],
            ['name' => 'Movies & Anime', 'color' => '#6d28d9'],
            ['name' => 'Manga', 'color' => '#0369a1'],
            ['name' => 'Trailers', 'color' => '#0f172a'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'color' => $cat['color'],
                ]
            );
        }

        // 3. Hand off Post creation to the PostSeeder
        // Note: Ensure PostSeeder does NOT use the fake() helper if on production
        $this->call([
            PostSeeder::class,
        ]);
    }
}