<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Service;
use App\Models\Trainer;
use App\Models\Timeslot;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $gym_id = auth()->user()->gym_id;

        $stats = [
            'services' => Service::where('gym_id', $gym_id)->count(),
            'trainers' => Trainer::where('gym_id', $gym_id)->count(),
            'classes' => Timeslot::where('gym_id', $gym_id)->count(),
        ];

        $upcomingClasses = Timeslot::with(['service', 'trainer'])
            ->withCount(['bookings' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->where('gym_id', $gym_id)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'upcomingClasses'));
    }
}
