@extends('layouts.admin')

@section('title', 'Gym Details')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Gym Configuration</h3>
            <p class="text-sm text-gray-500 mt-1">Manage and view your gym profile settings and contact details.</p>
        </div>
        <a href="{{ route('admin.gym.edit') }}" class="bg-purple-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
            </svg>
            Edit Details
        </a>
    </div>

    <!-- Gym Profile Info Card -->
    <div class="card p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Gym Branding / Icon Column -->
        <div class="flex flex-col items-center justify-center border-b md:border-b-0 md:border-r border-gray-100 pb-8 md:pb-0 md:pr-8">
            <div class="w-24 h-24 bg-gradient-to-br from-purple-500 to-indigo-600 text-white rounded-3xl flex items-center justify-center font-extrabold text-4xl shadow-xl shadow-purple-100 mb-4">
                {{ strtoupper(substr($gym->name, 0, 1)) }}
            </div>
            <h2 class="font-extrabold text-2xl text-gray-800 text-center">{{ $gym->name }}</h2>
            <span class="inline-flex items-center px-3 py-1 bg-purple-50 text-purple-600 rounded-full text-xs font-bold uppercase tracking-widest mt-2">
                Active Gym
            </span>
        </div>

        <!-- Details Column -->
        <div class="col-span-2 space-y-6">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Contact & Information</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Gym Name</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $gym->name }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Email Address</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $gym->email }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Phone Number</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $gym->phone ?: 'Not Provided' }}</span>
                </div>

                <div>
                    <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Physical Address</span>
                    <span class="text-sm font-semibold text-gray-800">{{ $gym->address ?: 'Not Provided' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gym Stats Section -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="card p-6 flex items-center space-x-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.618.309a6 6 0 01-3.86.517l-2.387-.477a2 2 0 00-1.022.547l-1.168 1.168a2 2 0 000 2.828l1.168 1.168a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.618-.309a6 6 0 013.86-.517l2.387.477a2 2 0 001.022-.547l1.168-1.168a2 2 0 000-2.828l-1.168-1.168z"></path>
                </svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Services</span>
                <span class="text-xl font-extrabold text-gray-800">{{ $gym->services()->count() }} Available</span>
            </div>
        </div>

        <div class="card p-6 flex items-center space-x-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Trainers</span>
                <span class="text-xl font-extrabold text-gray-800">{{ $gym->trainers()->count() }} Active</span>
            </div>
        </div>

        <div class="card p-6 flex items-center space-x-4">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Scheduled Classes</span>
                <span class="text-xl font-extrabold text-gray-800">{{ $gym->timeslots()->count() }} Total</span>
            </div>
        </div>
    </div>
</div>
@endsection
