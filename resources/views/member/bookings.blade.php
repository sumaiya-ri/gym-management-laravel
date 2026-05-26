@extends('layouts.member')

@section('title', 'My Bookings')
@section('subtitle', 'Manage your upcoming class bookings')

@section('content')
<div class="space-y-8">
    <div class="flex justify-between items-center">
        <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-widest flex items-center">
            <span class="w-2 h-2 bg-indigo-600 rounded-full mr-3"></span>
            Manage your upcoming class bookings
        </h3>
        <a href="{{ route('member.classes') }}" class="btn-purple text-xs font-black uppercase tracking-widest px-6 py-3 flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
            Book New Class
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        @forelse($bookings as $booking)
            @php
                $isPast = \Carbon\Carbon::parse($booking->timeslot->date)->isPast();
                $isCancelled = $booking->status === 'cancelled';
            @endphp
            <div class="card p-8 border-transparent hover:border-indigo-100 group transition-all duration-300 flex flex-col md:flex-row gap-8 items-start relative {{ $isCancelled ? 'opacity-60 grayscale' : '' }}">
                <div class="w-20 h-20 bg-indigo-50 rounded-3xl flex items-center justify-center text-indigo-600 shadow-sm border border-indigo-100 shrink-0">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>

                <div class="flex-1">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-xl font-black text-gray-900 tracking-tight">{{ $booking->timeslot->service->name }}</h4>
                            <p class="text-[10px] text-indigo-600 font-black uppercase tracking-widest mt-1">Studio A · {{ $booking->timeslot->gym->name }}</p>
                        </div>
                        <span class="px-3 py-1 text-[10px] font-extrabold uppercase tracking-widest rounded-full
                            @if($isCancelled) bg-gray-100 text-gray-500 @elseif($isPast) bg-gray-100 text-gray-500 @else bg-emerald-50 text-emerald-600 border border-emerald-100 @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 mb-8">
                        <div class="flex items-center text-xs text-gray-500 font-medium">
                            <svg class="w-4 h-4 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ $booking->timeslot->trainer->name }}
                        </div>
                        <div class="flex items-center text-xs text-gray-500 font-medium">
                            <svg class="w-4 h-4 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::parse($booking->timeslot->date)->format('M d, Y') }}
                        </div>
                        <div class="flex items-center text-xs text-gray-500 font-medium col-span-2">
                            <svg class="w-4 h-4 mr-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ \Carbon\Carbon::parse($booking->timeslot->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($booking->timeslot->end_time)->format('h:i A') }}
                        </div>
                    </div>

                    @if(!$isPast && !$isCancelled)
                        <form action="{{ route('member.cancel', $booking->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3 border-2 border-red-50 text-red-500 hover:bg-red-50 hover:border-red-100 rounded-2xl font-bold text-xs flex items-center justify-center transition-all">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Cancel Booking
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full card p-20 text-center border-dashed border-2 border-gray-100">
                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-indigo-50">
                    <svg class="w-10 h-10 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2 tracking-tight">Plan your week ahead</h4>
                <p class="text-gray-400 text-sm max-w-xs mx-auto mb-8">Check out our schedule to find more classes that fit your goals.</p>
                <a href="{{ route('member.classes') }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-[0.2em] transition-colors border-b-2 border-indigo-100 pb-1">Browse Schedule →</a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
