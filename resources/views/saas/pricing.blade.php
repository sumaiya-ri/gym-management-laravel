<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pricing Plans - GlowGym SaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fff5f7;
        }
        .text-gradient {
            background: linear-gradient(to right, #ec4899, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-glow {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
        }
        .btn-glow {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            transition: all 0.3s ease;
        }
        .btn-glow:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(236, 72, 153, 0.4);
        }
    </style>
</head>
<body class="antialiased text-gray-900 min-h-screen flex flex-col justify-between">
    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-50 fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-2">
                    <a href="/" class="flex items-center space-x-2">
                        <div class="bg-glow p-2 rounded-lg shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-gray-900">Glow<span class="text-pink-500">Gym</span></span>
                    </a>
                </div>
                <a href="{{ route('login') }}" class="text-sm font-bold text-gray-700 hover:text-pink-500 transition-colors">Sign In</a>
            </div>
        </div>
    </nav>

    <!-- Pricing Section -->
    <div class="relative pt-32 pb-20 flex-1 flex flex-col items-center justify-center">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center space-y-12">
            <!-- Header -->
            <div class="space-y-4">
                <h1 class="text-4xl md:text-6xl font-black text-gray-900 tracking-tight">Simple, Transparent <span class="text-gradient">Pricing</span></h1>
                <p class="text-lg text-gray-500 font-medium max-w-2xl mx-auto">Scale your ladies-only gym with our fully loaded subscription tiers. No hidden fees. Cancel anytime.</p>
            </div>

            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Starter Plan -->
                <div class="bg-white rounded-[2.5rem] border border-pink-50/50 p-10 flex flex-col justify-between shadow-xl shadow-pink-100/30 hover:scale-[1.02] transition-transform duration-300 relative overflow-hidden">
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-gray-900">Starter Plan</h3>
                        <p class="text-sm text-gray-400">Perfect for newly established pilates or yoga studios.</p>
                        <div class="flex items-baseline justify-center">
                            <span class="text-5xl font-black tracking-tight">${{ number_format($plans['Starter'], 0) }}</span>
                            <span class="text-gray-400 font-semibold text-sm ml-1">/mo</span>
                        </div>
                        <ul class="text-left space-y-4 border-t border-gray-50 pt-6">
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                1 Gym Location
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Up to 100 Members
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Basic Scheduler & CRM
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register.enterprise', ['plan' => 'Starter']) }}" class="mt-8 block w-full py-4 text-center text-pink-600 bg-pink-50 hover:bg-pink-100 transition-colors font-bold rounded-2xl text-xs uppercase tracking-wider">
                        Choose Starter
                    </a>
                </div>

                <!-- Professional Plan -->
                <div class="bg-white rounded-[2.5rem] border-2 border-pink-500 p-10 flex flex-col justify-between shadow-xl shadow-pink-200/50 hover:scale-[1.02] transition-transform duration-300 relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-pink-500 text-white font-extrabold text-[9px] uppercase tracking-widest px-5 py-2 rounded-bl-3xl">
                        Popular Choice
                    </div>
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-gray-900">Professional</h3>
                        <p class="text-sm text-gray-400">Great for rapidly growing women's fitness centers.</p>
                        <div class="flex items-baseline justify-center">
                            <span class="text-5xl font-black tracking-tight">${{ number_format($plans['Professional'], 0) }}</span>
                            <span class="text-gray-400 font-semibold text-sm ml-1">/mo</span>
                        </div>
                        <ul class="text-left space-y-4 border-t border-gray-50 pt-6">
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                3 Gym Locations
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Up to 500 Members
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Advanced Analytics
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Email & Queue Notifications
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register.enterprise', ['plan' => 'Professional']) }}" class="mt-8 block w-full py-4 text-center btn-glow text-white font-bold rounded-2xl text-xs uppercase tracking-wider shadow-lg">
                        Choose Professional
                    </a>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-white rounded-[2.5rem] border border-pink-50/50 p-10 flex flex-col justify-between shadow-xl shadow-pink-100/30 hover:scale-[1.02] transition-transform duration-300 relative overflow-hidden">
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-gray-900">Enterprise</h3>
                        <p class="text-sm text-gray-400">For multi-franchise gym networks requiring top specs.</p>
                        <div class="flex items-baseline justify-center">
                            <span class="text-5xl font-black tracking-tight">${{ number_format($plans['Enterprise'], 0) }}</span>
                            <span class="text-gray-400 font-semibold text-sm ml-1">/mo</span>
                        </div>
                        <ul class="text-left space-y-4 border-t border-gray-50 pt-6">
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Unlimited Gym Locations
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Unlimited Members
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Dedicated Account Manager
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-600">
                                <svg class="w-4 h-4 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                Custom API Integrations
                            </li>
                        </ul>
                    </div>
                    <a href="{{ route('register.enterprise', ['plan' => 'Enterprise']) }}" class="mt-8 block w-full py-4 text-center text-pink-600 bg-pink-50 hover:bg-pink-100 transition-colors font-bold rounded-2xl text-xs uppercase tracking-wider">
                        Choose Enterprise
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-8 bg-white border-t border-gray-50 text-center">
        <p class="text-xs font-bold text-gray-300 uppercase tracking-widest">&copy; 2026 GlowGym Management System. All Rights Reserved.</p>
    </footer>
</body>
</html>
