<x-guest-layout>
    <div class="min-h-screen bg-gradient-to-br from-pink-50 to-indigo-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <div class="inline-flex items-center space-x-2 mb-6">
                <div class="bg-gradient-to-r from-pink-500 to-indigo-600 p-2 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-gray-900">Glow<span class="text-pink-500">Gym</span></span>
            </div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Enterprise Sign Up</h2>
            <p class="mt-2 text-sm text-gray-500 font-medium">
                Subscribe to the <span class="text-pink-600 font-bold uppercase tracking-wider">{{ $plan }} Plan</span> to get started.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl shadow-pink-100/50 sm:rounded-[2rem] border border-pink-50 sm:px-10">
                <x-validation-errors class="mb-4" />

                <form method="POST" action="{{ route('register.enterprise.post') }}" class="space-y-6">
                    @csrf
                    <input type="hidden" name="plan" value="{{ $plan }}">

                    <div>
                        <x-label for="name" value="Admin Name" class="font-bold text-xs uppercase tracking-wider text-gray-400" />
                        <x-input id="name" class="block mt-1 w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" name="name" :value="old('name')" placeholder="e.g. Alisha Hendrick" required autofocus autocomplete="name" />
                    </div>

                    <div>
                        <x-label for="email" value="Admin Email" class="font-bold text-xs uppercase tracking-wider text-gray-400" />
                        <x-input id="email" class="block mt-1 w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="email" name="email" :value="old('email')" placeholder="e.g. alisha@gmail.com" required autocomplete="username" />
                    </div>

                    <div>
                        <x-label for="gym_name" value="Gym / Studio Name" class="font-bold text-xs uppercase tracking-wider text-gray-400" />
                        <x-input id="gym_name" class="block mt-1 w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" name="gym_name" :value="old('gym_name')" placeholder="e.g. GlowGym Pilates" required />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <x-label for="phone" value="Gym Phone" class="font-bold text-xs uppercase tracking-wider text-gray-400" />
                            <x-input id="phone" class="block mt-1 w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" name="phone" :value="old('phone')" placeholder="Phone" />
                        </div>
                        <div>
                            <x-label for="address" value="Gym Address" class="font-bold text-xs uppercase tracking-wider text-gray-400" />
                            <x-input id="address" class="block mt-1 w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" name="address" :value="old('address')" placeholder="Address" />
                        </div>
                    </div>

                    <div>
                        <x-label for="password" value="Password" class="font-bold text-xs uppercase tracking-wider text-gray-400" />
                        <x-input id="password" class="block mt-1 w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 characters" />
                    </div>

                    <div>
                        <x-label for="password_confirmation" value="Confirm Password" class="font-bold text-xs uppercase tracking-wider text-gray-400" />
                        <x-input id="password_confirmation" class="block mt-1 w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-gradient-to-r from-pink-500 to-indigo-600 text-white font-black py-4 rounded-xl hover:from-pink-600 hover:to-indigo-700 transition-all shadow-lg shadow-pink-100 flex items-center justify-center text-xs uppercase tracking-[0.2em]">
                            Continue to Payment
                        </button>
                    </div>

                    <div class="text-center text-xs text-gray-500 font-medium">
                        Already have an account? <a href="{{ route('login') }}" class="text-pink-500 font-bold hover:underline">Sign In</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>
