@extends('layouts.admin')

@section('title', 'Schedule New Class')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card p-8">
        <form action="{{ route('admin.timeslots.store') }}" method="POST">
            @csrf
            
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="service_id" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Service</label>
                        <select name="service_id" id="service_id" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" required>
                            <option value="">Select Service</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->name }} ({{ $service->duration }} mins)</option>
                            @endforeach
                        </select>
                        @error('service_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="trainer_id" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Trainer</label>
                        <select name="trainer_id" id="trainer_id" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" required>
                            <option value="">Select Trainer</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                    {{ $trainer->name }}{{ $trainer->specialization ? ' (' . $trainer->specialization . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('trainer_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="date" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Date</label>
                        <input type="date" name="date" id="date" value="{{ old('date', date('Y-m-d')) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" required>
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="start_time" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Start Time</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" required>
                        @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">End Time</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" required>
                        @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="capacity" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Capacity (Max People)</label>
                        <input type="number" name="capacity" id="capacity" value="{{ old('capacity', 10) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" required>
                        @error('capacity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                    <button type="submit" class="w-full bg-purple-600 text-white font-bold py-4 rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200 flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Schedule Class
                    </button>
                    <a href="{{ route('admin.timeslots.index') }}" class="block text-center text-sm font-bold text-gray-400 uppercase tracking-widest mt-4 hover:text-gray-600 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
