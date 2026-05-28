@extends('layouts.admin')

@section('title', 'Edit Trainer')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <form action="{{ route('admin.trainers.update', $trainer->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Trainer Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $trainer->name) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. John Doe" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Trainer Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $trainer->user->email ?? '') }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. trainer@gymsaas.com" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="specialization" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Specialization</label>
                    <input type="text" name="specialization" id="specialization" value="{{ old('specialization', $trainer->specialization) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. Yoga Specialist, Strength Coach">
                    @error('specialization') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Password</label>
                    <input type="password" name="password" id="password" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="Leave blank to keep current password">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label for="hourly_rate" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Hourly Rate ($)</label>
                        <input type="number" step="0.01" name="hourly_rate" id="hourly_rate" value="{{ old('hourly_rate', $trainer->hourly_rate) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. 50.00">
                        @error('hourly_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Status</label>
                        <select name="status" id="status" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4">
                            <option value="active" {{ old('status', $trainer->status) === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $trainer->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-purple-600 text-white font-bold py-4 rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200">
                        Update Trainer
                    </button>
                    <a href="{{ route('admin.trainers.index') }}" class="block text-center text-sm font-bold text-gray-400 uppercase tracking-widest mt-4 hover:text-gray-600 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
