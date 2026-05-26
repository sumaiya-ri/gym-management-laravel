<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Failed - GlowGym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fff5f7; }
        .bg-glow { background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%); }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full mx-auto bg-white rounded-[3rem] border border-pink-50 shadow-xl shadow-pink-100/30 p-10 text-center space-y-6 flex flex-col items-center">
        <!-- Failure Icon -->
        <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center border-4 border-red-100 animate-pulse">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
        </div>

        <div class="space-y-2">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Payment Declined</h2>
            <p class="text-gray-500 text-sm font-medium">We were unable to process your subscription payment. No charges were made, and your gym portal remains inactive.</p>
        </div>

        <div class="w-full bg-red-50/50 border border-red-100 rounded-2xl p-6 text-left space-y-3 text-xs font-semibold text-red-700">
            <h4 class="font-extrabold uppercase tracking-widest text-[10px] text-red-800">Troubleshooting tips:</h4>
            <ul class="list-disc list-inside space-y-1">
                <li>Verify your cardholder name, card number, expiry, and CVV are correct.</li>
                <li>Make sure you enter a valid CVV (not 999 which triggers simulated decline).</li>
                <li>Ensure the card has sufficient credit/balance for the chosen plan.</li>
            </ul>
        </div>

        <div class="pt-4 w-full flex flex-col space-y-3">
            <a href="{{ route('saas.checkout', $gym->id) }}" class="block w-full py-4 text-center bg-gradient-to-r from-red-500 to-pink-600 text-white font-black rounded-xl hover:from-red-600 hover:to-pink-700 transition-all shadow-lg shadow-pink-100 text-xs uppercase tracking-[0.2em]">
                Try Again
            </a>
            <a href="{{ route('saas.pricing') }}" class="block text-center text-xs font-bold text-gray-400 uppercase tracking-widest hover:text-gray-600 transition-colors">Select Different Plan</a>
        </div>
    </div>
</body>
</html>
