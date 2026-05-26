@extends('layouts.admin')

@section('title', 'Add New Service')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <form action="{{ route('admin.services.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Service Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. Yoga, HIIT, Pilates" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="Briefly describe what this service is about...">{{ old('description') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="duration" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Duration (Minutes)</label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration', 60) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" required>
                        @error('duration') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Status</label>
                        <select name="status" id="status" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-purple-600 text-white font-bold py-4 rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200">
                        Create Service
                    </button>
                    <a href="{{ route('admin.services.index') }}" class="block text-center text-sm font-bold text-gray-400 uppercase tracking-widest mt-4 hover:text-gray-600 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
