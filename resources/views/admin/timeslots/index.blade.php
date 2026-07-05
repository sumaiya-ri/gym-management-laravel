@extends('layouts.admin')

@section('title', 'Class Schedule')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Master Schedule</h3>
        <p class="text-sm text-gray-500 mt-1">View and manage all scheduled fitness classes.</p>
    </div>
    <a href="{{ route('admin.timeslots.create') }}" class="bg-purple-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        Schedule Class
    </a>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Date & Time</th>
                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Service</th>
                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest">Trainer</th>
                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest text-center">Capacity</th>
                <th class="px-8 py-5 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Status</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($timeslots as $slot)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-5">
                        <p class="font-bold text-gray-800">{{ \Carbon\Carbon::parse($slot->date)->format('M d, Y') }}</p>
                        <p class="text-xs text-purple-600 font-bold uppercase tracking-wider mt-0.5">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}
                        </p>
                    </td>
                    <td class="px-8 py-5">
                        <p class="font-bold text-gray-700">{{ $slot->service->name }}</p>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-50 text-purple-600 rounded-lg flex items-center justify-center font-bold text-xs mr-3 border border-purple-100">
                                {{ strtoupper(substr($slot->trainer->name, 0, 1)) }}
                            </div>
                            <span class="font-medium text-gray-700">{{ $slot->trainer->name }}</span>
                        </div>
                    </td>
                    <td class="px-8 py-5 text-center">
                        @php
                            $booked = $slot->bookings_count ?? 0;
                            $total = $slot->capacity + $booked;
                        @endphp
                        <span class="text-sm font-bold text-gray-800">{{ $booked }} / {{ $total }}</span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        @if($slot->status === 'active')
                            <span class="inline-flex items-center px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-bold uppercase tracking-widest">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-bold uppercase tracking-widest">
                                Inactive
                            </span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-8 py-12 text-center text-gray-400 font-medium">
                        No classes scheduled. <a href="{{ route('admin.timeslots.create') }}" class="text-purple-600 font-bold hover:underline">Schedule your first class</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    @if($timeslots->hasPages())
        <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/20">
            {{ $timeslots->links() }}
        </div>
    @endif
</div>
@endsection
