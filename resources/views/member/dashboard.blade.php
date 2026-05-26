@extends('layouts.member')

@section('title', 'Member Dashboard')
@section('subtitle')
    Welcome back, {{ auth()->user()->name }}
@endsection

@section('content')
<div class="space-y-8">
    {{-- Daily Motivation Quote Widget --}}
    <livewire:member.daily-motivation-quote />

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    {{-- Left Section: Stats & Quick Actions --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="card p-8 text-center relative overflow-hidden">
            <div class="absolute -top-6 -right-6 w-32 h-32 bg-purple-50 rounded-full opacity-60"></div>
            <div class="relative inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-2xl font-extrabold text-2xl shadow-xl shadow-purple-200 mx-auto mb-5">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <h2 class="text-xl font-extrabold text-gray-900 mb-1">{{ auth()->user()->name }}</h2>
            <p class="text-[10px] text-purple-600 font-black uppercase tracking-widest">{{ auth()->user()->gym->name }} Member</p>

            <div class="grid grid-cols-2 gap-3 text-center border-t border-gray-50 pt-6 mt-6">
                <div>
                    <p class="text-xl font-black text-purple-600">{{ $stats['total_bookings'] }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Total Bookings</p>
                </div>
                <div>
                    <p class="text-xl font-black text-indigo-600">{{ $stats['upcoming_classes'] }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Upcoming</p>
                </div>
            </div>
        </div>

        <div class="card p-6">
            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('member.classes') }}" class="flex items-center p-3 bg-purple-50 text-purple-700 rounded-xl font-bold text-sm hover:bg-purple-100 transition-colors">
                    <div class="bg-purple-600 text-white p-2 rounded-lg mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    Book a Class
                </a>
                <a href="{{ route('member.bookings') }}" class="flex items-center p-3 bg-indigo-50 text-indigo-700 rounded-xl font-bold text-sm hover:bg-indigo-100 transition-colors">
                    <div class="bg-indigo-600 text-white p-2 rounded-lg mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    My Schedule
                </a>
            </div>
        </div>
    </div>

    {{-- Right Section: Schedule & Browse --}}
    <div class="lg:col-span-3 space-y-8">
        {{-- My Upcoming Classes --}}
        <div>
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-widest flex items-center">
                    <span class="w-2 h-2 bg-purple-600 rounded-full mr-3 animate-pulse"></span>
                    My Upcoming sessions
                </h3>
                <a href="{{ route('member.bookings') }}" class="text-[10px] font-black text-purple-600 hover:text-purple-800 uppercase tracking-widest transition-colors bg-purple-50 px-3 py-1.5 rounded-full">View Full Schedule →</a>
            </div>

            <div class="space-y-4">
                @forelse($myUpcoming as $booking)
                    <div class="card p-5 flex items-center justify-between border-transparent hover:border-purple-100 group">
                        <div class="flex items-center">
                            <div class="w-14 h-14 bg-purple-50 flex items-center justify-center rounded-2xl mr-5 group-hover:bg-purple-100 transition-colors text-purple-600">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-base">{{ $booking->timeslot->service->name }}</h4>
                                <div class="flex items-center text-xs font-medium text-gray-400 mt-1 space-x-5">
                                    <span class="flex items-center"><svg class="w-3.5 h-3.5 mr-1.5 text-purple-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg> {{ $booking->timeslot->trainer->name }}</span>
                                    <span class="flex items-center font-bold text-purple-600">{{ \Carbon\Carbon::parse($booking->timeslot->start_time)->format('H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-900">{{ \Carbon\Carbon::parse($booking->timeslot->date)->format('M d, Y') }}</p>
                            <p class="text-[9px] text-emerald-500 font-extrabold uppercase tracking-widest mt-1">Confirmed</p>
                        </div>
                    </div>
                @empty
                    <div class="card p-10 text-center border-dashed border-2 border-gray-100">
                        <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No upcoming bookings</p>
                        <a href="{{ route('member.classes') }}" class="mt-3 inline-block text-xs font-bold text-purple-600 hover:underline">Browse classes to book your first session!</a>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Suggested for You --}}
        <div>
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-widest flex items-center">
                    <span class="w-2 h-2 bg-indigo-600 rounded-full mr-3"></span>
                    Available Today & Tomorrow
                </h3>
                <a href="{{ route('member.classes') }}" class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest transition-colors bg-indigo-50 px-3 py-1.5 rounded-full">View All Classes</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($availableClasses as $class)
                    <div class="card p-6 border-transparent hover:border-indigo-100 group">
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $class->service->name }}</h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">{{ $class->trainer->name }}</p>
                            </div>
                            <div class="bg-indigo-50 p-2 rounded-xl text-indigo-600 group-hover:bg-indigo-100 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-bold text-gray-500">
                                {{ \Carbon\Carbon::parse($class->date)->format('D, M d') }} · <span class="text-indigo-600">{{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }}</span>
                            </div>
                            <a href="{{ route('member.payment.checkout', $class->id) }}" class="text-[10px] font-black text-white bg-indigo-600 px-4 py-2 rounded-full shadow-lg shadow-indigo-100 hover:scale-105 transition-transform active:scale-95 text-center">Book Now</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
@endsection
