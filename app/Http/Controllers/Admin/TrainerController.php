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

    public function edit(Trainer $trainer)
    {
        // Enforce gym isolation
        if ($trainer->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.trainers.edit', compact('trainer'));
    }

    public function update(Request $request, Trainer $trainer)
    {
        // Enforce gym isolation
        if ($trainer->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }

        $user = $trainer->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($user->id ?? 0) . '|max:255',
            'password' => 'nullable|string|min:8',
            'specialization' => 'nullable|string|max:255',
            'hourly_rate' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:active,inactive',
        ]);

        DB::transaction(function () use ($request, $trainer, $user) {
            if ($user) {
                $userUserData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'status' => $request->status,
                ];

                if ($request->filled('password')) {
                    $userUserData['password'] = bcrypt($request->password);
                }

                $user->update($userUserData);
            }

            $trainer->update([
                'name' => $request->name,
                'specialization' => $request->specialization,
                'hourly_rate' => $request->hourly_rate,
                'status' => $request->status,
            ]);
        });

        return redirect()->route('admin.trainers.index')->with('success', 'Trainer updated successfully.');
    }

    public function destroy(Trainer $trainer)
    {
        // Enforce gym isolation
        if ($trainer->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }

        DB::transaction(function () use ($trainer) {
            $user = $trainer->user;
            $trainer->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()->route('admin.trainers.index')->with('success', 'Trainer deleted successfully.');
    }
}
