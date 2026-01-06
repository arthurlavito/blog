<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create the Admin
        User::factory()->create([
            'name'     => 'Super Admin',
            'email'    => 'admin@test.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // 2. Create an Author
        User::factory()->create([
            'name'     => 'Writer Jane',
            'email'    => 'author@test.com',
            'password' => Hash::make('author123'),
            'role'     => 'author',
        ]);

        // 3. Create a Reader
        User::factory()->create([
            'name'     => 'Regular Reader',
            'email'    => 'reader@test.com',
            'password' => Hash::make('reader123'),
            'role'     => 'reader',
        ]);

        // 4. Call other seeders (Like your PostSeeder)
        // Ensure you have actually created a PostSeeder file before running this!
        $this->call([
            PostSeeder::class,
        ]);
    }
}