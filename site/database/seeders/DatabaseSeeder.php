<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        User::factory()->count(10)->create();

        // Initial seeder for tags and locale
        $this->call([
            LocaleSeeder::class,
            TagSeeder::class
        ]);
    }
}
