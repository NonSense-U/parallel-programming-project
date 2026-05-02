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
        $index = 1;
         while ($index <= 100) {
            User::factory()->create([
                'name' => "testUser$index",
                'email' => "testUser$index@gmail.com",
            ]);

            $index++;
        }

        $this->call([
            ProductSeeder::class,
        ]);
    }
}
