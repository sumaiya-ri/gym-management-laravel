<div class="relative bg-gradient-to-r from-purple-100 to-indigo-50 text-gray-900 p-6 md:p-8 rounded-3xl overflow-hidden shadow-md shadow-purple-100/30 border border-purple-200/50 transition-all duration-300">
    <!-- Glassmorphism decorative blurry circles -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-purple-300/20 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-48 h-48 bg-indigo-300/20 rounded-full blur-2xl -ml-16 -mb-16 pointer-events-none"></div>

    <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6 z-10">
        <div class="flex items-start gap-4 flex-1">
            <!-- Dynamic motivation icon with lightning SVG -->
            <div class="w-12 h-12 bg-purple-200/60 flex items-center justify-center rounded-2xl flex-shrink-0 text-purple-600 shadow-inner">
                <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>

            <!-- Quote Text & Author -->
            <div class="space-y-2 flex-1">
                <p class="text-[9px] text-purple-600 font-extrabold uppercase tracking-[0.2em]">Daily Motivation</p>
                
                <!-- Display Quote Content -->
                <div wire:loading.remove wire:target="refreshQuote" class="transition-opacity duration-300">
                    <p class="text-sm md:text-base font-extrabold leading-relaxed italic text-purple-950">
                        "{{ $quoteText }}"
                    </p>
                    <p class="text-xs text-purple-700 font-bold mt-1">
                        — {{ $quoteAuthor }}
                    </p>
                </div>

                <!-- Skeleton loader state during refresh -->
                <div wire:loading wire:target="refreshQuote" class="space-y-3 animate-pulse">
                    <div class="h-3.5 bg-purple-200/40 rounded-md w-11/12 md:w-3/4"></div>
                    <div class="h-3 bg-purple-200/40 rounded-md w-1/4"></div>
                </div>
            </div>
        </div>

        <!-- Refresh Action Button -->
        <div class="flex-shrink-0 self-end md:self-center">
            <button wire:click="refreshQuote" class="flex items-center space-x-2 bg-white hover:bg-purple-50 active:scale-95 transition-all text-xs font-bold text-purple-700 px-4 py-2.5 rounded-full border border-purple-200 shadow-sm">
                <!-- Sync loop reload icon -->
                <svg wire:loading.class="animate-spin" wire:target="refreshQuote" class="w-4 h-4 text-purple-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89M9 11l3-3 3 3m0 0l-3 3-3-3"/>
                </svg>
                <span>New Quote</span>
            </button>
        </div>
    </div>
</div>
