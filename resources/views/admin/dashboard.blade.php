@extends('layouts.admin')

@section('title', 'System Overview')

@section('content')
<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
    <div class="card p-6 border-l-[6px] border-purple-500 flex items-center">
        <div class="bg-purple-50 p-4 rounded-2xl mr-5">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.618.309a6 6 0 01-3.86.517l-2.387-.477a2 2 0 00-1.022.547l-1.168 1.168a2 2 0 000 2.828l1.168 1.168a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.618-.309a6 6 0 013.86-.517l2.387.477a2 2 0 001.022-.547l1.168-1.168a2 2 0 000-2.828l-1.168-1.168z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Total Services</p>
            <p class="text-3xl font-black text-gray-900 tracking-tight">{{ $stats['services'] }}</p>
        </div>
    </div>
    <div class="card p-6 border-l-[6px] border-indigo-500 flex items-center">
        <div class="bg-indigo-50 p-4 rounded-2xl mr-5">
            <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Active Trainers</p>
            <p class="text-3xl font-black text-gray-900 tracking-tight">{{ $stats['trainers'] }}</p>
        </div>
    </div>
    <div class="card p-6 border-l-[6px] border-emerald-500 flex items-center">
        <div class="bg-emerald-50 p-4 rounded-2xl mr-5">
            <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <div>
            <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest mb-1">Scheduled Classes</p>
            <p class="text-3xl font-black text-gray-900 tracking-tight">{{ $stats['classes'] }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-10">
    <!-- Upcoming Classes List -->
    <div class="lg:col-span-3">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-widest flex items-center">
                <span class="w-2 h-2 bg-purple-600 rounded-full mr-3 animate-pulse"></span>
                Upcoming Classes
            </h3>
            <a href="{{ route('admin.timeslots.index') }}" class="text-[10px] font-black text-purple-600 hover:text-purple-800 uppercase tracking-widest transition-colors bg-purple-50 px-3 py-1.5 rounded-full">View Master Schedule</a>
        </div>

        <div class="space-y-4">
            @forelse($upcomingClasses as $class)
                <div class="card p-5 flex items-center justify-between border-transparent hover:border-purple-100 group">
                    <div class="flex items-center">
                        <div class="w-14 h-14 bg-gray-50 flex items-center justify-center rounded-2xl mr-5 group-hover:bg-purple-50 transition-colors">
                            <svg class="w-7 h-7 text-gray-400 group-hover:text-purple-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-base">{{ $class->service->name }}</h4>
                            <div class="flex items-center text-xs font-medium text-gray-400 mt-1 space-x-5">
                                <span class="flex items-center"><svg class="w-3.5 h-3.5 mr-1.5 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg> {{ $class->trainer->name }}</span>
                                <span class="flex items-center"><svg class="w-3.5 h-3.5 mr-1.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> {{ \Carbon\Carbon::parse($class->date)->format('M d, Y') }}</span>
                                <span class="flex items-center font-bold text-purple-600">{{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        @php
                            $booked = $class->bookings_count ?? 0;
                            $total = $class->capacity + $booked;
                            $pct = $total > 0 ? round(($booked / $total) * 100) : 0;
                        @endphp
                        <div class="flex items-center justify-end mb-2">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mr-3">Occupancy</span>
                            <span class="text-xs font-bold text-gray-900">{{ $booked }} / {{ $total }}</span>
                        </div>
                        <div class="w-32 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-16 text-center border-dashed border-2 border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No classes found</p>
                    <a href="{{ route('admin.timeslots.create') }}" class="mt-4 inline-block text-sm font-bold text-purple-600 hover:underline">Schedule your first class now →</a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions Sidebar -->
    <div class="lg:col-span-1">
        <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-widest mb-6">Quick Actions</h3>
        
        <div class="space-y-4">
            <a href="{{ route('admin.services.create') }}" class="card p-5 flex flex-col hover:bg-gray-50 border-transparent hover:border-gray-200 group">
                <div class="bg-purple-100 p-3 rounded-xl text-purple-600 w-fit mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </div>
                <h4 class="font-bold text-gray-900">Add Service</h4>
                <p class="text-xs text-gray-400 mt-1">Create new fitness programs</p>
            </a>

            <a href="{{ route('admin.trainers.create') }}" class="card p-5 flex flex-col hover:bg-gray-50 border-transparent hover:border-gray-200 group">
                <div class="bg-indigo-100 p-3 rounded-xl text-indigo-600 w-fit mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <h4 class="font-bold text-gray-900">New Trainer</h4>
                <p class="text-xs text-gray-400 mt-1">Onboard a new instructor</p>
            </a>

            <a href="{{ route('admin.timeslots.create') }}" class="card p-5 flex flex-col hover:bg-gray-50 border-transparent hover:border-gray-200 group">
                <div class="bg-emerald-100 p-3 rounded-xl text-emerald-600 w-fit mb-4 group-hover:scale-110 transition-transform">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-bold text-gray-900">Schedule Class</h4>
                <p class="text-xs text-gray-400 mt-1">Pick a time and trainer</p>
            </a>

            <!-- Live Notification Feed Component -->
            <livewire:gym-admin.live-notification-feed />
        </div>
    </div>
</div>
@endsection
