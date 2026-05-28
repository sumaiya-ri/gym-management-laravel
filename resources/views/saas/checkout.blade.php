<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SaaS Subscription Checkout - GlowGym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fff5f7; }
        .text-gradient { background: linear-gradient(to right, #ec4899, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .bg-glow { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col justify-between py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full mx-auto space-y-10">
        <!-- Logo Header -->
        <div class="text-center">
            <div class="inline-flex items-center space-x-2 mb-6">
                <div class="bg-glow p-2 rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <span class="text-2xl font-extrabold tracking-tight text-gray-900">Glow<span class="text-pink-500">Gym</span></span>
            </div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Complete Gym Subscription</h2>
            <p class="mt-2 text-sm text-gray-500 font-medium">Activate your multi-tenant SaaS portal access</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Plan Summary -->
            <div class="bg-white rounded-[2.5rem] border border-pink-50 p-8 shadow-xl shadow-pink-100/30 md:col-span-1 h-fit space-y-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Plan Details</h3>
                <div class="space-y-4">
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Gym / Studio</span>
                        <span class="text-base font-extrabold text-gray-900">{{ $gym->name }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Subscription</span>
                        <span class="text-lg font-black text-pink-600 uppercase tracking-wider">{{ $plan }} Plan</span>
                    </div>
                    <div class="pt-4 border-t border-gray-50 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Rate</span>
                        <span class="text-2xl font-black text-indigo-600">${{ number_format($price, 2) }}<span class="text-xs text-gray-400 font-bold">/mo</span></span>
                    </div>
                </div>
            </div>

            <!-- Payment Input -->
            <div class="bg-white rounded-[2.5rem] border border-pink-50 p-10 shadow-xl shadow-pink-100/30 md:col-span-2 space-y-6">
                @if(isset($stripeEnabled) && $stripeEnabled)
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Secure Online Payment</h3>

                    <!-- Stripe Sandbox Info Box -->
                    <div class="p-6 bg-indigo-50/70 border border-indigo-100 rounded-3xl flex items-start space-x-4">
                        <div class="bg-indigo-600 p-2.5 rounded-2xl text-white shadow-md shadow-indigo-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <div>
                            <p class="font-extrabold text-indigo-900 text-sm">Secure Payment with Stripe Sandbox</p>
                            <p class="text-xs text-indigo-700 mt-1 leading-relaxed">
                                You will be redirected to the secure Stripe Checkout portal to complete your transaction in **Test Mode**. No real money will be charged.
                            </p>
                            <p class="text-[11px] text-indigo-600 mt-2 font-semibold">
                                Acceptable test card: <span class="bg-indigo-100/80 px-2 py-0.5 rounded font-mono">4242 4242 4242 4242</span>
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('saas.process', $gym->id) }}" class="space-y-6">
                        @csrf
                        <div class="pt-4 flex flex-col space-y-4">
                            <button type="submit" class="w-full bg-glow text-white font-black py-4 rounded-xl hover:opacity-90 transition-all shadow-lg shadow-pink-100 flex items-center justify-center text-xs uppercase tracking-[0.2em]">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Confirm and Pay
                            </button>
                            <a href="{{ route('saas.pricing') }}" class="block text-center text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">Cancel</a>
                        </div>
                    </form>
                @else
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Simulated Credit Card Details</h3>

                    <!-- IPG Helper Alert -->
                    <div class="p-5 bg-pink-50 border border-pink-100 rounded-2xl flex items-start space-x-3">
                        <div class="bg-pink-500 p-1.5 rounded-lg text-white mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="font-bold text-pink-900 text-xs">Simulated payment environment</p>
                            <p class="text-[11px] text-pink-700 mt-1 leading-relaxed">
                                Use any valid 16-digit card and standard CVV (e.g. 123) for **successful** subscription.<br>
                                Enter CVV **999** to test **failed/declined** transactions.
                            </p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('saas.process', $gym->id) }}" class="space-y-6">
                        @csrf
                        <div>
                            <x-label for="cardholder_name" value="Cardholder Name" class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-2" />
                            <x-input id="cardholder_name" name="cardholder_name" class="block w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" value="{{ old('cardholder_name', $gym->name) }}" required placeholder="e.g. Alisha Hendrick" />
                        </div>

                        <div>
                            <x-label for="card_number" value="Card Number" class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-2" />
                            <x-input id="card_number" name="card_number" class="block w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" minlength="16" maxlength="16" required placeholder="4111222233334444" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-label for="expiry" value="Expiry Date (MM/YY)" class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-2" />
                                <x-input id="expiry" name="expiry" class="block w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" maxlength="5" required placeholder="12/28" />
                            </div>
                            <div>
                                <x-label for="cvv" value="CVV" class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-2" />
                                <x-input id="cvv" name="cvv" class="block w-full p-4 border-gray-200 focus:ring-pink-500 focus:border-pink-500 rounded-xl" type="text" minlength="3" maxlength="4" required placeholder="123" />
                            </div>
                        </div>

                        <div class="pt-4 flex flex-col space-y-4">
                            <button type="submit" class="w-full bg-gradient-to-r from-pink-500 to-indigo-600 text-white font-black py-4 rounded-xl hover:from-pink-600 hover:to-indigo-700 transition-all shadow-lg shadow-pink-100 flex items-center justify-center text-xs uppercase tracking-[0.2em]">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                Confirm & Subscribe
                            </button>
                            <a href="{{ route('saas.pricing') }}" class="block text-center text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">Cancel</a>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
