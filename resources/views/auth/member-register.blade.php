<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-purple-50 to-indigo-50">
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-3xl border border-purple-100">
            <div class="text-center mb-8">
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-purple-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Create Account</h2>
                <p class="text-sm text-gray-500 mt-2 font-medium">Join <span class="text-purple-600 font-bold">GymGlow</span> and start your journey</p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('member.register') }}">
                @csrf

                <div>
                    <x-label for="name" value="{{ __('Full name') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1" />
                    <x-input id="name" class="block mt-1 w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl px-4 py-3" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Jane Doe" />
                </div>

                <div class="mt-4">
                    <x-label for="email" value="{{ __('Email Address') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1" />
                    <x-input id="email" class="block mt-1 w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl px-4 py-3" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="jane@example.com" />
                </div>

                <div class="mt-4">
                    <x-label for="gym_id" value="{{ __('Select Your Gym') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1" />
                    <select id="gym_id" name="gym_id" class="block mt-1 w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl px-4 py-3 bg-white text-gray-700" required>
                        <option value="" disabled selected>Select a gym...</option>
                        @foreach($gyms as $gym)
                            <option value="{{ $gym->id }}">{{ $gym->name }} - {{ $gym->address }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <x-label for="password" value="{{ __('Password') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1" />
                    <x-input id="password" class="block mt-1 w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl px-4 py-3" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                <div class="mt-4">
                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1" />
                    <x-input id="password_confirmation" class="block mt-1 w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl px-4 py-3" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
                </div>

                <div class="mt-8">
                    <button type="submit" style="background-color: #7c3aed; color: white;" class="w-full py-4 rounded-2xl font-bold shadow-lg shadow-purple-200 hover:opacity-90 transition-all active:scale-95 focus:outline-none">
                        Create Account
                    </button>
                </div>

                <div class="mt-6 text-center text-sm font-medium text-gray-500">
                    Already have an account? <a href="{{ route('login') }}" class="text-purple-600 font-bold hover:underline">Sign In</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
