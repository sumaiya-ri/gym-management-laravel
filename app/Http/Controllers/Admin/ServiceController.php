<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Service; //imports the service tbl

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('gym_id', auth()->user()->gym_id)->paginate(10);
        return view('admin.services.index', compact('services'));
    }

    public function create()
    {
        return view('admin.services.create');
    } //opens resources/admins/views pg

    public function store(Request $request) //handles form submission
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
    } //redirect back to service list and displays 

    public function edit(Service $service)
    {
        // Enforce gym isolation //cechks if logged in user matches gym id
        if ($service->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }
//display the edit form page 
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
        // Enforce gym isolation. checks owndership first
        if ($service->gym_id !== auth()->user()->gym_id) {
            abort(403, 'Unauthorized action.');
        }

        $service->delete();
//DELETE FROM services
// WHERE id = ?; equivelant 
        return redirect()->route('admin.services.index')->with('success', 'Service deleted successfully.');
    }
}
