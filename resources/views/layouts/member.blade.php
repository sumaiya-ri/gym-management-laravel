<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gym Member') }} — @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f9fafb; color: #1f2937; }
        .sidebar { background: #ffffff; border-right: 1px solid #f3f4f6; }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; font-weight: 500; }
        .nav-link:hover { background-color: #f3f4f6; color: #7c3aed; }
        .nav-link.active { background-color: #f5f3ff; color: #7c3aed; }
        .card { background: #ffffff; border-radius: 1.25rem; border: 1px solid #f3f4f6; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); transition: all 0.3s ease; }
        .card:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); }
        .btn-purple { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); color: white; padding: 0.6rem 1.25rem; border-radius: 0.75rem; font-weight: 600; font-size: 0.8rem; transition: all 0.2s; display: inline-block; }
        .btn-purple:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124,58,237,0.3); }
        .progress-bar { background: #f3f4f6; border-radius: 9999px; overflow: hidden; height: 6px; }
        .progress-fill { background: linear-gradient(90deg, #7c3aed, #6366f1); border-radius: 9999px; height: 100%; transition: width 0.6s ease; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="sidebar w-72 flex-shrink-0 flex flex-col">
            <div class="p-8">
                <div class="flex items-center space-x-3 mb-2">
                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-2.5 rounded-xl shadow-lg shadow-purple-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight text-gray-900">
                        @if(auth()->user()->gym)
                            @php
                                $gymName = auth()->user()->gym->name;
                                $parts = explode(' ', $gymName, 2);
                            @endphp
                            {{ $parts[0] }}@if(isset($parts[1]))<span class="text-purple-600"> {{ $parts[1] }}</span>@endif
                        @else
                            Gym<span class="text-purple-600">Member</span>
                        @endif
                    </span>
                </div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] ml-1">Client Portal</p>
            </div>

            <nav class="flex-1 px-6 space-y-1.5 overflow-y-auto mt-4">
                <a href="{{ route('member.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('member.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    Overview
                </a>

                <a href="{{ route('member.classes') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('member.classes') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Browse Classes
                </a>

                <a href="{{ route('member.bookings') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('member.bookings') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    My Bookings
                </a>

                <a href="{{ route('profile.show') }}" class="nav-link flex items-center px-4 py-3 text-gray-500">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    My Profile
                </a>
            </nav>

            <div class="p-8 border-t border-gray-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-semibold text-red-500 hover:bg-red-50 rounded-xl transition-all">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 py-5 px-10 flex justify-between items-center z-10">
                <div>
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">@yield('title', 'Dashboard')</h2>
                    <p class="text-xs text-gray-400 font-medium">@yield('subtitle', 'Welcome back to your fitness journey')</p>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="text-right">
                        <p class="text-xs font-black text-purple-600 uppercase tracking-widest">{{ auth()->user()->gym->name ?? 'Gym' }}</p>
                        <p class="text-[9px] text-gray-400 font-bold">{{ auth()->user()->gym->address ?? '' }}</p>
                    </div>
                    <div class="flex items-center space-x-4 bg-gray-50 py-1.5 pl-4 pr-1.5 rounded-full border border-gray-100">
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-[9px] text-purple-600 font-extrabold uppercase tracking-widest">Member</p>
                        </div>
                        <div class="w-9 h-9 bg-purple-600 text-white flex items-center justify-center rounded-full font-bold shadow-md shadow-purple-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Area -->
            <main class="flex-1 overflow-y-auto p-10">
                @if(session('success'))
                    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                        <div class="bg-emerald-500 p-1.5 rounded-lg mr-3">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-8 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center shadow-sm">
                        <div class="bg-red-500 p-1.5 rounded-lg mr-3">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                        </div>
                        <span class="font-semibold text-sm">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
