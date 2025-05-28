<?php

namespace Database\Seeders;

use App\Enums\RoomStatusEnum;
use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\factories\RoomFactory;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        RoomFactory::factory()->count(50)->create();
        for ($i = 0; $i < 50; $i++) {
            $floor = floor($i/10)+1;
            $roomsOnFloor = Room::where('floor', $floor)->count();
            $number = $floor.$roomsOnFloor;

            Room::create([
                'status' => RoomStatusEnum::AVAILABLE,
                'floor' => $floor,
                'number' => $number,
            ]);
        }
    }
}
