<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-purple-50 to-indigo-50">
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-3xl border border-purple-100">
            <div class="text-center mb-8">
                <div class="bg-gradient-to-br from-purple-500 to-indigo-600 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-purple-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Security Verification</h2>
                <p class="text-sm text-gray-500 mt-2 font-medium">Please verify your identity to access the dashboard</p>
            </div>

            <!-- Validation Errors -->
            <x-validation-errors class="mb-4" />

            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-xl text-sm text-emerald-700 font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-xl text-sm text-red-700 font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Verification form -->
            <form method="POST" action="{{ route('auth.otp.verify.post') }}" class="space-y-6">
                @csrf

                <div>
                    <x-label for="otp" value="{{ __('Enter 6-Digit Verification Code') }}" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2 text-center" />
                    
                    <div class="flex justify-center">
                        <x-input id="otp" class="block w-full text-center text-3xl font-extrabold tracking-[0.3em] border-gray-200 focus:border-purple-500 focus:ring-purple-500 rounded-2xl px-4 py-3 bg-white text-gray-800" 
                            type="text" 
                            name="otp" 
                            required 
                            autofocus 
                            autocomplete="off" 
                            maxlength="6" 
                            pattern="[0-9]{6}" 
                            placeholder="000000" />
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex items-center justify-center gap-2 w-full py-4 bg-purple-600 hover:bg-purple-700 active:scale-95 text-white font-bold rounded-2xl shadow-lg shadow-purple-100 transition-all">
                        <span>Verify and Login</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Resend form -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <form id="resend-form" method="POST" action="{{ route('auth.otp.resend') }}">
                    @csrf
                    <p class="text-sm text-gray-500 font-medium">
                        Didn't receive the code? 
                        <button type="submit" id="resend-btn" class="text-purple-600 font-bold hover:underline disabled:opacity-50 disabled:no-underline disabled:cursor-not-allowed">
                            Resend Code
                        </button>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Client-side Resend Countdown Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const resendBtn = document.getElementById('resend-btn');
            const cooldownKey = 'otp_cooldown_expiry';
            const cooldownDuration = 60 * 1000; // 60 seconds

            function startTimer(expiryTime) {
                resendBtn.disabled = true;

                const timerInterval = setInterval(function () {
                    const now = new Date().getTime();
                    const timeLeft = expiryTime - now;

                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        resendBtn.disabled = false;
                        resendBtn.textContent = 'Resend Code';
                        localStorage.removeItem(cooldownKey);
                    } else {
                        const seconds = Math.ceil(timeLeft / 1000);
                        resendBtn.textContent = `Resend in ${seconds}s`;
                    }
                }, 1000);
            }

            // Check if there is an active cooldown stored
            const savedExpiry = localStorage.getItem(cooldownKey);
            const now = new Date().getTime();

            if (savedExpiry && savedExpiry > now) {
                startTimer(parseInt(savedExpiry));
            }

            // Handle resend click to trigger local timer
            document.getElementById('resend-form').addEventListener('submit', function () {
                const expiryTime = new Date().getTime() + cooldownDuration;
                localStorage.setItem(cooldownKey, expiryTime);
            });
        });
    </script>
</x-guest-layout>
