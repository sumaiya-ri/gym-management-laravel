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

    public function edit(Service $service)
    {
        // Enforce gym isolation
        if ($service->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        // Enforce gym isolation
        if ($service->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'status' => 'required|string|in:active,inactive',
        ]);

        $service->update([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        // Enforce gym isolation
        if ($service->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }

        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
