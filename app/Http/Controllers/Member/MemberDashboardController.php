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

        // Upcoming bookings for this member
        $myUpcoming = Booking::with(['timeslot.service', 'timeslot.trainer'])
            ->where('user_id', auth()->id())
            ->whereHas('timeslot', function ($q) {
                $q->where('date', '>=', Carbon::today());
            })
            ->orderBy('booking_date')
            ->take(3)
            ->get();

        // Available classes today/upcoming
        $availableClasses = Timeslot::with(['service', 'trainer'])
            ->where('gym_id', $gymId)
            ->where('date', '>=', Carbon::today())
            ->where('status', 'active')
            ->where('capacity', '>', 0)
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        $stats = [
            'total_bookings'   => Booking::where('user_id', auth()->id())->count(),
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

        // Get IDs of classes already booked by the user to show status
        $myBookedIds = Booking::where('user_id', auth()->id())
            ->pluck('timeslot_id')
            ->toArray();

        return view('member.classes', compact('classes', 'myBookedIds'));
    }
}
