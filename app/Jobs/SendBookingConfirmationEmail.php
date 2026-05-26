<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Mail\BookingConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendBookingConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $booking;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var array
     */
    public $backoff = [10, 30, 60];

    /**
     * Create a new job instance.
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (!$this->booking) {
            Log::error("SendBookingConfirmationEmail failed: Booking model is null.");
            return;
        }

        $user = $this->booking->user;
        if (!$user || !$user->email) {
            Log::error("SendBookingConfirmationEmail failed: User or user email is missing for booking ID: {$this->booking->id}");
            return;
        }

        Mail::to($user->email)->send(new BookingConfirmationMail($this->booking));
        
        Log::info("Booking confirmation email sent successfully to member {$user->email} for booking ID: {$this->booking->id}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $bookingId = $this->booking ? $this->booking->id : 'Unknown';
        $userEmail = ($this->booking && $this->booking->user) ? $this->booking->user->email : 'Unknown Member';

        Log::error("Booking confirmation email job failed. Booking ID: {$bookingId}. Member: {$userEmail}. Error: {$exception->getMessage()}", [
            'exception' => $exception
        ]);
    }
}
