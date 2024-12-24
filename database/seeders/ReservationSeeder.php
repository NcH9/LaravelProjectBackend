<?php

namespace Database\Seeders;

use App\Models\Reservation;
use Database\Factories\ReservationFactory;
use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // ReservationFactory::factory()->count(50)->create();
        for ($i = 0; $i < 50; $i++) { 
            $this->createReservation();
        }
    }
    private function createReservation() {
        $reservationStart = (new DateTime())->modify('+'.rand(1, 30).' days');
        $reservationEnd = (clone $reservationStart)->modify('+'.rand(1, 60).' days');
        $roomId = rand(1, 50);

        $datesCollide = Reservation::
            where('room_id', $roomId)
            ->where('reservation_start', '<', $reservationEnd->format('Y-m-d'))
            ->where('reservation_end', '>', $reservationStart->format('Y-m-d'))
            ->exists();

        if (!$datesCollide) {
            Reservation::create([
                'reservation_start' => $reservationStart->format('Y-m-d'),
                'reservation_end' => $reservationEnd->format('Y-m-d'),
                'room_id' => $roomId,
                'user_id' => rand(1, 6),
            ]);
        }
    }
}
