<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>GlowGym SaaS Portal — @yield('title', 'Platform Management')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }
        .sidebar { background: #0f172a; color: #94a3b8; }
        .nav-link { transition: all 0.2s ease; border-radius: 0.75rem; font-weight: 500; }
        .nav-link:hover { background-color: #1e293b; color: #f8fafc; }
        .nav-link.active { background-color: #38bdf8; color: #0f172a; }
        .card { background: #ffffff; border-radius: 1.25rem; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.02); transition: all 0.3s ease; }
        .card:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.04); }
        .btn-cyan { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); color: white; padding: 0.6rem 1.25rem; border-radius: 0.75rem; font-weight: 600; font-size: 0.8rem; transition: all 0.2s; display: inline-block; }
        .btn-cyan:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14,165,233,0.3); }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="sidebar w-72 flex-shrink-0 flex flex-col justify-between">
            <div>
                <!-- Brand Logo -->
                <div class="p-8">
                    <div class="flex items-center space-x-3 mb-2">
                        <div class="bg-gradient-to-br from-sky-400 to-indigo-600 p-2.5 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-xl font-extrabold tracking-tight text-white">Glow<span class="text-sky-400">SaaS</span></span>
                    </div>
                    <p class="text-[9px] text-gray-500 font-bold uppercase tracking-[0.22em] ml-1">SaaS Administration</p>
                </div>

                <!-- Nav List -->
                <nav class="px-6 space-y-1.5 mt-4">
                    <a href="{{ route('super-admin.dashboard') }}" class="nav-link flex items-center px-4 py-3 text-slate-400 {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Overview Dashboard
                    </a>
                    <a href="{{ route('profile.api-tokens') }}" class="nav-link flex items-center px-4 py-3 text-slate-400 {{ request()->routeIs('profile.api-tokens') ? 'active' : '' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2v2a2 2 0 01-2 2H9a2 2 0 01-2-2V9a2 2 0 012-2h6zM15 7V5a3 3 0 10-6 0v2m6 0h-6"/></svg>
                        Developer Integrations
                    </a>
                </nav>
            </div>

            <!-- Sign Out -->
            <div class="p-8 border-t border-slate-800">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full px-4 py-3 text-sm font-semibold text-rose-400 hover:bg-slate-800 rounded-xl transition-all">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-slate-100 py-5 px-10 flex justify-between items-center z-10">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">@yield('title', 'Platform Dashboard')</h2>
                    <p class="text-xs text-slate-400 font-medium">@yield('subtitle', 'Real-time multi-tenant platform metrics')</p>
                </div>
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-4 bg-slate-50 py-1.5 pl-4 pr-1.5 rounded-full border border-slate-100">
                        <div class="text-right">
                            <p class="text-xs font-bold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-[9px] text-sky-600 font-extrabold uppercase tracking-widest">Super Admin</p>
                        </div>
                        <div class="w-9 h-9 bg-sky-600 text-white flex items-center justify-center rounded-full font-bold shadow-md">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Scrollable View -->
            <main class="flex-1 overflow-y-auto p-10">
                @if(session('success'))
                    <div class="mb-8 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                        <div class="bg-emerald-500 p-1.5 rounded-lg mr-3">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <span class="font-semibold text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
