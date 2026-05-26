@extends('layouts.admin')

@section('title', 'Manage Trainers')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Trainer Directory</h3>
        <p class="text-sm text-gray-500 mt-1">Manage your team of professional fitness trainers.</p>
    </div>
    <a href="{{ route('admin.trainers.create') }}" class="bg-purple-600 text-white font-bold py-3 px-6 rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
        Add Trainer
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($trainers as $trainer)
        <div class="card p-6 flex items-start space-x-6 relative group overflow-hidden">
            <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center font-bold text-2xl border-2 border-purple-100 group-hover:bg-purple-600 group-hover:text-white transition-all">
                {{ strtoupper(substr($trainer->name, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">{{ $trainer->name }}</h4>
                        <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mt-1">{{ $trainer->specialization ?? 'General Trainer' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Rate</p>
                        <p class="text-lg font-bold text-gray-800">${{ number_format($trainer->hourly_rate, 2) }}</p>
                    </div>
                </div>
                
                <div class="flex items-center mt-6 pt-4 border-t border-gray-50">
                    @if($trainer->status === 'active')
                        <span class="inline-flex items-center px-2 py-0.5 bg-green-50 text-green-600 rounded text-[10px] font-bold uppercase tracking-widest">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 bg-red-50 text-red-600 rounded text-[10px] font-bold uppercase tracking-widest">
                            Inactive
                        </span>
                    @endif
                    <span class="ml-auto text-[10px] font-bold text-gray-400 uppercase tracking-widest">Joined {{ $trainer->created_at->format('M Y') }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full card p-12 text-center text-gray-400 font-medium">
            No trainers found. <a href="{{ route('admin.trainers.create') }}" class="text-purple-600 font-bold hover:underline">Add your first trainer</a>
        </div>
    @endforelse
</div>
@endsection
