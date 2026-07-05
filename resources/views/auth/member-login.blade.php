<x-guest-layout>
    <!-- Back Button -->
    <div class="fixed top-8 left-8 z-50">
        <a href="{{ route('login') }}" class="flex items-center space-x-2 text-sm font-bold text-gray-500 hover:text-purple-600 transition-colors bg-white/80 backdrop-blur px-4 py-2 rounded-full border border-gray-100 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back</span>
        </a>
    </div>

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-purple-50 to-indigo-50">
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-3xl border border-purple-100">
            <div class="text-center mb-8">
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-purple-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Member Sign In</h2>
                <p class="text-sm text-gray-500 mt-2 font-medium">Access your <span class="text-purple-600 font-bold">GymGlow</span> member portal</p>
            </div>

            <x-validation-errors class="mb-4" />

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-sm text-red-700 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email -->
                <div>
                    <x-label for="email" value="{{ __('Email Address') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1" />
                    <x-input id="email" class="block w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl px-4 py-3 bg-white text-gray-700" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="john@example.com" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-label for="password" value="{{ __('Password') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1" />
                    <div class="relative">
                        <x-input id="password" class="block w-full border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-xl pl-4 pr-10 py-3 bg-white text-gray-700" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                        <button type="button" onclick="togglePasswordVisibility('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between mt-4">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" />
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-purple-600 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit" class="flex items-center justify-center gap-2 w-full py-4 bg-gray-900 hover:bg-gray-800 active:scale-95 text-white font-bold rounded-2xl shadow-lg transition-all">
                        <span>Sign In</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                        </svg>
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold uppercase tracking-wider">or</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Google OAuth Button -->
                <div>
                    <a href="{{ route('auth.google.redirect') }}" class="flex items-center justify-center gap-3 w-full py-4 border border-gray-300 rounded-2xl font-bold text-gray-700 bg-white hover:bg-gray-50 active:scale-95 transition-all shadow-sm">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>
                </div>

                <div class="mt-6 text-center text-sm font-medium text-gray-500">
                    Not a member yet? <a href="{{ route('member.register') }}" class="text-purple-600 font-bold hover:underline">Register Here</a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
