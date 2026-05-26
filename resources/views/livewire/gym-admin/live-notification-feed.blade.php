<div wire:poll.10s class="card p-6 space-y-6 bg-white border border-gray-100 shadow-sm rounded-3xl mt-6">
    <div class="flex items-center justify-between border-b border-gray-50 pb-4">
        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest flex items-center">
            <span class="relative flex h-2 w-2 mr-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Live Activity Feed
        </h3>
        <span class="text-[9px] font-extrabold text-gray-400 uppercase tracking-wider bg-gray-50 px-2 py-0.5 rounded-md">
            Auto Refresh
        </span>
    </div>

    <!-- Feed Items -->
    <div class="flow-root">
        <ul role="list" class="-mb-8">
            @forelse($activities as $act)
                <li>
                    <div class="relative pb-8">
                        @if(!$loop->last)
                            <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-100" aria-hidden="true"></span>
                        @endif
                        <div class="relative flex space-x-3 items-start">
                            <div>
                                <span class="h-8 w-8 rounded-full flex items-center justify-center {{ $act['badge_class'] }}">
                                    {!! $act['icon'] !!}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0 pt-1.5">
                                <p class="text-xs font-extrabold text-slate-900 tracking-tight">
                                    {{ $act['title'] }}
                                </p>
                                <p class="text-[11px] text-gray-500 font-medium mt-0.5 leading-relaxed">
                                    {{ $act['description'] }}
                                </p>
                                <span class="text-[9px] font-bold text-slate-400 uppercase mt-1 block">
                                    {{ $act['timestamp']->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </li>
            @empty
                <div class="text-center py-6">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">No recent gym activities</p>
                    <p class="text-[9px] text-gray-300 mt-1">Activities will appear as bookings are created.</p>
                </div>
            @endforelse
        </ul>
    </div>
</div>
