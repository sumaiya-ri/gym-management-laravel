<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Service;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('gym_id', auth()->user()->gym_id)->get();
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|in:active,inactive',
        ]);

        Service::create([
            'gym_id' => auth()->user()->gym_id,
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service created successfully.');
    }
}
