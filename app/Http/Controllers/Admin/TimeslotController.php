<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Timeslot;
use App\Models\Service;
use App\Models\Trainer;

class TimeslotController extends Controller
{
    public function index()
    {
        $timeslots = Timeslot::with(['service', 'trainer'])
            ->withCount(['bookings' => function ($q) {
                $q->where('status', 'confirmed');
            }])
            ->where('gym_id', auth()->user()->gym_id)
            ->orderBy('date', 'desc')
            ->paginate(10);
        return view('admin.timeslots.index', compact('timeslots'));
    }

    public function create()
    {
        $gym_id = auth()->user()->gym_id;
        $services = Service::where('gym_id', $gym_id)->where('status', 'active')->get();
        $trainers = Trainer::where('gym_id', $gym_id)->where('status', 'active')->get();

        return view('admin.timeslots.create', compact('services', 'trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'trainer_id' => 'required|exists:trainers,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|string|in:active,inactive',
        ]);

        Timeslot::create([
            'gym_id' => auth()->user()->gym_id,
            'service_id' => $request->service_id,
            'trainer_id' => $request->trainer_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'capacity' => $request->capacity,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.timeslots.index')->with('success', 'Timeslot scheduled successfully.');
    }
}
