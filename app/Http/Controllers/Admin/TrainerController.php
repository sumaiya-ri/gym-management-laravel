<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Trainer;
use App\Models\User;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::where('gym_id', auth()->user()->gym_id)->get();
        return view('admin.trainers.index', compact('trainers'));
    }

    public function create()
    {
        return view('admin.trainers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'nullable|string|min:8',
            'specialization' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password ?? 'password'),
                'role' => 'trainer',
                'gym_id' => auth()->user()->gym_id,
            ]);

            Trainer::create([
                'gym_id' => auth()->user()->gym_id,
                'user_id' => $user->id,
                'name' => $request->name,
                'specialization' => $request->specialization,
                'hourly_rate' => $request->hourly_rate,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('admin.trainers.index')->with('success', 'Trainer created successfully.');
    }
}
