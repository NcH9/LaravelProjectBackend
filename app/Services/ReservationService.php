<?php

namespace App\Services;

use App\Http\Controllers\RoomController;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use DateTime;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class ReservationService
{
    public function getAllReservations(array $data):LengthAwarePaginator {
        $perPage = $data['perPage'] ?? 10;
        return Reservation::with('user')->with('order')->with('room')->paginate($perPage);
    }
    public function getReservationsForUser(int $userId, array $data):LengthAwarePaginator {
        $perPage = $data['perPage'] ?? 10;
        return Reservation::with('user')->with('order')->where('user_id', $userId)->with('room')->paginate($perPage);
    }
    public function findRoom($startDate, $endDate):Room|int {
        $reservations = Reservation::
            where('reservation_end', '>=', $startDate)
            ->where('reservation_start', '<=', $endDate)
            ->pluck('room_id')->toArray();
        $rooms = RoomController::getAll();
        $suitableRooms = array_values(array_diff($rooms, $reservations));

        return $suitableRooms[0] ?? 1;
    }
    public function countPrice(int $roomId, string $reservationStart, string $reservationEnd):float {

        $defaultPrice = Room::find($roomId)->price;

        $daysAmount = $this->getDaysAmount($reservationStart, $reservationEnd);

        $daysMultiplier = $daysAmount >= 7 ? 0.9 : 1;

        $price = ($defaultPrice * $daysAmount) * $daysMultiplier;
        if ($price == 0) {
            $price = 500;
        }

        return $price;
    }
    public function getFloor($room_id):int {
        return RoomController::getFloor($room_id);
    }
    public function getDaysAmount(string $reservationStart, string $reservationEnd):int {
        $startDate = new DateTime($reservationStart);
        $endDate = new DateTime($reservationEnd);

        return $startDate->diff($endDate)->days;
    }
    public function getRoomNumber(int $roomId) {
        return Room::find($roomId)?->number;
    }
    public function prepareData(array $data):array {
        $data['user_id'] = Auth::id();

        $data['room_id'] = !isset($data['room_number']) || $data['room_number'] === 0
            ? $this->findRoom($data['reservation_start'], $data['reservation_end'])
            : Room::where('number', $data['room_number'])->first()->id;

        $data['room_number'] = $data['room_number'] === 0 ? Room::find($data['room_id'])->number : $data['room_number'];

        $data['price'] = $this->countPrice(
            $data['room_id'],
            $data['reservation_start'],
            $data['reservation_end']
        );

        return $data;
    }
    public function createReservation($data):Reservation {
        return Reservation::create($data);
    }

    public function checkAvailability(array $data):bool {
        $roomId = $data['room_id'];
        $startDate = $data['reservation_start'];
        $endDate = $data['reservation_end'];

        $existingReservations = Reservation::where('room_id', $roomId)->get();

        if (count($existingReservations) == 0) {
            return true;
        }

        foreach($existingReservations as $reservation) {
            if ($startDate < $reservation->reservation_end && $endDate > $reservation->reservation_start) {
                return false;
            }
        }

        return true;
    }

    public function isRedactable(int $reservationId, array $data):bool {
        $roomId = $data['room_id'];
        $startDate = $data['reservation_start'];
        $endDate = $data['reservation_end'];

        $existingReservations = Reservation::where('room_id', $roomId)->where('id', '!=', $reservationId)->get();

        if ($existingReservations->isEmpty()) {
            return true;
        }

        foreach($existingReservations as $existingReservation) {
            if ($startDate < $existingReservation->reservation_end && $endDate > $existingReservation->reservation_start) {
                return false;
            }
        }

        return true;
    }

    public function getReservationsBetweenDates(string $startDate, string $endDate):array {
        return Reservation::where('reservation_start', '>=', $startDate)->where('reservation_end', '<=', $endDate)->get();
    }

    public function prepareDataForUpdate(array $data):array {
        $room = Room::where('number', $data['room_number'])->first();
        $data['room_id'] = $room->id;
        $data['price'] = $this->countPrice($room->id, $data['reservation_start'], $data['reservation_end']);
        $data['floor'] = $room->floor;
        $data['days_amount'] = $this->getDaysAmount($data['reservation_start'], $data['reservation_end']);

        return $data;
    }
}
