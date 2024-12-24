<?php

namespace App\Jobs;

use App\Mail\ReservationUpdated;
use App\Models\Reservation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendUpdateReservationsInfo implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Reservation $reservation
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->reservation->user->email)->send(new ReservationUpdated($this->reservation));
    }
}
