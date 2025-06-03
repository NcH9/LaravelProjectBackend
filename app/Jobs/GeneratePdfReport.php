<?php

namespace App\Jobs;

use App\Events\ReportGenerated;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use \Illuminate\Support\Facades\Storage;

class GeneratePdfReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public array $data
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $reservations = Reservation::
            where('reservation_start', '>=', $this->data['start_date'])
            ->where('reservation_end', '<=', $this->data['end_date'])
            ->get();

        $pdf = PDF::loadView('reports.reservations', ['reservations' => $reservations]);

        $path = 'reports/reservations_report_' . now()->format('d.m.Y_H_i_s') . '.pdf';
        Storage::disk('public')->put($path, $pdf->output());
        broadcast(new ReportGenerated($path));
    }
}
