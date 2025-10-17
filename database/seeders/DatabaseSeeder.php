<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'test_user',
            'email' => 'test@example.com',
        ]);

        User::factory()->count(10)->create()->each(function ($user) {
            Follower::factory()->count(2)->create([
                'following_id' => $user->id
            ]);
        });;
    }
}
