@extends('layouts.admin')

@section('title', 'Edit Gym Details')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card p-8">
        <form action="{{ route('admin.gym.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Gym Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $gym->name) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. Super Fitness" required>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $gym->email) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. contact@superfitness.com" required>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Phone Number</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $gym->phone) }}" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. +1234567890">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-bold text-gray-700 uppercase tracking-widest mb-2">Physical Address</label>
                    <textarea name="address" id="address" rows="3" class="w-full border-gray-200 rounded-xl focus:ring-purple-500 focus:border-purple-500 shadow-sm p-4" placeholder="e.g. 123 Fitness Ave, Gym City">{{ old('address', $gym->address) }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4 flex flex-col space-y-4">
                    <button type="submit" class="w-full bg-purple-600 text-white font-bold py-4 rounded-xl hover:bg-purple-700 transition-colors shadow-lg shadow-purple-200">
                        Save Details
                    </button>
                    <a href="{{ route('admin.gym.show') }}" class="block text-center text-sm font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
