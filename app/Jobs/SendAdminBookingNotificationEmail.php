<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\User;
use App\Mail\AdminBookingNotificationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendAdminBookingNotificationEmail implements ShouldQueue
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
            Log::error("SendAdminBookingNotificationEmail failed: Booking model is null.");
            return;
        }

        // Ensure timeslot relation exists
        $timeslot = $this->booking->timeslot;
        if (!$timeslot) {
            Log::error("SendAdminBookingNotificationEmail failed: Timeslot relation is missing for booking ID: {$this->booking->id}");
            return;
        }

        $gymId = $this->booking->gym_id ?? $timeslot->gym_id;
        $adminUser = User::where('gym_id', $gymId)->where('role', 'admin')->first();

        try {
            if ($adminUser && $adminUser->email) {
                Mail::to($adminUser->email)->send(new AdminBookingNotificationMail($this->booking));
                Log::info("Admin booking notification email sent successfully to gym admin email {$adminUser->email} for booking ID: {$this->booking->id}");
            } else {
                // Fallback to gym table email if no admin user is found
                $gym = $this->booking->gym ?? $timeslot->gym;
                if ($gym && $gym->email) {
                    Mail::to($gym->email)->send(new AdminBookingNotificationMail($this->booking));
                    Log::info("Admin booking notification email sent successfully to fallback gym email {$gym->email} for booking ID: {$this->booking->id}");
                } else {
                    Log::warning("Could not send admin booking notification email. Real gym admin and gym email are both missing for booking ID: {$this->booking->id}");
                }
            }
        } catch (\Throwable $e) {
            Log::error("Mail sending failed in SendAdminBookingNotificationEmail for booking ID: {$this->booking->id}. Error: " . $e->getMessage());
            if (config('queue.default') !== 'sync') {
                throw $e;
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        $bookingId = $this->booking ? $this->booking->id : 'Unknown';
        $gym = $this->booking ? ($this->booking->gym ?? $this->booking->timeslot?->gym) : null;
        $gymEmail = $gym ? $gym->email : 'Unknown Gym';
        
        Log::error("Admin booking notification email job failed. Booking ID: {$bookingId}. Gym Email: {$gymEmail}. Error: {$exception->getMessage()}", [
            'exception' => $exception
        ]);
    }
}
