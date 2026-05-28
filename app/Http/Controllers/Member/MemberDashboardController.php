<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Timeslot;
use App\Models\Booking;
use App\Models\Gym;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MemberDashboardController extends Controller
{
    /**
     * Member dashboard overview.
     */
    public function index()
    {
        $gymId = auth()->user()->gym_id;
        $gym   = Gym::find($gymId);

        // Upcoming bookings for this member (excluding cancelled)
        $myUpcoming = Booking::with(['timeslot.service', 'timeslot.trainer'])
            ->where('user_id', auth()->id())
            ->where('status', '!=', 'cancelled')
            ->whereHas('timeslot', function ($q) {
                $q->where('date', '>=', Carbon::today());
            })
            ->orderBy('booking_date')
            ->take(3)
            ->get();

        // Available classes today/upcoming (excluding classes the user has already booked)
        $availableClasses = Timeslot::with(['service', 'trainer'])
            ->where('gym_id', $gymId)
            ->where('date', '>=', Carbon::today())
            ->where('status', 'active')
            ->where('capacity', '>', 0)
            ->whereDoesntHave('bookings', function ($q) {
                $q->where('user_id', auth()->id())
                  ->where('status', '!=', 'cancelled');
            })
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $stats = [
            'total_bookings'   => Booking::where('user_id', auth()->id())->where('status', '!=', 'cancelled')->count(),
            'upcoming_classes' => $myUpcoming->count(),
            'gym_name'         => $gym->name ?? 'My Gym',
        ];

        return view('member.dashboard', compact('gym', 'myUpcoming', 'availableClasses', 'stats'));
    }

    /**
     * Browse all available classes in the gym.
     */
    public function classes()
    {
        $gymId = auth()->user()->gym_id;

        $classes = Timeslot::with(['service', 'trainer', 'bookings'])
            ->withCount('bookings')
            ->where('gym_id', $gymId)
            ->where('date', '>=', Carbon::today())
            ->where('status', 'active')
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(9);

        // Get IDs of classes already booked by the user to show status (excluding cancelled)
        $myBookedIds = Booking::where('user_id', auth()->id())
            ->where('status', '!=', 'cancelled')
            ->pluck('timeslot_id')
            ->toArray();

        return view('member.classes', compact('classes', 'myBookedIds'));
    }
}
