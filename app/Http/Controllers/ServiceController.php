<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Service;


class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        if (auth()->user()->role !== 'admin') {
            abort(403); // forbidden
        }

        return view('services.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        Service::create([
            'name' => $request->name,
            'duration' => $request->duration,
            'gym_id' => auth()->user()->gym_id
        ]);

        return redirect('/services');
    }
}