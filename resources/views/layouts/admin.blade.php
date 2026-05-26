<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Gym Admin') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f9fafb;
            color: #1f2937;
        }
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #f3f4f6;
        }
        .nav-link {
            transition: all 0.2s ease;
            border-radius: 0.75rem;
            font-weight: 500;
        }
        .nav-link:hover {
            background-color: #f3f4f6;
            color: #7c3aed;
        }
        .nav-link.active {
            background-color: #f5f3ff;
            color: #7c3aed;
        }
        .card {
            background: #ffffff;
            border-radius: 1.25rem;
            border: 1px solid #f3f4f6;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        }
        .btn-purple {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: transform 0.2s;
        }
        .btn-purple:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
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
                            Gym<span class="text-purple-600">Admin</span>
                        @endif
                    </span>
                </div>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] ml-1">Management Suite</p>
            </div>

            <nav class="flex-1 px-6 space-y-1.5 overflow-y-auto mt-4">
                <a href="{{ route('admin.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Overview
                </a>
                
                <div class="pt-4 pb-2">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-4">Core Management</p>
                </div>

                <a href="{{ route('admin.services.index') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.618.309a6 6 0 01-3.86.517l-2.387-.477a2 2 0 00-1.022.547l-1.168 1.168a2 2 0 000 2.828l1.168 1.168a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.618-.309a6 6 0 013.86-.517l2.387.477a2 2 0 001.022-.547l1.168-1.168a2 2 0 000-2.828l-1.168-1.168z"></path></svg>
                    Services
                </a>
                <a href="{{ route('admin.trainers.index') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('admin.trainers.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Trainers
                </a>
                <a href="{{ route('admin.timeslots.index') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('admin.timeslots.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Master Schedule
                </a>
                <a href="{{ route('admin.gym.show') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('admin.gym.*') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Gym Details
                </a>
                <a href="{{ route('profile.show') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    My Profile
                </a>
                <a href="{{ route('profile.api-tokens') }}" class="nav-link flex items-center px-4 py-3 text-gray-500 {{ request()->routeIs('profile.api-tokens') ? 'active' : '' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zM15 7V5a3 3 0 10-6 0v2m6 0h-6"/></svg>
                    Developer Integrations
                </a>
            </nav>

            <div class="p-8 border-t border-gray-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-semibold text-red-500 hover:bg-red-50 rounded-xl transition-all">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 py-5 px-10 flex justify-between items-center z-10">
                <div class="flex items-center space-x-4">
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">@yield('title', 'Dashboard')</h2>
                </div>
                <div class="flex items-center space-x-6">
                    <button class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    <div class="flex items-center space-x-4 bg-gray-50 py-1.5 pl-4 pr-1.5 rounded-full border border-gray-100">
                        <div class="text-right">
                            <p class="text-xs font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-[9px] text-purple-600 font-extrabold uppercase tracking-widest">Admin</p>
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
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
