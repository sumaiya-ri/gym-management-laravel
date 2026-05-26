<div class="space-y-8">
    <!-- Filter Controls Panel -->
    <div class="card p-6 bg-white border border-purple-50/50 shadow-md rounded-[2rem] space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <!-- Text Search Input -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2".5 d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    placeholder="Search class or trainer..." 
                    class="w-full pl-11 pr-4 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-xs font-bold text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                >
            </div>

            <!-- Category/Service Dropdown -->
            <div>
                <select 
                    wire:model.live="category" 
                    class="w-full px-4 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-xs font-bold text-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                >
                    <option value="">All Categories/Services</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Selector -->
            <div>
                <input 
                    type="date" 
                    wire:model.live="date" 
                    class="w-full px-4 py-3.5 bg-gray-50/50 border border-gray-100 rounded-2xl text-xs font-bold text-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all"
                >
            </div>
        </div>

        <!-- Eager Loading Indicator -->
        <div wire:loading class="w-full text-center py-2">
            <span class="inline-flex items-center text-xs font-extrabold text-purple-600 tracking-wider uppercase animate-pulse">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Filtering classes dynamically...
            </span>
        </div>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative">
        @forelse($classes as $class)
            @php
                $isBooked = in_array($class->id, $myBookedIds);
                $isFull   = $class->capacity <= 0;
                $duration = \Carbon\Carbon::parse($class->start_time)->diffInMinutes(\Carbon\Carbon::parse($class->end_time));
            @endphp
            <div class="card p-8 border-transparent hover:border-purple-100 group transition-all duration-300 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-start mb-6">
                        <span class="px-3 py-1 bg-purple-50 text-purple-700 text-[10px] font-extrabold uppercase tracking-widest rounded-full border border-purple-100">
                            {{ $class->service->name }}
                        </span>
                        <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            {{ $duration }} min
                        </span>
                    </div>

                    <h4 class="text-xl font-black text-gray-900 mb-4 tracking-tight">{{ $class->service->name }} Intermediate</h4>

                    <div class="space-y-3 mb-8">
                        <div class="flex items-center text-xs text-gray-500 font-medium">
                            <svg class="w-4 h-4 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            {{ $class->trainer->name }}
                        </div>
                        <div class="flex items-center text-xs text-gray-500 font-medium">
                            <svg class="w-4 h-4 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ \Carbon\Carbon::parse($class->date)->format('D, M d, Y') }}
                        </div>
                        <div class="flex items-center text-xs text-gray-500 font-medium">
                            <svg class="w-4 h-4 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ \Carbon\Carbon::parse($class->start_time)->format('h:i A') }}
                        </div>
                        
                        <!-- Reactive Seat Counter integration -->
                        <div class="flex items-center text-xs font-bold">
                            <svg class="w-4 h-4 mr-3 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <livewire:member.live-seat-availability :timeslotId="$class->id" :displayType="'badge'" :wire:key="'seats-badge-'.$class->id" />
                        </div>
                    </div>
                </div>

                <div>
                    @if($isBooked)
                        <button disabled class="w-full py-4 bg-emerald-50 text-emerald-600 font-black text-[10px] uppercase tracking-[0.2em] rounded-2xl flex items-center justify-center border border-emerald-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Booked
                        </button>
                    @else
                        <!-- Button type availability counter -->
                        <livewire:member.live-seat-availability :timeslotId="$class->id" :displayType="'button'" :wire:key="'seats-btn-'.$class->id" />
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full card p-20 text-center border-dashed border-2 border-gray-100">
                <div class="w-20 h-20 bg-purple-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No classes matched your search parameters</p>
                <p class="text-xs text-gray-300 mt-2">Adjust your filters or query to find available classes.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    <div class="mt-8">
        {{ $classes->links() }}
    </div>
</div>
