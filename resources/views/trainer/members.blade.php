@extends('layouts.trainer')

@section('title', 'Enrolled Members')
@section('subtitle')
    {{ $timeslot->service->name ?? "Class" }} · {{ \Carbon\Carbon::parse($timeslot->date)->format("M d, Y") }}
@endsection

@section('content')
<div class="space-y-6">

    {{-- Back button + class info card --}}
    <div class="flex items-center space-x-4">
        <a href="{{ route('trainer.classes') }}" id="back-to-classes-btn"
           class="btn-outline flex items-center space-x-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <span>Back to Classes</span>
        </a>
    </div>

    {{-- Class summary strip --}}
    <div class="card p-6 bg-gradient-to-r from-purple-50 to-indigo-50 border-purple-100">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center space-x-5">
                <div class="w-14 h-14 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-purple-100">
                    <svg class="w-7 h-7 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-gray-900">{{ $timeslot->service->name }}</h3>
                    <div class="flex items-center space-x-4 text-xs text-gray-500 font-medium mt-1">
                        <span class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ \Carbon\Carbon::parse($timeslot->date)->format('D, M d Y') }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ \Carbon\Carbon::parse($timeslot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($timeslot->end_time)->format('H:i') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-6 text-center">
                <div>
                    <p class="text-2xl font-black text-purple-700">{{ $members->count() }}</p>
                    <p class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Enrolled</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-indigo-600">{{ max(0, $timeslot->capacity - $members->count()) }}</p>
                    <p class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Spots Left</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-700">{{ $timeslot->capacity }}</p>
                    <p class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Capacity</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Members table --}}
    <div class="card overflow-hidden">
        @if($members->count() > 0)
            <table class="w-full" id="members-table">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">#</th>
                        <th class="text-left px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Member</th>
                        <th class="text-left px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Email</th>
                        <th class="text-left px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Booked On</th>
                        <th class="text-left px-6 py-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($members as $index => $booking)
                        <tr class="hover:bg-gray-50/50 transition-colors" id="member-row-{{ $booking->id }}">
                            <td class="px-6 py-4 text-sm text-gray-400 font-bold">{{ $index + 1 }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 bg-gradient-to-br from-purple-100 to-indigo-100 text-purple-700 rounded-xl flex items-center justify-center font-extrabold text-sm">
                                        {{ strtoupper(substr($booking->user->name ?? '?', 0, 2)) }}
                                    </div>
                                    <span class="font-bold text-gray-900 text-sm">{{ $booking->user->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $booking->user->email ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($booking->booking_date ?? $booking->created_at)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @php $status = $booking->status ?? 'confirmed'; @endphp
                                <span class="px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest
                                    {{ $status === 'confirmed' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' :
                                       ($status === 'cancelled' ? 'bg-red-50 text-red-500 border border-red-100' : 'bg-amber-50 text-amber-600 border border-amber-100') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="p-20 text-center">
                <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <p class="font-extrabold text-gray-400 uppercase tracking-widest text-xs mb-2">No members enrolled yet</p>
                <p class="text-xs text-gray-300">Members will appear here once they book this class.</p>
            </div>
        @endif
    </div>

</div>
@endsection
