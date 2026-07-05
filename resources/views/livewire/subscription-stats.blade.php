<div class="card p-8 space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider">Subscription Distribution</h3>
        <div class="flex items-center space-x-4 text-xs font-bold text-slate-400">
            <span class="text-emerald-500 bg-emerald-50 px-2.5 py-1 rounded-full uppercase tracking-wider text-[9px]"><span x-text="activeSubs">{{ $active }}</span> Active</span>
            <span class="text-rose-500 bg-rose-50 px-2.5 py-1 rounded-full uppercase tracking-wider text-[9px]"><span x-text="expiredSubs">{{ $expired }}</span> Expired</span>
        </div>
    </div>

    @php
        $totalPlans = $starterCount + $professionalCount + $enterpriseCount;
        $starterPct = $totalPlans > 0 ? round(($starterCount / $totalPlans) * 100) : 0;
        $professionalPct = $totalPlans > 0 ? round(($professionalCount / $totalPlans) * 100) : 0;
        $enterprisePct = $totalPlans > 0 ? round(($enterpriseCount / $totalPlans) * 100) : 0;
    @endphp

    <div class="space-y-5">
        <!-- Starter Progress -->
        <div class="space-y-2">
            <div class="flex justify-between text-xs font-bold">
                <span class="text-slate-600">Starter Plan ($29/mo)</span>
                <span class="text-slate-800"><span x-text="starterCount">{{ $starterCount }}</span> (<span x-text="starterPct">{{ $starterPct }}</span>%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-gradient-to-r from-sky-400 to-sky-50 h-2 rounded-full" :style="'width: ' + starterPct + '%'" style="width: {{ $starterPct }}%"></div>
            </div>
        </div>

        <!-- Professional Progress -->
        <div class="space-y-2">
            <div class="flex justify-between text-xs font-bold">
                <span class="text-slate-600">Professional Plan ($59/mo)</span>
                <span class="text-slate-800"><span x-text="professionalCount">{{ $professionalCount }}</span> (<span x-text="professionalPct">{{ $professionalPct }}</span>%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 h-2 rounded-full" :style="'width: ' + professionalPct + '%'" style="width: {{ $professionalPct }}%"></div>
            </div>
        </div>

        <!-- Enterprise Progress -->
        <div class="space-y-2">
            <div class="flex justify-between text-xs font-bold">
                <span class="text-slate-600">Enterprise Plan ($99/mo)</span>
                <span class="text-slate-800"><span x-text="enterpriseCount">{{ $enterpriseCount }}</span> (<span x-text="enterprisePct">{{ $enterprisePct }}</span>%)</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2">
                <div class="bg-gradient-to-r from-pink-500 to-pink-600 h-2 rounded-full" :style="'width: ' + enterprisePct + '%'" style="width: {{ $enterprisePct }}%"></div>
            </div>
        </div>
    </div>
</div>
