<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Timeslot;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * View all bookings for the member.
     */
    public function index()
    {
        $bookings = Booking::with(['timeslot.service', 'timeslot.trainer', 'timeslot.gym'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('member.bookings', compact('bookings'));
    }

    /**
     * Handle booking a class.
     */
    public function store(int $timeslotId)
    {
        return redirect()->route('member.payment.checkout', $timeslotId);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(int $bookingId)
    {
        $booking = Booking::with('timeslot')
            ->where('id', $bookingId)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'This booking is already cancelled.');
        }

        DB::transaction(function () use ($booking) {
            $booking->update(['status' => 'cancelled']);
            $booking->timeslot->increment('capacity');
        });

        Log::info("Booking ID: {$booking->id} cancelled by User ID: " . auth()->id());

        return back()->with('success', 'Booking cancelled successfully.');
    }
}
