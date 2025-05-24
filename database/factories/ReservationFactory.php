<?php

namespace Database\Factories;

use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_start' => $this->faker->dateTimeBetween('now', '+4 weeks')->format('Y-m-d'),
            'reservation_end' => $this->faker->dateTimeBetween('+4 weeks', '+8 weeks')->format('Y-m-d'),
            'room_id' => Room::all()->random()->id,
            'user_id' => User::all()->random()->id,
        ];
    }
}
