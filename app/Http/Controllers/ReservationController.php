<?php

namespace App\Http\Controllers;

use App\Jobs\GeneratePdfReport;
use App\Jobs\SendNewReservationsInfo;
use App\Jobs\SendUpdateReservationsInfo;
use App\Mail\ReservationCreated;
use App\Mail\ReservationUpdated;
use Auth;
use DateTime;
use Gate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Mail;
use Storage;
use Validator;
use View;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!$request->expectsJson() && !Auth::check()) {

            return redirect()->route('login');
        }

        $sortBy = $request->input('sort_by', 'id');
        $direction = $request->input('direction', 'asc');
        $allowedSorts = ['id', 'reservation_start', 'reservation_end', 'room_id', 'user_id'];

        if (!in_array($sortBy, $allowedSorts)) {
            $sortBy = 'id';
        }
        if ($direction != 'asc' && $direction != 'desc') {
            $direction = 'asc';
        }

        if (Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager')) {
            $reservations = Reservation::with('user')->orderBy($sortBy, $direction)->paginate(10);
            
        } else {
            $reservations = Reservation::with('user')
                ->where('user_id', Auth::user()->id)
                ->orderBy($sortBy, $direction)
                ->paginate(10);
        }
        // $reservations = Reservation::all();
        if ($request->expectsJson()) {

            return response()->json([
                'data' => $reservations->items(),
                'meta' => [
                    'current_page' => $reservations->currentPage(),
                    'total_pages' => $reservations->lastPage(),
                    'total_items' => $reservations->total(),
                ],
                'links' => [
                    'next' => $reservations->nextPageUrl(),
                    'prev' => $reservations->previousPageUrl(),
                ],
                'sort' => [
                    'sort_by' => $sortBy,
                    'direction' => $direction,
                ],
            ]);
        }
        return view('reservations.index')->with('reservations', $reservations);
    }
    public function generateReport(Request $request) {
        if (Gate::denies('is-manager-or-admin')) {
            abort(403);
        }
        $validated = $request->validate([
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date|before_or_equal:today',
        ]);
        GeneratePdfReport::dispatch($validated);
        return response()->json(['message' => 'Report is being generated']);
    }
    public function showReports(Request $request) {
        if (Gate::denies('is-manager-or-admin')) {
            abort(403);
        }
        $request->validate([
            'term' => 'nullable|string|max:255',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('term');
        $allReports = Storage::disk('public')->files('reports');
        $reportLinks = array_map(function ($file) {
            $fileName = basename($file);
        
            preg_match('/(\d{2}\.\d{2}\.\d{4}_\d{2}:\d{2}:\d{2})/', $fileName, $matches);
            // dd($matches[1]);
            $createdAt = isset($matches[1])
                ? \Carbon\Carbon::createFromFormat('d.m.Y_H:i:s', $matches[1])
                : null;
        
            return [
                'name' => $fileName,
                'url' => Storage::url($file),
                'createdAt' => $createdAt,
            ];
        }, $allReports);
        // dd($reportLinks);
        // if ($reportLinks == null) {
        //     return view('reports.list')->with('reports', []);
        // }
        if ($dateFrom != null && $dateTo != null) {
            $reportLinks = array_filter($reportLinks, function ($report) use ($dateFrom, $dateTo) {
                $createdAt = $report['createdAt'];
        
                if ($dateFrom && $createdAt->lt(\Carbon\Carbon::parse($dateFrom))) {
                    return false;
                }

                if ($dateTo && $createdAt->gt(\Carbon\Carbon::parse($dateTo))) {
                    return false;
                }
        
                return true;
            });
        }
        
        if ($search == null) {
            return view('reports.list')->with('reports', array_reverse($reportLinks));
        }

        $reports = array_filter(array_reverse($reportLinks), function ($report) use ($search) {
            return str_contains(strtolower($report['name']), strtolower($search)) !== false;
        });

        return view('reports.list')->with('reports', $reports);
    }
    public function create() {
        if (!Auth::check()) {
            return redirect()->route('login');
            // return response()->json(['error' => 'User is not authenticated'], 401);
        }

        return view('reservations.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {

            return redirect()->route('login');
            // return response()->json(['error' => 'User is not authenticated'], 401);
        }
        $data = $request->all();
        $data['user_id'] = Auth::id();

        if (!isset($data['room_id']) || $data['room_id'] == 0) {
            if (isset($data['reservation_start']) && isset($data['reservation_end'])) {
                $data['room_id'] = $this->findRoom($data['reservation_start'], $data['reservation_end']);
            } else {
                $data['room_id'] = 1;
            }
        }

        $validated = Validator::make($data, [
            'reservation_start' => 'required|date|after_or_equal:today',
            'reservation_end' => 'required|date|after_or_equal:reservation_start',
            'room_id' => 'required|integer|exists:rooms,id',
            'user_id' => 'required|integer|exists:users,id',
            'price' => 'nullable|numeric',
        ])->validate();
        $validated['price'] = $this->countPrice(
            $validated['room_id'], 
            $validated['reservation_start'], 
            $validated['reservation_end'])['price'];
        if (!$this->checkAvailability($validated['room_id'], $validated['reservation_start'], $validated['reservation_end'])) {
            return response()->json(['error' => 'Room is unavailable for booking for this period'], 400);
        }

        $reservation = Reservation::create($validated);
        SendNewReservationsInfo::dispatch($reservation);
        if (request()->expectsJson()) {
            return response()->json($reservation, 201);
        }
        return response()->json($reservation, 200);
    }
    private function findRoom($startDate, $endDate) {

        $reservations = Reservation::
            where('reservation_end', '>=', $startDate)
            ->where('reservation_start', '<=', $endDate)
            ->pluck('room_id')->toArray();
        $rooms = RoomController::getAll();
        $suitableRooms = array_values(array_diff($rooms, $reservations));

        return isset($suitableRooms[0]) ? $suitableRooms[0] : 1;
    }
    private function checkAvailability(int $roomId, $startDate, $endDate):bool {
        $existingReservations = [];
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
    public function confirm(Request $request) {
        
        if (!Auth::check()) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }
        $data = $request->all();
        $data['user_id'] = Auth::id();

        if (!isset($data['room_id']) || $data['room_id'] == 0) {
            if (isset($data['reservation_start']) && isset($data['reservation_end'])) {
                $data['room_id'] = $this->findRoom($data['reservation_start'], $data['reservation_end']);
            }
            else {
                $data['room_id'] = 1;
            }
        }

        $validated = Validator::make($data, [
            'reservation_start' => 'required|date|after_or_equal:today',
            'reservation_end' => 'required|date|after_or_equal:reservation_start',
            'room_id' => 'required|integer|exists:rooms,id',
            'user_id' => 'required|integer|exists:users,id',
        ])->validate();

        if (!$this->checkAvailability($validated['room_id'], $validated['reservation_start'], $validated['reservation_end'])) {
            return response()->json(['error' => 'Room is unavailable for booking for this period'], 400);
        }

        $additionalData = $this->countPrice(
            $validated['room_id'],
            $validated['reservation_start'],
            $validated['reservation_end']
        );

        $data = [
            'room_id' => $validated['room_id'],
            'reservation_start' => $validated['reservation_start'],
            'reservation_end' => $validated['reservation_end'],
            'floor' => $additionalData['floor'],
            'days_amount' => $additionalData['days_amount'],
            'price' => $additionalData['price'],
        ];
        if (request()->expectsJson()) {
            return response()->json($data);
        }
        return view('reservations.confirm', $data);
    }
    private function countPrice($room_id, $reservation_start, $reservation_end) {
        $defaultPrice = 500;
        $floor = RoomController::getFloor($room_id);

        $startDate = new DateTime($reservation_start);
        $endDate = new DateTime($reservation_end);
        $daysAmount = $startDate->diff($endDate)->days;

        $startDate = $startDate->format('Y-m-d');
        $endDate = $endDate->format('Y-m-d');

        // mp - multiplier
        $daysMp = $daysAmount >= 14 ? 0.9 : 1;
        $floorMp = ($floor - 1) * 0.25;

        $price = ($defaultPrice * $daysAmount) * (1 + $floorMp) * $daysMp;
        if ($price == 0) {
            $price = 500;
        }

        return [
            'floor' => $floor,
            'price' => $price, 
            'days_amount' => $daysAmount
        ];
    }

    public function showRequest(Request $request) {
        return dump($request);
    }
    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $reservation = Reservation::with('user')->find($id);
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }
        if (request()->expectsJson()) {
            return response()->json($reservation);
        }
        return view('reservations.show')->with('reservation', $reservation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function confirmUpdate(Request $request, Reservation $reservation)
    {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }

        $validated = $request->validate([
            'reservation_start' => 'required|date|after_or_equal:today',
            'reservation_end' => 'required|date|after_or_equal:reservation_start',
            'room_id' => 'required|integer|exists:rooms,id',
            'price' => 'nullable|numeric',
        ]);

        if (!$this->isRedactable(
            $reservation->id,
            $validated['room_id'],
            $validated['reservation_start'],
            $validated['reservation_end']
        )) {

            return response()->json(['error' => 'Room is unavailable for booking for this period'], 400);
        }
        $additionalData = $this->countPrice(
            $validated['room_id'],
            $validated['reservation_start'],
            $validated['reservation_end']
        );
        $validated['id'] = $reservation->id;
        $validated['price'] = $additionalData['price'];
        $validated['floor'] = $additionalData['floor'];
        $validated['days_amount'] = $additionalData['days_amount'];

        unset($validated['reservation_id']);

        if (request()->expectsJson()) {
            return response()->json($validated);
        }
        return view('reservations.confirmUpdate')->with('reservation', $validated);
    }
    public function update(Request $request, Reservation $reservation)
    {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }

        $validated = $request->validate([
            'reservation_start' => 'required|date|after_or_equal:today',
            'reservation_end' => 'required|date|after_or_equal:reservation_start',
            'room_id' => 'required|integer|exists:rooms,id',
            'price' => 'nullable|numeric',
        ]);
        if (!isset($validated['price']) || $validated['price'] == 0) {
            $additionalData = $this->countPrice(
                $validated['room_id'],
                $validated['reservation_start'],
                $validated['reservation_end']
            );
            $validated['price'] = $additionalData['price'];
            $validated['floor'] = $additionalData['floor'];
            $validated['days_amount'] = $additionalData['days_amount'];
        }

        if (!$this->isRedactable(
            $reservation->id,
            $validated['room_id'],
            $validated['reservation_start'],
            $validated['reservation_end']
        )) {
            return response()->json(['error' => 'Room is unavailable for booking for this period'], 400);
        }

        unset($validated['reservation_id']);
        $reservation->update($validated);
        SendUpdateReservationsInfo::dispatch($reservation);
        if (request()->expectsJson()) {
            return response()->json($reservation);
        }
        return view('reservations.show')->with('reservation', $reservation);
    }
    public function edit(Reservation $reservation) {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }

        return view('reservations.update')->with('reservation', $reservation);
    }
    private function isRedactable(int $reservationId, int $roomId, $startDate, $endDate) {

        $existingReservations = Reservation::where('room_id', $roomId)
        ->where('id', '!=', $reservationId)
        ->get();

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
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }
        $reservation->delete();
        // return response()->json(['message' => 'Reservation deleted successfully']);

        return redirect()->route('reservations.index');
    }
}
