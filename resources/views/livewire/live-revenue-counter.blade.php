<div class="card p-8 bg-gradient-to-br from-indigo-900 to-slate-900 text-white relative overflow-hidden h-full flex flex-col justify-between">
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-sky-500 rounded-full opacity-10 blur-2xl"></div>
    
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <span class="text-[10px] text-sky-400 font-extrabold uppercase tracking-widest">Total SaaS Revenue</span>
            <button wire:click="refreshRevenue" class="text-sky-400 hover:text-white transition-colors p-1.5 bg-slate-800 rounded-lg hover:scale-105 active:scale-95 duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18"/></svg>
            </button>
        </div>

        <div class="space-y-1">
            <span class="text-xs font-semibold text-slate-400">ARR Platform Earnings</span>
            <div class="text-4xl font-black tracking-tight text-white flex items-baseline">
                <span x-text="'$' + Number(totalRevenue).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })">${{ number_format($totalRevenue, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="pt-8 border-t border-slate-800/80 flex items-center justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest">
        <span>Payment Gateway</span>
        <span class="text-emerald-400 flex items-center">
            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-2 animate-ping"></span>
            Online (IPG)
        </span>
    </div>
</div>
