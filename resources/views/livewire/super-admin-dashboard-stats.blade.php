<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <!-- Gyms Card -->
    <div class="card p-6 flex items-center justify-between border-l-4 border-l-sky-500">
        <div>
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest mb-1">Total Gyms</p>
            <h4 class="text-3xl font-black text-slate-800" x-text="totalGyms">{{ $totalGyms }}</h4>
        </div>
        <div class="bg-sky-50 text-sky-500 p-3.5 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
        </div>
    </div>

    <!-- Members Card -->
    <div class="card p-6 flex items-center justify-between border-l-4 border-l-purple-500">
        <div>
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest mb-1">Total Members</p>
            <h4 class="text-3xl font-black text-slate-800" x-text="totalMembers">{{ $totalMembers }}</h4>
        </div>
        <div class="bg-purple-50 text-purple-500 p-3.5 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        </div>
    </div>

    <!-- Trainers Card -->
    <div class="card p-6 flex items-center justify-between border-l-4 border-l-pink-500">
        <div>
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest mb-1">Total Trainers</p>
            <h4 class="text-3xl font-black text-slate-800" x-text="totalTrainers">{{ $totalTrainers }}</h4>
        </div>
        <div class="bg-pink-50 text-pink-500 p-3.5 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        </div>
    </div>

    <!-- Bookings Card -->
    <div class="card p-6 flex items-center justify-between border-l-4 border-l-emerald-500">
        <div>
            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-widest mb-1">Total Bookings</p>
            <h4 class="text-3xl font-black text-slate-800" x-text="totalBookings">{{ $totalBookings }}</h4>
        </div>
        <div class="bg-emerald-50 text-emerald-500 p-3.5 rounded-2xl">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        </div>
    </div>
</div>
