<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GlowGym - Empowering Women's Fitness</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            scroll-behavior: smooth;
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
            box-shadow: 0 10px 20px -5px rgba(236, 72, 153, 0.5);
        }

        .card-feature {
            transition: all 0.3s ease;
        }

        .card-feature:hover {
            transform: translateY(-5px);
            border-color: #fce7f3;
        }
    </style>
</head>

<body class="antialiased bg-white text-gray-900">

    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-2">
                    <div class="bg-glow p-2 rounded-lg shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-gray-900">Glow<span
                            class="text-pink-500">Gym</span></span>
                </div>

                <div class="flex items-center space-x-8">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="text-sm font-bold text-gray-700 hover:text-pink-500 transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-sm font-bold text-gray-700 hover:text-pink-500 transition-colors">Log In</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="btn-glow px-6 py-2.5 rounded-full text-sm font-bold text-white shadow-lg shadow-pink-100">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('images/gymladies.jpg') }}" class="w-full h-full object-cover opacity-10"
                alt="Gym Background">
            <div class="absolute inset-0 bg-gradient-to-b from-white via-transparent to-white"></div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 relative z-10 text-center">
            <div class="inline-block px-4 py-1.5 mb-8 rounded-full bg-pink-50 border border-pink-100">
                <span class="text-[10px] font-black text-pink-600 uppercase tracking-[0.2em]">#1 Fitness Management
                    Software</span>
            </div>

            <h1 class="text-5xl lg:text-8xl font-black text-gray-900 leading-[1.1] mb-8 tracking-tight">
                EMPOWERING<br>
                <span class="text-gradient">WOMEN'S FITNESS</span>
            </h1>

            <div
                class="max-w-2xl mx-auto mb-12 py-4 px-8 rounded-2xl bg-gray-50/50 backdrop-blur-sm border border-gray-100 shadow-sm">
                <p class="text-lg font-semibold text-gray-600">All-in-One Gym Management platform for ladies Gym</p>
            </div>

            <a href="{{ route('register') }}"
                class="btn-glow px-10 py-5 rounded-full text-lg font-bold text-white shadow-xl shadow-pink-200 inline-block">
                Get Started Free
            </a>
        </div>
    </section>

    <!-- Struggles Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                <div class="relative">
                    <div class="absolute -inset-4 bg-pink-100/50 rounded-[2.5rem] -rotate-2"></div>
                    <img src="{{ asset('public/images/gymgirls.png') }}"
                        class="relative rounded-[2rem] shadow-2xl w-full h-[500px] object-cover" alt="Women training">
                    <div
                        class="absolute bottom-8 left-8 right-8 bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-xl border border-white">
                        <p class="text-sm font-bold text-gray-900">"How Gym simplifies management so you can empower
                            more women."</p>
                    </div>
                </div>

                <div>
                    <h2 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-4">What Gym Owners <br><span
                            class="text-pink-500">Struggle With</span></h2>

                    <div class="space-y-10 mt-12">
                        <div class="flex items-start">
                            <div
                                class="w-12 h-12 rounded-full bg-pink-50 flex items-center justify-center flex-shrink-0 mr-6">
                                <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">Chaotic Class Scheduling</h4>
                                <p class="text-gray-500 leading-relaxed">Manual scheduling leads to confusion,
                                    overbooked classes, and frustrated members. Automate it instantly.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="w-12 h-12 rounded-full bg-indigo-50 flex items-center justify-center flex-shrink-0 mr-6">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">Missed Classes & No-shows</h4>
                                <p class="text-gray-500 leading-relaxed">Up to 25% revenue loss monthly due to lack of
                                    reminders. Our automated notifications fix this.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0 mr-6">
                                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xl font-bold text-gray-900 mb-2">Inflexible Memberships</h4>
                                <p class="text-gray-500 leading-relaxed">Membership tiers and pricing are hard to manage
                                    manually. Simplify billing and access control.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-24 bg-pink-50/30">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">All the tools u need to</p>
            <h2 class="text-5xl lg:text-7xl font-black text-pink-500 mb-20">GROW UR BUSINESS</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="card-feature bg-white p-10 rounded-[2.5rem] text-left border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-pink-50 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-4">Notifications</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Consumers have xxxx automatically to keep them
                        engaged and reduce dropout rates.</p>
                </div>

                <!-- Feature 2 -->
                <div class="card-feature bg-white p-10 rounded-[2.5rem] text-left border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-4">Member Management</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Easily manage members, monitor engagement, and
                        track progress all in one place.</p>
                </div>

                <!-- Feature 3 -->
                <div class="card-feature bg-white p-10 rounded-[2.5rem] text-left border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-4">Trainer Scheduling</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Assign trainers to classes and manage their
                        availability effortlessly without conflicts.</p>
                </div>

                <!-- Feature 4 -->
                <div class="card-feature bg-white p-10 rounded-[2.5rem] text-left border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-4">Class & Timeslot</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Members book classes real-time, anywhere. Full
                        control over capacity and waitlists.</p>
                </div>

                <!-- Feature 5 -->
                <div class="card-feature bg-white p-10 rounded-[2.5rem] text-left border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-purple-50 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-4">Analytics Dashboard</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Track attendance trends, popular classes, and gym
                        growth with visual data.</p>
                </div>

                <!-- Feature 6 -->
                <div class="card-feature bg-white p-10 rounded-[2.5rem] text-left border border-gray-100 shadow-sm">
                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mb-8">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-900 mb-4">Payment Processing</h4>
                    <p class="text-gray-500 text-sm leading-relaxed">Integrated payments for seamless membership
                        renewals and merchandise sales.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Gym Types Section -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-16 uppercase tracking-tight">Types of Gyms
                We <span class="text-pink-500">Support</span></h2>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="group relative overflow-hidden rounded-3xl h-64">
                    <img src="{{ asset('images/landing/pilates.png') }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        alt="Pilates">
                    <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                        <h5 class="text-white font-bold text-lg">Pilates Studio</h5>
                    </div>
                </div>
                <div class="group relative overflow-hidden rounded-3xl h-64">
                    <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&q=80&w=600"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        alt="Fitness">
                    <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                        <h5 class="text-white font-bold text-lg">Fitness Training Gym</h5>
                    </div>
                </div>
                <div class="group relative overflow-hidden rounded-3xl h-64">
                    <img src="{{ asset('images/landing/hiit.png') }}"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        alt="HIIT">
                    <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                        <h5 class="text-white font-bold text-lg">HIIT Studio</h5>
                    </div>
                </div>
                <div class="group relative overflow-hidden rounded-3xl h-64">
                    <img src="https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?auto=format&fit=crop&q=80&w=600"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                        alt="Yoga">
                    <div class="absolute inset-0 bg-black/40 flex items-end p-6">
                        <h5 class="text-white font-bold text-lg">Yoga Studio</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="py-24 bg-gradient-to-b from-white to-pink-50/50 overflow-hidden relative">
        <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
            <h2 class="text-4xl lg:text-5xl font-black text-gray-900 mb-6 tracking-tight">Interested in joining? Get
                Started <br>with <span class="text-pink-500">GlowGym</span> today</h2>
            <p class="text-gray-500 font-medium mb-12">Join hundreds of women-focused fitness centers that are scaling
                their business with our all-in-one platform.</p>

            <a href="{{ route('register') }}"
                class="btn-glow px-12 py-5 rounded-full text-xl font-black text-white shadow-2xl shadow-pink-200 inline-block">
                JOIN NOW
            </a>
        </div>

        <div
            class="absolute top-1/2 left-0 w-64 h-64 bg-pink-100 rounded-full blur-3xl opacity-50 -translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute top-1/2 right-0 w-64 h-64 bg-indigo-100 rounded-full blur-3xl opacity-50 translate-x-1/2 -translate-y-1/2">
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 bg-white border-t border-gray-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-1">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="bg-glow p-1.5 rounded-lg shadow-md">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-black tracking-tight text-gray-900">Glow<span
                                class="text-pink-500">Gym</span></span>
                    </div>
                    <p class="text-sm text-gray-400 font-medium leading-relaxed">The ultimate management platform for
                        the modern women's fitness studio.</p>
                </div>

                <div>
                    <h5 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Service</h5>
                    <ul class="space-y-4 text-sm text-gray-500 font-medium">
                        <li><a href="#" class="hover:text-pink-500 transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-pink-500 transition-colors">Features</a></li>
                        <li><a href="#" class="hover:text-pink-500 transition-colors">Case Studies</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Support</h5>
                    <ul class="space-y-4 text-sm text-gray-500 font-medium">
                        <li><a href="#" class="hover:text-pink-500 transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-pink-500 transition-colors">Contact Us</a></li>
                        <li><a href="#" class="hover:text-pink-500 transition-colors">Privacy Policy</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Stay Updated</h5>
                    <div class="flex">
                        <input type="email" placeholder="Email Address"
                            class="bg-gray-50 border-none rounded-l-xl px-4 py-3 text-sm focus:ring-2 focus:ring-pink-500 w-full">
                        <button class="bg-glow px-4 rounded-r-xl text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="pt-8 border-t border-gray-50 text-center">
                <p class="text-xs font-bold text-gray-300 uppercase tracking-widest">&copy; 2026 GlowGym Management
                    System. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>