<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\ReservationRequest;
use App\Http\Requests\Reservations\ReservationConfirmRequest;
use App\Http\Requests\Reservations\ReservationIndexRequest;
use App\Http\Requests\Reservations\ReservationsShowReports;
use App\Http\Requests\Reservations\ReservationStoreRequest;
use App\Jobs\GeneratePdfReport;
use App\Jobs\SendNewReservationsInfo;
use App\Jobs\SendUpdateReservationsInfo;
use App\Mail\ReservationCreated;
use App\Mail\ReservationUpdated;
use App\Services\ReservationService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Gate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Mail;
use Storage;
use Validator;
use View;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class ReservationController extends Controller
{
    public function __construct(
        protected ReservationService $reservationService,
    ) {}
    /**
     * Display a listing of the resource.
     */
    public function index(ReservationIndexRequest $request):mixed
    {
        if (!$request->expectsJson() && !Auth::check()) {

            return redirect()->route('login');
        }
        $data = $request->validated();
        $user = Auth::user();
        $userId = Auth::id();

        $reservations = $user->hasRole(RoleEnum::ADMIN) || $user->hasRole(RoleEnum::MANAGER)
            ? $this->reservationService->getAllReservations($data)
            : $this->reservationService->getReservationsForUser($userId, $data);

        return $request->expectsJson()
            ? response()->json([
                'data' => $reservations->items(),
                'meta' => Arr::only($reservations->items(), [
                    'current_page', 'last_page', 'total', 'per_page', 'next_page_url', 'prev_page_url',
                ])
            ])
            : view('reservations.index')->with('reservations', $reservations);
    }
    public function generateReport(ReservationStoreRequest $request) {
        if (Gate::denies('is-manager-or-admin')) {
            abort(403);
        }
        $validated = $request->validated();

        if ($request->expectsJson()) {
            GeneratePdfReport::dispatch($validated);
            return response()->json(['message' => 'Report is being generated']);
        }

        $reservations = $this->reservationService->getReservationsBetweenDates($validated['reservation_start'], $validated['reservation_end']);

        $pdf = PDF::loadView('reports.reservations', ['reservations' => $reservations]);

        $path = 'reports/reservations_report_' . now()->format('d.m.Y_H:i:s') . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());

        $allReports = Storage::disk('public')->files('reports');
        return view('reports.list')->with('reports', $allReports);
    }
    public function showReports(ReservationsShowReports $request) {
        if (Gate::denies('is-manager-or-admin')) {
            abort(403);
        }

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $search = $request->input('term');
        $allReports = Storage::disk('public')->files('reports');
        $reportLinks = array_map(function ($file) {
            $fileName = basename($file);

            preg_match('/(\d{2}\.\d{2}\.\d{4}_\d{2}:\d{2}:\d{2})/', $fileName, $matches);
            $createdAt = isset($matches[1])
                ? \Carbon\Carbon::createFromFormat('d.m.Y_H:i:s', $matches[1])
                : null;

            return [
                'name' => $fileName,
                'url' => Storage::url($file),
                'createdAt' => $createdAt,
            ];
        }, $allReports);
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
        return view('reservations.create');
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(ReservationStoreRequest $request)
    {
        $data = $this->reservationService->prepareData($request->validated());

        if (!$this->reservationService->checkAvailability($data)) {
            return request()->expectsJson()
                ? response()->json(['error' => 'Room is unavailable for booking for this period'], 400)
                : view('reservations.unavailable');
        }

        $reservation = $this->reservationService->createReservation($data);
        SendNewReservationsInfo::dispatch($reservation);

        return request()->expectsJson()
            ? response()->json($reservation, 201)
            : view('reservations.show')->with('reservation', $reservation);
    }

    public function confirm(ReservationConfirmRequest $request) {
        $data = $this->reservationService->prepareData($request->validated());

        if (!$this->reservationService->checkAvailability($data)) {
            return request()->expectsJson()
                ? response()->json(['error' => 'Room is unavailable for booking for this period'], 400)
                : view('reservations.unavailable');
        }

        $data['floor'] = $this->reservationService->getFloor($data['room_id']);
        $data['days_amount'] = $this->reservationService->getDaysAmount($data['reservation_start'], $data['reservation_end']);

        return request()->expectsJson()
            ? response()->json($data)
            : view('reservations.confirm')->with('reservation', $data);
    }


    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }

        return request()->expectsJson()
            ? response()->json($reservation)
            : view('reservations.show')->with('reservation', $reservation);
    }

    /**
     * Update the specified resource in storage.
     */
    public function confirmUpdate(ReservationRequest $request, Reservation $reservation)
    {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }

        $data = $this->reservationService->prepareDataForUpdate($request->validated());

        if (!$this->reservationService->isRedactable($reservation->id, $data)) {
            return request()->expectsJson()
                ? response()->json(['error' => 'Room is unavailable for booking for this period'], 400)
                : view('reservations.unavailable');
        }

        return request()->expectsJson()
            ? response()->json($data)
            : view('reservations.confirmUpdate')->with('reservation', $data);
    }
    public function update(ReservationStoreRequest $request, Reservation $reservation)
    {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }

        $data = $this->reservationService->prepareDataForUpdate($request->validated());

        if (!$this->reservationService->isRedactable($reservation->id, $data)) {
            return request()->expectsJson()
                ? response()->json(['error' => 'Room is unavailable for booking for this period'], 400)
                : view('reservations.unavailable');
        }

        $reservation->update($data);
        SendUpdateReservationsInfo::dispatch($reservation);

        return request()->expectsJson()
            ? response()->json($reservation)
            : view('reservations.show')->with('reservation', $reservation);
    }
    public function edit(Reservation $reservation) {
        if (Gate::denies('show-and-redact-reservation', $reservation)) {
            abort(403);
        }

        return view('reservations.update')->with('reservation', $reservation);
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

        return request()->expectsJson()
            ? response()->json(['message' => 'Reservation deleted successfully'])
            : redirect()->route('reservations.index');
    }
}
