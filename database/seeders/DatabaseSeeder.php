<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Room;
use App\Models\Reservation;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Room::factory(50)->create();
        Reservation::factory(10)->create();
    }
}
