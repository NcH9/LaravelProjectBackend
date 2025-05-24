<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rooms = Room::with('reservations')->orderBy('floor')->get();
        $this->updateRoomStatus();

        $start = $request->input('start', '');
        $end = $request->input('end', '');

        if ($start !== '' && $end !== '') {
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
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'status_id' => 'required|integer',
            'floor' => 'required|integer|min:1|max:10',
        ]);
        $room = Room::create($validated);
        return response()->json($room, 200);
    }

    /**
     * Display the specified resource.
     */
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Room $room)
    {
        //
        $validated = $request->validate([
            'status_id' => 'integer',
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

            $room->status_id = $isOccupied ? 1 : 2;
            $room->save();
        }

        // return view('rooms.index', ['rooms' => $rooms]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Room $room)
    {
        //
        $room->delete();
        return response()->json(['message' => 'Room deleted successfully']);
    }
}
