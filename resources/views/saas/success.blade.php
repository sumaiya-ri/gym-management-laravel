<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Success - GlowGym</title>
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
<body class="antialiased min-h-screen flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full mx-auto bg-white rounded-[3rem] border border-pink-50 shadow-xl shadow-pink-100/30 p-10 text-center space-y-6 flex flex-col items-center">
        <!-- Success Icon -->
        <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center border-4 border-emerald-100 animate-bounce">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </div>

        <div class="space-y-2">
            <h2 class="text-3xl font-black text-gray-900 tracking-tight">Subscription Active!</h2>
            <p class="text-gray-500 text-sm font-medium">Your Gym Portal has been successfully provisioned. Welcome to the GlowGym network!</p>
        </div>

        <!-- Details block -->
        <div class="w-full bg-pink-50/50 border border-pink-100 rounded-2xl p-6 text-left space-y-4 text-xs font-bold text-gray-700">
            <div class="flex justify-between items-center">
                <span class="text-gray-400 uppercase tracking-widest text-[10px]">Gym Name</span>
                <span class="text-gray-900">{{ $gym->name }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400 uppercase tracking-widest text-[10px]">Active Plan</span>
                <span class="text-pink-600 uppercase tracking-wider">{{ $gym->subscription_plan }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400 uppercase tracking-widest text-[10px]">Transaction ID</span>
                <span class="font-mono text-indigo-700 bg-white px-2.5 py-0.5 rounded border border-pink-100">{{ $gym->subscription_transaction_id }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-gray-400 uppercase tracking-widest text-[10px]">Expires At</span>
                <span class="text-gray-900">{{ \Carbon\Carbon::parse($gym->subscription_expires_at)->format('M d, Y') }}</span>
            </div>
        </div>

        <div class="pt-4 w-full">
            <a href="{{ route('dashboard') }}" class="block w-full py-4 text-center bg-gradient-to-r from-pink-500 to-indigo-600 text-white font-black rounded-xl hover:from-pink-600 hover:to-indigo-700 transition-all shadow-lg shadow-pink-100 text-xs uppercase tracking-[0.2em]">
                Go to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
