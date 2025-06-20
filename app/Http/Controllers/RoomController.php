<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoomIndexRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RoomIndexRequest $request)
    {
        $rooms = Room::with('reservations')->orderBy('floor')->get();
        $this->updateRoomStatus();

        $validated = $request->validated();
        $start = isset($validated['period'][0]) ? $validated['period'][0]['start'] : null;
        $end = isset($validated['period'][0]) ? $validated['period'][0]['end'] : null;

        if ($start !== null && $end !== null) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            foreach ($rooms as $room) {
                $room->calculatedStatus = $room->reservations->filter(function ($reservation) use ($startDate, $endDate) {
                    $reservationStart = Carbon::parse($reservation->reservation_start);
                    $reservationEnd = Carbon::parse($reservation->reservation_end);
                    return
                        $reservationStart->between($startDate, $endDate) ||
                        $reservationEnd->between($startDate, $endDate) ||
                        $startDate->between($reservationStart, $reservationEnd) ||
                        $endDate->between($reservationStart, $reservationEnd);
                })->count() > 0 ? 'Occupied' : 'Available';
            }
        } else {
            foreach ($rooms as $room) {
                $room->calculatedStatus = $room->status === 'occupied' ? 'Occupied' : 'Available';
            }
        }

        foreach ($rooms as $room) {
            $room->pricePerWeek = round(($room->price * 7 * 0.9), 2);
        }

        $groupedRooms = $rooms->groupBy('floor');

        if (request()->expectsJson()) {
            return response()->json($groupedRooms);
        }
        return view('rooms.index')->with('groupedRooms', $groupedRooms);
    }
    public static function getAll() {
        $rooms = Room::pluck('id')->toArray();
        return $rooms;
    }
    public static function getFloor($roomId):int {
        $room = Room::find($roomId);
        return $room->floor;
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,occupied',
            'floor' => 'required|integer|min:1|max:5',
            'price' => 'required|numeric|min:0',
        ]);
        $room = Room::create($validated);
        return response()->json($room, 200);
    }
    public function show(Room $room)
    {
        $room->load('status');
        $room->calculatedStatus = $room->status->name === 'occupied' ? 'Occupied' : 'Available';

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager')) {
            $room->load('reservations');
        } else {
            $room->load(['reservations' => function ($query) {
                $query->where('user_id', Auth::user()->id);
            }]);
        }

        if (request()->expectsJson()) {
            return response()->json($room);
        }
        return view('rooms.show')->with('room', $room);
    }
    public function update(Request $request, Room $room)
    {
        //
        $validated = $request->validate([
            'status' => 'sometimes|string',
            'floor' => 'integer|min:1',
        ]);

        $room->update($validated);
        return response()->json($room);
    }
    public function updateRoomStatus() {
        $currentTime = Carbon::now();

        $rooms = Room::with('reservations')->get();

        foreach ($rooms as $room) {
            $isOccupied = $room->reservations->contains(function ($reservations) use ($currentTime) {
                return $currentTime->between($reservations->reservation_start, $reservations->reservation_end);
            });

            $room->status = $isOccupied ? 1 : 2;
            $room->save();
        }

        // return view('rooms.index', ['rooms' => $rooms]);
    }
    public function destroy(Room $room)
    {
        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }
}
