@extends('layouts.super-admin')

@section('title', 'SaaS Platform Overview')
@section('subtitle', 'Monitor gym signups, active subscriptions, and aggregate SaaS earnings')

@section('content')
@php
    $starterCount = \App\Models\Gym::where('subscription_plan', 'Starter')->count();
    $professionalCount = \App\Models\Gym::where('subscription_plan', 'Professional')->count();
    $enterpriseCount = \App\Models\Gym::where('subscription_plan', 'Enterprise')->count();
@endphp
<div x-data="{
    totalGyms: {{ $totalGyms }},
    totalMembers: {{ $totalMembers }},
    totalTrainers: {{ $totalTrainers }},
    totalBookings: {{ $totalBookings }},
    totalRevenue: {{ $totalRevenue }},
    activeSubs: {{ $activeSubscriptions }},
    expiredSubs: {{ $expiredSubscriptions }},
    starterCount: {{ $starterCount }},
    professionalCount: {{ $professionalCount }},
    enterpriseCount: {{ $enterpriseCount }},
    isLoading: false,
    showError: false,
    errorMessage: '',

    get totalPlans() {
        return this.starterCount + this.professionalCount + this.enterpriseCount;
    },
    get starterPct() {
        return this.totalPlans > 0 ? Math.round((this.starterCount / this.totalPlans) * 100) : 0;
    },
    get professionalPct() {
        return this.totalPlans > 0 ? Math.round((this.professionalCount / this.totalPlans) * 100) : 0;
    },
    get enterprisePct() {
        return this.totalPlans > 0 ? Math.round((this.enterpriseCount / this.totalPlans) * 100) : 0;
    },

    refreshAnalytics() {
        this.isLoading = true;
        this.showError = false;

        axios.get('/super-admin/analytics')
            .then(response => {
                const data = response.data;
                this.totalGyms = data.total_gyms;
                this.totalMembers = data.total_members;
                this.totalTrainers = data.total_trainers;
                this.totalBookings = data.total_bookings;
                this.totalRevenue = data.total_revenue;
                this.activeSubs = data.active_subs;
                this.expiredSubs = data.expired_subs;
                this.starterCount = data.starter_count;
                this.professionalCount = data.professional_count;
                this.enterpriseCount = data.enterprise_count;
                this.isLoading = false;
            })
            .catch(error => {
                console.error(error);
                this.isLoading = false;
                this.errorMessage = error.response && error.response.data && error.response.data.message
                    ? error.response.data.message
                    : 'Failed to refresh analytics. Please check your credentials.';
                this.showError = true;
            });
    }
}" x-on:revenue-updated.window="totalRevenue = $event.detail.totalRevenue" class="space-y-10">

    <!-- Refresh Bar & Error Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <div class="space-y-1">
            <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
                <span :class="isLoading ? 'bg-sky-500 animate-ping' : 'bg-emerald-500'" class="w-2.5 h-2.5 rounded-full mr-3 transition-colors"></span>
                System Performance Metrics
            </h3>
            <p class="text-xs text-slate-400 font-medium">Platform-wide analytics cache. Click refresh to load real-time updates.</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div x-show="showError" x-transition class="bg-rose-50 border border-rose-100 text-rose-700 text-xs font-semibold px-4 py-2.5 rounded-xl flex items-center shadow-sm">
                <svg class="w-4 h-4 text-rose-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span x-text="errorMessage"></span>
            </div>

            <button @click="refreshAnalytics()" :disabled="isLoading" class="btn-cyan flex items-center space-x-2 font-bold shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                <svg :class="isLoading ? 'animate-spin' : ''" class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89H18"/>
                </svg>
                <span x-text="isLoading ? 'Refreshing...' : 'Refresh Analytics'">Refresh Analytics</span>
            </button>
        </div>
    </div>
    
    <!-- Row 1: Real-time Stats Cards (Livewire) -->
    <livewire:super-admin-dashboard-stats 
        :totalGyms="$totalGyms" 
        :totalMembers="$totalMembers" 
        :totalTrainers="$totalTrainers" 
        :totalBookings="$totalBookings" 
    />

    <!-- Row 2: Live Revenue Counter & Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Live Revenue (Livewire) -->
        <div class="lg:col-span-1">
            <livewire:live-revenue-counter :totalRevenue="$totalRevenue" />
        </div>
        
        <!-- Live Subscription Distribution (Livewire) -->
        <div class="lg:col-span-2">
            <livewire:subscription-stats :active="$activeSubscriptions" :expired="$expiredSubscriptions" />
        </div>
    </div>

    <!-- Row 3: Recent Gym Registrations -->
    <div class="card p-8 space-y-6">
        <h3 class="text-sm font-extrabold text-slate-800 uppercase tracking-wider flex items-center">
            <span class="w-2.5 h-2.5 bg-sky-500 rounded-full mr-3 animate-pulse"></span>
            Recent Gym Registrations
        </h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs font-semibold text-slate-600">
                <thead>
                    <tr class="text-[10px] text-slate-400 uppercase tracking-widest border-b border-slate-100 pb-3">
                        <th class="pb-3">Gym Details</th>
                        <th class="pb-3">Plan</th>
                        <th class="pb-3">Status</th>
                        <th class="pb-3">Expiry Date</th>
                        <th class="pb-3">Transaction ID</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($recentGyms as $gym)
                        <tr class="group">
                            <td class="py-4">
                                <div class="font-bold text-slate-900 text-sm mb-0.5">{{ $gym->name }}</div>
                                <div class="text-[10px] text-slate-400">{{ $gym->email }}</div>
                            </td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full uppercase tracking-wider font-extrabold text-[9px]">
                                    {{ $gym->subscription_plan ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-4">
                                <span class="px-2.5 py-1 text-[9px] uppercase tracking-wider font-extrabold rounded-full {{ $gym->subscription_status === 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-500' }}">
                                    {{ ucfirst($gym->subscription_status) }}
                                </span>
                            </td>
                            <td class="py-4 text-slate-500">
                                {{ $gym->subscription_expires_at ? \Carbon\Carbon::parse($gym->subscription_expires_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="py-4 font-mono text-indigo-600">
                                {{ $gym->subscription_transaction_id ?? 'N/A' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 font-bold uppercase tracking-widest text-[10px]">
                                No gyms registered yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
