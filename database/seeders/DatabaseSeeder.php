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
        User::create([
            'name'     => 'Super Admin',
            'email'    => 'admin@anim24.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Arthuradmin@anim24'),
            'role'     => 'admin',
        ]);

        User::create([
            'name'     => 'Writer Jane',
            'email'    => 'author@anim24.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Arthurauthor@anim24'),
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