@extends('layouts.admin')

@section('title', 'Service Management')

@section('content')
<div class="flex justify-between items-end mb-10">
    <div>
        <p class="text-[10px] font-black text-purple-600 uppercase tracking-[0.2em] mb-2">Inventory</p>
        <h3 class="text-3xl font-black text-gray-900 tracking-tight">Gym Services</h3>
    </div>
    <a href="{{ route('admin.services.create') }}" class="btn-purple flex items-center shadow-lg shadow-purple-100">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
        New Service
    </a>
</div>

<div class="card overflow-hidden border-none shadow-xl shadow-gray-100">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Service Details</th>
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Duration</th>
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($services as $service)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-10 py-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center mr-5 group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"></path></svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 text-lg tracking-tight">{{ $service->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1 max-w-md">{{ $service->description }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-10 py-6 text-center">
                        <span class="inline-flex items-center px-4 py-1.5 bg-indigo-50 text-indigo-600 rounded-full text-[11px] font-black uppercase tracking-wider">
                            {{ $service->duration }} MINS
                        </span>
                    </td>
                    <td class="px-10 py-6 text-center">
                        @if($service->status === 'active')
                            <span class="inline-flex items-center px-4 py-1.5 bg-emerald-50 text-emerald-600 rounded-full text-[11px] font-black uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-4 py-1.5 bg-red-50 text-red-600 rounded-full text-[11px] font-black uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2"></span> Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-10 py-6 text-right">
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('admin.services.edit', $service->id) }}" class="p-2 text-gray-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-all" title="Edit Service">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 00-2 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" onsubmit="showDeleteModal(event, 'Are you sure you want to delete this service?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete Service">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-10 py-24 text-center">
                        <div class="max-w-xs mx-auto">
                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-[10px] mb-4">No Services Found</p>
                            <a href="{{ route('admin.services.create') }}" class="text-purple-600 font-bold hover:underline">Launch your first service</a>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($services->hasPages())
        <div class="px-10 py-5 border-t border-gray-100 bg-gray-50/20">
            {{ $services->links() }}
        </div>
    @endif
</div>
@endsection
