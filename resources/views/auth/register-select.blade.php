<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose Your Journey - GlowGym</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #fff5f7; }
        .pink-glow { box-shadow: 0 20px 40px -10px rgba(236, 72, 153, 0.2); }
        .btn-pink { background: #ec4899; color: white; transition: all 0.3s ease; }
        .btn-pink:hover { background: #db2777; transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(236, 72, 153, 0.4); }
    </style>
</head>
<body class="antialiased">
    <!-- Back Button -->
    <div class="fixed top-8 left-8 z-50">
        <a href="/" class="flex items-center space-x-2 text-sm font-bold text-gray-500 hover:text-pink-600 transition-colors bg-white/80 backdrop-blur px-4 py-2 rounded-full border border-gray-100 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back</span>
        </a>
    </div>

    <div class="min-h-screen flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl w-full space-y-12">
            
            <!-- Header -->
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight mb-4">Choose Your Journey</h1>
                <p class="text-lg text-gray-500 font-medium max-w-2xl mx-auto">Join the exclusive community designed for ladies-only fitness. Select how you'd like to experience <span class="text-pink-500 font-bold">GymGlow</span>.</p>
            </div>

            <!-- Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                
                <!-- Member Card -->
                <div class="bg-white rounded-[3rem] overflow-hidden pink-glow border border-pink-50 flex flex-col h-full group hover:shadow-pink-200/50 transition-all duration-500">
                    <div class="h-64 relative overflow-hidden shrink-0">
                        <img src="{{ asset('images/Girls_member.png') }}" alt="Member" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000" style="object-position: center top;">
                        <div class="absolute top-6 left-6">
                            <span class="px-5 py-2 bg-pink-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg">For Members</span>
                        </div>
                    </div>
                    <div class="p-12 flex flex-col flex-1">
                        <h2 class="text-3xl font-black text-gray-900 mb-4">Join as a Member</h2>
                        <p class="text-gray-500 text-base leading-relaxed mb-8 flex-1">Ready to glow? Start your personal fitness journey with access to world-class programs, tracking tools, and a supportive community of women.</p>

                        <ul class="space-y-4 mb-10">
                            <li class="flex items-center text-sm font-bold text-gray-700">
                                <div class="w-6 h-6 bg-pink-100 text-pink-500 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                Book fitness classes and manage appointments
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-700">
                                <div class="w-6 h-6 bg-pink-100 text-pink-500 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                Access to ladies-only local gyms
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-700">
                                <div class="w-6 h-6 bg-pink-100 text-pink-500 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                Choose classes that fit your fitness goals
                            </li>
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('member.register') }}" class="block w-full py-5 btn-pink text-center rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] shadow-lg">
                                Member Sign Up
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Enterprise Card -->
                <div class="bg-white rounded-[3rem] overflow-hidden pink-glow border border-pink-50 flex flex-col h-full group hover:shadow-pink-200/50 transition-all duration-500">
                    <div class="h-64 relative overflow-hidden shrink-0">
                        <img src="{{ asset('images/auth/enterprise-bg.png') }}" alt="Enterprise" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                        <div class="absolute top-6 left-6">
                            <span class="px-5 py-2 bg-pink-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg">For Enterprises</span>
                        </div>
                    </div>
                    <div class="p-12 flex flex-col flex-1">
                        <h2 class="text-3xl font-black text-gray-900 mb-4">Join as a Gym Enterprise</h2>
                        <p class="text-gray-500 text-base leading-relaxed mb-8 flex-1">Scale your business with the most powerful management suite built specifically for ladies-only gyms. Manage members, staff, and payments with ease.</p>

                        <ul class="space-y-4 mb-10">
                            <li class="flex items-center text-sm font-bold text-gray-700">
                                <div class="w-6 h-6 bg-pink-100 text-pink-500 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                Multi-branch management
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-700">
                                <div class="w-6 h-6 bg-pink-100 text-pink-500 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                Advanced business analytics
                            </li>
                            <li class="flex items-center text-sm font-bold text-gray-700">
                                <div class="w-6 h-6 bg-pink-100 text-pink-500 rounded-full flex items-center justify-center mr-3 shrink-0">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                Automated billing & CRM
                            </li>
                        </ul>

                        <div class="mt-auto">
                            <a href="{{ route('saas.pricing') }}" class="block w-full py-5 btn-pink text-center rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] shadow-lg">
                                Enterprise Sign Up
                            </a>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Footer Login Link -->
            <div class="text-center text-sm font-medium text-gray-500 pt-8">
                Already have an account? <a href="{{ route('login') }}" class="text-pink-500 font-bold hover:underline">Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>
