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
        $gym_id = auth()->user()->gym_id; //multi-tenant isolation

        $stats = [
            'services' => Service::where('gym_id', $gym_id)->count(), //gives the count for each in the db
            'trainers' => Trainer::where('gym_id', $gym_id)->count(),
            'classes' => Timeslot::where('gym_id', $gym_id)->count(),
        ];

        $upcomingClasses = Timeslot::with(['service', 'trainer']) //loads each timelsot with service and trainer
            ->withCount(['bookings' => function ($q) {
                $q->where('status', 'confirmed'); //count confirmed bookings
            }])
            ->where('gym_id', $gym_id)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time') //sort upcoming classes 
            ->take(5) //next 5 classes show
            ->get();

        return view('admin.dashboard', compact('stats', 'upcomingClasses'));
    } //send data to view 
}
