@extends('layouts.trainer')

@section('title', 'My Classes')
@section('subtitle')
    All assigned timeslots for your gym
@endsection

@section('content')
<div class="space-y-6">

    {{-- Page header --}}
    <div class="flex justify-between items-center">
        <div>
            <p class="text-xs text-gray-400 font-medium">
                Showing {{ $classes->firstItem() ?? 0 }}–{{ $classes->lastItem() ?? 0 }} of {{ $classes->total() }} classes
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Gym:</span>
            <span class="text-xs font-extrabold text-purple-700 bg-purple-50 px-3 py-1 rounded-full">{{ auth()->user()->gym->name ?? 'My Gym' }}</span>
        </div>
    </div>

    {{-- Classes list --}}
    <div class="space-y-4">
        @forelse($classes as $class)
            @php
                $enrolled  = $class->bookings_count;
                $capacity  = $class->capacity;
                $pct       = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
                $remaining = max(0, $capacity - $enrolled);
                $isPast    = \Carbon\Carbon::parse($class->date)->isPast();
                $duration  = \Carbon\Carbon::parse($class->start_time)->diffInMinutes(\Carbon\Carbon::parse($class->end_time));
            @endphp
            <div class="card p-6 border-transparent hover:border-purple-100 group" id="class-row-{{ $class->id }}">
                <div class="flex items-center justify-between flex-wrap gap-4">

                    {{-- Date badge + class info --}}
                    <div class="flex items-center space-x-5">
                        <div class="text-center {{ $isPast ? 'bg-gray-50' : 'bg-purple-50 group-hover:bg-purple-100' }} rounded-2xl px-4 py-3 transition-colors min-w-[52px]">
                            <p class="text-2xl font-black {{ $isPast ? 'text-gray-400' : 'text-purple-700' }} leading-none">
                                {{ \Carbon\Carbon::parse($class->date)->format('d') }}
                            </p>
                            <p class="text-[9px] font-extrabold uppercase tracking-widest {{ $isPast ? 'text-gray-400' : 'text-purple-500' }}">
                                {{ \Carbon\Carbon::parse($class->date)->format('M D') }}
                            </p>
                        </div>

                        <div>
                            <div class="flex items-center space-x-2 mb-1">
                                <h4 class="font-extrabold text-gray-900 text-base">{{ $class->service->name }}</h4>
                                @if($isPast)
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-400 text-[9px] font-extrabold uppercase tracking-widest rounded-full">Past</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-600 text-[9px] font-extrabold uppercase tracking-widest rounded-full border border-emerald-100">Upcoming</span>
                                @endif
                            </div>
                            <div class="flex items-center text-xs text-gray-400 font-medium space-x-4">
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                    ({{ $duration }} min)
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ $enrolled }} enrolled · {{ $remaining }} spots left
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Capacity bar + action --}}
                    <div class="flex items-center space-x-6">
                        <div class="w-36">
                            <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1.5">
                                <span>Capacity</span>
                                <span class="{{ $pct >= 90 ? 'text-red-500 font-extrabold' : 'text-gray-700' }}">{{ $pct }}%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-gradient-to-r from-purple-500 to-indigo-500') }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="text-[9px] text-gray-400 mt-1">{{ $enrolled }} / {{ $capacity }}</p>
                        </div>

                        <a href="{{ route('trainer.members', $class->id) }}"
                           id="members-link-{{ $class->id }}"
                           class="btn-purple whitespace-nowrap">
                            View Members
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="card p-20 text-center border-dashed border-2 border-gray-100">
                <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <p class="font-extrabold text-gray-400 uppercase tracking-widest text-xs mb-2">No classes assigned</p>
                <p class="text-xs text-gray-300">Ask your gym admin to schedule classes for you.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($classes->hasPages())
        <div class="mt-6">
            {{ $classes->links() }}
        </div>
    @endif

</div>
@endsection
