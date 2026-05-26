@extends('layouts.trainer')

@section('title', 'My Dashboard')
@section('subtitle')
    Welcome back, {{ auth()->user()->name }}
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

    {{-- ===== LEFT COLUMN: Trainer Profile Card ===== --}}
    <div class="lg:col-span-1 space-y-6">

        {{-- Profile Card --}}
        <div class="card p-8 text-center relative overflow-hidden">
            {{-- Decorative bg blob --}}
            <div class="absolute -top-6 -right-6 w-32 h-32 bg-purple-50 rounded-full opacity-60"></div>
            <div class="absolute -bottom-4 -left-4 w-24 h-24 bg-indigo-50 rounded-full opacity-40"></div>

            {{-- Avatar --}}
            <div class="relative inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-2xl font-extrabold text-2xl shadow-xl shadow-purple-200 mx-auto mb-5">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>

            <h2 class="text-xl font-extrabold text-gray-900 mb-1">{{ auth()->user()->name }}</h2>
            <p class="text-xs text-gray-400 font-medium mb-4 leading-relaxed px-2">
                {{ $trainer->specialization ?? 'Fitness Trainer' }}
            </p>

            {{-- Specialization badges --}}
            <div class="flex flex-wrap gap-2 justify-center mb-5">
                @foreach(collect(explode(',', $trainer->specialization ?? 'General'))->map(fn($s) => trim($s))->filter() as $spec)
                    <span class="px-3 py-1 bg-purple-50 text-purple-700 text-[10px] font-extrabold uppercase tracking-widest rounded-full border border-purple-100">
                        {{ $spec }}
                    </span>
                @endforeach
            </div>

            {{-- Quick stats --}}
            <div class="grid grid-cols-2 gap-3 text-center border-t border-gray-50 pt-5">
                <div>
                    <p class="text-xl font-black text-purple-600">{{ $stats['upcoming_sessions'] }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Upcoming</p>
                </div>
                <div>
                    <p class="text-xl font-black text-indigo-600">{{ number_format($trainer->hourly_rate ?? 0) }}</p>
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Rate/hr</p>
                </div>
            </div>
        </div>

        {{-- Status Card --}}
        <div class="card p-5">
            <div class="flex items-center justify-between mb-3">
                <p class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Status</p>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-widest
                    {{ $trainer->status === 'active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-500 border border-red-100' }}">
                    {{ ucfirst($trainer->status) }}
                </span>
            </div>
            <div class="flex items-center space-x-3 mt-4">
                <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-900">{{ $stats['total_enrolled'] }} Members</p>
                    <p class="text-[10px] text-gray-400">Total enrolled across all classes</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== RIGHT COLUMN: Stats + Schedule ===== --}}
    <div class="lg:col-span-3 space-y-8">

        {{-- Stats strip --}}
        <div class="grid grid-cols-3 gap-5">
            <div class="stat-card border-t-4 border-purple-500">
                <p class="text-3xl font-black text-purple-600 mb-1">{{ $stats['upcoming_sessions'] }}</p>
                <p class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Upcoming Classes</p>
            </div>
            <div class="stat-card border-t-4 border-indigo-500">
                <p class="text-3xl font-black text-indigo-600 mb-1">{{ $stats['total_enrolled'] }}</p>
                <p class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Students Active</p>
            </div>
            <div class="stat-card border-t-4 border-emerald-500">
                <p class="text-3xl font-black text-emerald-600 mb-1">{{ $stats['specializations'] }}</p>
                <p class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest">Expertise Areas</p>
            </div>
        </div>

        {{-- My Schedule --}}
        <div>
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-sm font-extrabold text-gray-900 uppercase tracking-widest flex items-center">
                    <span class="w-2 h-2 bg-purple-600 rounded-full mr-3 animate-pulse"></span>
                    My Schedule
                </h3>
                <a href="{{ route('trainer.classes') }}" id="view-all-classes-link"
                   class="text-[10px] font-black text-purple-600 hover:text-purple-800 uppercase tracking-widest transition-colors bg-purple-50 px-3 py-1.5 rounded-full">
                    View All Classes →
                </a>
            </div>

            <div class="space-y-4">
                @forelse($schedulePreview as $class)
                    @php
                        $enrolled  = $class->bookings_count;
                        $capacity  = $class->capacity;
                        $pct       = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
                        $barColor  = $pct >= 90 ? 'bg-red-500' : ($pct >= 60 ? 'bg-amber-500' : 'bg-gradient-to-r from-purple-500 to-indigo-500');
                        $duration  = \Carbon\Carbon::parse($class->start_time)->diffInMinutes(\Carbon\Carbon::parse($class->end_time));
                    @endphp
                    <div class="card p-6 border-transparent hover:border-purple-100 group" id="class-card-{{ $class->id }}">
                        <div class="flex items-start justify-between">
                            {{-- Class info --}}
                            <div class="flex items-start space-x-5">
                                <div class="text-center bg-purple-50 rounded-2xl px-4 py-3 group-hover:bg-purple-100 transition-colors min-w-[52px]">
                                    <p class="text-2xl font-black text-purple-700 leading-none">{{ \Carbon\Carbon::parse($class->date)->format('d') }}</p>
                                    <p class="text-[9px] font-extrabold uppercase tracking-widest text-purple-500">
                                        {{ \Carbon\Carbon::parse($class->date)->format('M') }}
                                        {{ \Carbon\Carbon::parse($class->date)->format('D') }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-gray-900 text-base mb-1">{{ $class->service->name }}</h4>
                                    <div class="flex items-center text-xs text-gray-400 font-medium space-x-3 mb-3">
                                        <span class="flex items-center">
                                            <svg class="w-3.5 h-3.5 mr-1 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ \Carbon\Carbon::parse($class->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($class->end_time)->format('H:i') }}
                                            ({{ $duration }} min)
                                        </span>
                                    </div>

                                    {{-- Enrollment bar --}}
                                    <div class="w-48">
                                        <div class="flex justify-between text-[10px] font-bold text-gray-500 mb-1.5">
                                            <span>Enrollment</span>
                                            <span class="{{ $pct >= 90 ? 'text-red-500' : 'text-gray-700' }}">{{ $enrolled }}/{{ $capacity }}</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="{{ $barColor }} h-full rounded-full" style="width: {{ $pct }}%"></div>
                                        </div>
                                        <p class="text-[9px] text-gray-400 mt-1 font-semibold">{{ $pct }}% Capacity</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex items-center space-x-2 ml-4 flex-shrink-0">
                                <a href="{{ route('trainer.members', $class->id) }}"
                                   id="view-members-btn-{{ $class->id }}"
                                   class="btn-purple text-xs px-4 py-2">
                                    View Members
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="card p-16 text-center border-dashed border-2 border-gray-100">
                        <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg class="w-10 h-10 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <p class="font-extrabold text-gray-400 uppercase tracking-widest text-xs mb-2">No upcoming classes</p>
                        <p class="text-xs text-gray-300">Your gym admin will assign classes to you soon.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
