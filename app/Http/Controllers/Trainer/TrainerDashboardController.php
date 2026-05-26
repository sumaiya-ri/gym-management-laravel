<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Trainer;
use App\Models\Timeslot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrainerDashboardController extends Controller
{
    /**
     * Resolve the trainer record for the authenticated user.
     * Double-locked: must match BOTH user_id AND gym_id.
     */
    private function getTrainer(): Trainer
    {
        return Trainer::where('user_id', auth()->id())
            ->where('gym_id', auth()->user()->gym_id)
            ->firstOrFail();
    }

    /**
     * Main trainer dashboard overview.
     */
    public function index()
    {
        $gymId   = auth()->user()->gym_id;
        $trainer = $this->getTrainer();

        // All upcoming classes for this trainer — gym isolated
        $upcomingClasses = Timeslot::with(['service', 'bookings.user'])
            ->withCount('bookings')
            ->where('gym_id', $gymId)
            ->where('trainer_id', $trainer->id)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        // All classes (past + upcoming) for total enrolled count
        $allClasses = Timeslot::withCount('bookings')
            ->where('gym_id', $gymId)
            ->where('trainer_id', $trainer->id)
            ->get();

        $stats = [
            'upcoming_sessions' => $upcomingClasses->count(),
            'total_enrolled'    => $allClasses->sum('bookings_count'),
            'specializations'   => collect(explode(',', $trainer->specialization ?? ''))
                                        ->map(fn($s) => trim($s))
                                        ->filter()
                                        ->count(),
        ];

        // Take first 5 for the dashboard schedule strip
        $schedulePreview = $upcomingClasses->take(5);

        return view('trainer.dashboard', compact('trainer', 'stats', 'schedulePreview', 'upcomingClasses'));
    }

    /**
     * Full list of assigned classes (paginated).
     */
    public function classes()
    {
        $gymId   = auth()->user()->gym_id;
        $trainer = $this->getTrainer();

        $classes = Timeslot::with(['service', 'bookings'])
            ->withCount('bookings')
            ->where('gym_id', $gymId)
            ->where('trainer_id', $trainer->id)
            ->orderBy('date', 'desc')
            ->orderBy('start_time')
            ->paginate(10);

        return view('trainer.classes', compact('trainer', 'classes'));
    }

    /**
     * View members enrolled in a specific timeslot.
     * Gym-isolated: timeslot must belong to trainer's gym AND trainer.
     */
    public function members(int $timeslotId)
    {
        $gymId   = auth()->user()->gym_id;
        $trainer = $this->getTrainer();

        $timeslot = Timeslot::with(['service', 'bookings.user'])
            ->where('gym_id', $gymId)
            ->where('trainer_id', $trainer->id)
            ->where('id', $timeslotId)
            ->firstOrFail();

        $members = $timeslot->bookings;

        return view('trainer.members', compact('trainer', 'timeslot', 'members'));
    }
}
