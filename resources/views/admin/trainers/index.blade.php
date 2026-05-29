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
                    <span class="ml-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Joined {{ $trainer->created_at->format('M Y') }}</span>
                    
                    <div class="ml-auto flex items-center space-x-2">
                        <a href="{{ route('admin.trainers.edit', $trainer->id) }}" class="p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all" title="Edit Trainer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </a>
                        <form action="{{ route('admin.trainers.destroy', $trainer->id) }}" method="POST" onsubmit="showDeleteModal(event, 'Are you sure you want to delete this trainer? This will also remove their associated user account.');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Trainer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
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
