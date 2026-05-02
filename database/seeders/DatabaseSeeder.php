<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'testUser1',
            'email' => 'testUser1@gmail.com',
        ]);
        User::factory()->create([
            'name' => 'testUser2',
            'email' => 'testUser2@gmail.com',
        ]);
        User::factory()->create([
            'name' => 'testUser3',
            'email' => 'testUser3@gmail.com',
        ]);
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
