<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Gym;

class GymController extends Controller
{
    /**
     * Display the gym details.
     */
    public function show()
    {
        $gym = auth()->user()->gym;
        
        if (!$gym) {
            abort(404, 'No gym assigned to your user account.');
        }

        return view('admin.gym.show', compact('gym'));
    }

    /**
     * Show the form for editing gym details.
     */
    public function edit()
    {
        $gym = auth()->user()->gym;

        if (!$gym) {
            abort(404, 'No gym assigned to your user account.');
        }

        return view('admin.gym.edit', compact('gym'));
    }

    /**
     * Update the gym details.
     */
    public function update(Request $request)
    {
        $gym = auth()->user()->gym;

        if (!$gym) {
            abort(404, 'No gym assigned to your user account.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        $gym->update($validated);

        return redirect()->route('admin.gym.show')->with('success', 'Gym details updated successfully.');
    }
}
