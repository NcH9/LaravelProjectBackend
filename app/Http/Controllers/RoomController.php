<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with('status')->orderBy('floor')->get();
        $groupedRooms = $rooms->groupBy('floor');
        $this->updateRoomStatus();
        // dd($groupedRooms);
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
        $floor = Room::where('id', $roomId)->value('floor');
        return $floor;
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
    public function show(int $id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room, 200);
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
