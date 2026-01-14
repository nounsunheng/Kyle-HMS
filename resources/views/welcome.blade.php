<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Kyle-HMS') }} - Hospital Management System</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200 fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                        Kyle-HMS
                    </span>
                </div>

                <!-- Navigation Links -->
                @if (Route::has('login'))
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                                class="px-5 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="px-5 py-2 text-sm font-medium text-gray-700 hover:text-primary-600 transition-colors">
                                Log in
                            </a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                    class="px-6 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white text-sm font-medium rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div class="space-y-8">
                    <div class="inline-block">
                        <span class="px-4 py-2 bg-primary-100 text-primary-700 text-sm font-medium rounded-full">
                            🏥 Healthcare Made Simple
                        </span>
                    </div>

                    <h1 class="text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">
                        Modern Healthcare
                        <span class="bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                            Management System
                        </span>
                    </h1>

                    <p class="text-xl text-gray-600 leading-relaxed">
                        Streamline your hospital operations with our comprehensive management system.
                        From patient appointments to medical records, manage everything in one place.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="px-8 py-4 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-xl shadow-primary-500/30 hover:shadow-2xl hover:shadow-primary-500/40 hover:-translate-y-0.5">
                                Register as Patient
                            </a>
                        @endif

                        <a href="#features"
                            class="px-8 py-4 bg-white text-gray-800 font-semibold rounded-xl border-2 border-gray-200 hover:border-primary-500 hover:text-primary-600 transition-all duration-200 shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Learn More
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-8 pt-8 border-t border-gray-200">
                        <div>
                            <div class="text-3xl font-bold text-gray-900">1000+</div>
                            <div class="text-sm text-gray-600">Active Patients</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900">50+</div>
                            <div class="text-sm text-gray-600">Doctors</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-gray-900">30+</div>
                            <div class="text-sm text-gray-600">Specialties</div>
                        </div>
                    </div>
                </div>

                <!-- Right Image/Illustration -->
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-br from-primary-200/30 to-secondary-200/30 rounded-3xl blur-3xl">
                    </div>
                    <div class="relative bg-white rounded-3xl shadow-2xl p-8">
                        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?auto=format&fit=crop&w=800&q=80"
                            alt="Healthcare" class="rounded-2xl w-full h-auto">

                        <!-- Floating Card 1 -->
                        <div class="absolute -top-6 -left-6 bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Appointments</div>
                                    <div class="text-xs text-gray-500">Easy Booking</div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Card 2 -->
                        <div
                            class="absolute -bottom-6 -right-6 bg-white rounded-2xl shadow-xl p-4 border border-gray-100">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Records</div>
                                    <div class="text-xs text-gray-500">Digital & Secure</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Everything You Need
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Powerful features designed to make healthcare management effortless
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div
                    class="group p-8 bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-gray-100 hover:border-primary-200 hover:shadow-xl transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Easy Appointments</h3>
                    <p class="text-gray-600">Book and manage appointments with your preferred doctors in just a few
                        clicks.</p>
                </div>

                <!-- Feature 2 -->
                <div
                    class="group p-8 bg-gradient-to-br from-green-50 to-white rounded-2xl border border-gray-100 hover:border-secondary-200 hover:shadow-xl transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Medical Records</h3>
                    <p class="text-gray-600">Access your complete medical history and records anytime, anywhere
                        securely.</p>
                </div>

                <!-- Feature 3 -->
                <div
                    class="group p-8 bg-gradient-to-br from-purple-50 to-white rounded-2xl border border-gray-100 hover:border-purple-200 hover:shadow-xl transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Expert Doctors</h3>
                    <p class="text-gray-600">Connect with specialized doctors across 30+ medical specialties.</p>
                </div>

                <!-- Feature 4 -->
                <div
                    class="group p-8 bg-gradient-to-br from-orange-50 to-white rounded-2xl border border-gray-100 hover:border-orange-200 hover:shadow-xl transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">24/7 Access</h3>
                    <p class="text-gray-600">Access your healthcare information anytime, from any device, anywhere.</p>
                </div>

                <!-- Feature 5 -->
                <div
                    class="group p-8 bg-gradient-to-br from-red-50 to-white rounded-2xl border border-gray-100 hover:border-red-200 hover:shadow-xl transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Secure & Private</h3>
                    <p class="text-gray-600">Your health data is encrypted and protected with industry-leading
                        security.</p>
                </div>

                <!-- Feature 6 -->
                <div
                    class="group p-8 bg-gradient-to-br from-indigo-50 to-white rounded-2xl border border-gray-100 hover:border-indigo-200 hover:shadow-xl transition-all duration-300">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Fast & Reliable</h3>
                    <p class="text-gray-600">Lightning-fast performance with 99.9% uptime guarantee.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-white to-gray-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <span class="px-4 py-2 bg-primary-100 text-primary-700 text-sm font-medium rounded-full">
                    Simple Process
                </span>
                <h2 class="text-4xl font-bold text-gray-900 mt-6 mb-4">
                    How Kyle-HMS Works
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Get started in minutes with our simple three-step process
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                <!-- Connection Lines -->
                <div
                    class="hidden md:block absolute top-24 left-0 right-0 h-0.5 bg-gradient-to-r from-primary-200 via-primary-300 to-secondary-200">
                </div>

                <!-- Step 1 -->
                <div class="relative">
                    <div
                        class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-600 rounded-2xl flex items-center justify-center mb-6 mx-auto relative z-10">
                            <span class="text-2xl font-bold text-white">1</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Create Account</h3>
                        <p class="text-gray-600 text-center leading-relaxed">
                            Sign up in seconds with your basic information. Quick, easy, and secure registration
                            process.
                        </p>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div
                        class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-2xl flex items-center justify-center mb-6 mx-auto relative z-10">
                            <span class="text-2xl font-bold text-white">2</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Find Your Doctor</h3>
                        <p class="text-gray-600 text-center leading-relaxed">
                            Browse through our network of 50+ specialist doctors and choose the right one for your
                            needs.
                        </p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div
                        class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100 hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                        <div
                            class="w-16 h-16 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center mb-6 mx-auto relative z-10">
                            <span class="text-2xl font-bold text-white">3</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Book Appointment</h3>
                        <p class="text-gray-600 text-center leading-relaxed">
                            Schedule your appointment at your convenience and get instant confirmation via email.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Specialties Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Medical Specialties
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Access to 30+ medical specialties under one roof
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div
                    class="group p-6 bg-gradient-to-br from-blue-50 to-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">❤️</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Cardiology</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-green-50 to-white rounded-xl border border-gray-100 hover:border-green-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">🧠</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Neurology</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-purple-50 to-white rounded-xl border border-gray-100 hover:border-purple-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">🦴</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Orthopedics</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-pink-50 to-white rounded-xl border border-gray-100 hover:border-pink-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">👶</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Pediatrics</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-yellow-50 to-white rounded-xl border border-gray-100 hover:border-yellow-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">👁️</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Ophthalmology</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-red-50 to-white rounded-xl border border-gray-100 hover:border-red-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">🩺</div>
                    <h3 class="font-semibold text-gray-900 text-sm">General Medicine</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-indigo-50 to-white rounded-xl border border-gray-100 hover:border-indigo-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">🦷</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Dentistry</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-teal-50 to-white rounded-xl border border-gray-100 hover:border-teal-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">👩</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Gynecology</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-orange-50 to-white rounded-xl border border-gray-100 hover:border-orange-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">🫁</div>
                    <h3 class="font-semibold text-gray-900 text-sm">Pulmonology</h3>
                </div>
                <div
                    class="group p-6 bg-gradient-to-br from-cyan-50 to-white rounded-xl border border-gray-100 hover:border-cyan-300 hover:shadow-lg transition-all text-center">
                    <div class="text-3xl mb-2">🧬</div>
                    <h3 class="font-semibold text-gray-900 text-sm">+ 21 More</h3>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    What Our Patients Say
                </h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Real stories from people who trust Kyle-HMS with their healthcare
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "Kyle-HMS has transformed how I manage my family's healthcare. Booking appointments is now
                        effortless, and I can access all our medical records in one place!"
                    </p>
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center text-white font-bold">
                            SM
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Sarah Martinez</h4>
                            <p class="text-sm text-gray-500">Patient since 2024</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 2 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "As a busy professional, I appreciate the convenience of managing appointments online. The
                        doctors are excellent and the system is incredibly user-friendly."
                    </p>
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-full flex items-center justify-center text-white font-bold">
                            JC
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">James Chen</h4>
                            <p class="text-sm text-gray-500">Patient since 2023</p>
                        </div>
                    </div>
                </div>

                <!-- Testimonial 3 -->
                <div class="bg-white rounded-2xl p-8 shadow-xl border border-gray-100">
                    <div class="flex items-center mb-4">
                        <div class="flex text-yellow-400">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 leading-relaxed">
                        "The peace of mind knowing my medical records are secure yet accessible whenever I need them is
                        invaluable. Highly recommend Kyle-HMS to everyone!"
                    </p>
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                            RP
                        </div>
                        <div class="ml-4">
                            <h4 class="font-semibold text-gray-900">Rachel Patel</h4>
                            <p class="text-sm text-gray-500">Patient since 2024</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Content -->
                <div>
                    <span class="px-4 py-2 bg-secondary-100 text-secondary-700 text-sm font-medium rounded-full">
                        Why Kyle-HMS
                    </span>
                    <h2 class="text-4xl font-bold text-gray-900 mt-6 mb-6">
                        Healthcare Management Built for the Modern Age
                    </h2>
                    <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                        We've revolutionized how healthcare is managed by combining cutting-edge technology with
                        patient-centered care. Our platform ensures seamless communication between patients and
                        healthcare providers.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div
                                class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">HIPAA Compliant Security</h3>
                                <p class="text-gray-600">Your health data is protected with bank-level encryption and
                                    compliance with international healthcare standards.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Lightning Fast Performance</h3>
                                <p class="text-gray-600">Our optimized infrastructure ensures your data loads
                                    instantly, making appointment booking and record access seamless.</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">24/7 Availability</h3>
                                <p class="text-gray-600">Access your health information anytime, from anywhere. Our
                                    system never sleeps so you're always connected.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Image/Stats -->
                <div class="relative">
                    <div class="bg-gradient-to-br from-primary-50 to-secondary-50 rounded-3xl p-8">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-white rounded-2xl p-6 shadow-lg">
                                <div class="text-4xl font-bold text-primary-600 mb-2">99.9%</div>
                                <p class="text-gray-600 text-sm">Uptime Guarantee</p>
                            </div>
                            <div class="bg-white rounded-2xl p-6 shadow-lg">
                                <div class="text-4xl font-bold text-secondary-600 mb-2">&lt;2s</div>
                                <p class="text-gray-600 text-sm">Average Load Time</p>
                            </div>
                            <div class="bg-white rounded-2xl p-6 shadow-lg">
                                <div class="text-4xl font-bold text-purple-600 mb-2">50K+</div>
                                <p class="text-gray-600 text-sm">Appointments Booked</p>
                            </div>
                            <div class="bg-white rounded-2xl p-6 shadow-lg">
                                <div class="text-4xl font-bold text-orange-600 mb-2">4.9★</div>
                                <p class="text-gray-600 text-sm">Average Rating</p>
                            </div>
                        </div>

                        <div class="mt-8 bg-white rounded-2xl p-6 shadow-lg">
                            <h3 class="font-semibold text-gray-900 mb-4">Trusted By</h3>
                            <div class="flex items-center justify-between">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">1000+</div>
                                    <div class="text-xs text-gray-500">Patients</div>
                                </div>
                                <div class="w-px h-12 bg-gray-200"></div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">50+</div>
                                    <div class="text-xs text-gray-500">Doctors</div>
                                </div>
                                <div class="w-px h-12 bg-gray-200"></div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900">30+</div>
                                    <div class="text-xs text-gray-500">Specialties</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-gray-50 to-white">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Frequently Asked Questions
                </h2>
                <p class="text-xl text-gray-600">
                    Everything you need to know about Kyle-HMS
                </p>
            </div>

            <div class="space-y-4">
                <details class="group bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <summary
                        class="flex justify-between items-center cursor-pointer p-6 font-semibold text-gray-900 hover:bg-gray-50 transition-colors">
                        <span>How do I book an appointment?</span>
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 text-gray-600">
                        Simply register for an account, browse our list of specialist doctors, select your preferred
                        doctor and available time slot, then confirm your appointment. You'll receive instant
                        confirmation via email.
                    </div>
                </details>

                <details class="group bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <summary
                        class="flex justify-between items-center cursor-pointer p-6 font-semibold text-gray-900 hover:bg-gray-50 transition-colors">
                        <span>Is my medical data secure?</span>
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 text-gray-600">
                        Absolutely. We use bank-level 256-bit encryption to protect your data. Our platform is HIPAA
                        compliant and follows international healthcare data protection standards. Your privacy is our
                        top priority.
                    </div>
                </details>

                <details class="group bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <summary
                        class="flex justify-between items-center cursor-pointer p-6 font-semibold text-gray-900 hover:bg-gray-50 transition-colors">
                        <span>Can I access my medical records anytime?</span>
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 text-gray-600">
                        Yes! Your medical records are available 24/7 through our secure patient portal. You can view,
                        download, and share your records with healthcare providers at any time from any device.
                    </div>
                </details>

                <details class="group bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <summary
                        class="flex justify-between items-center cursor-pointer p-6 font-semibold text-gray-900 hover:bg-gray-50 transition-colors">
                        <span>What if I need to cancel or reschedule?</span>
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 text-gray-600">
                        You can easily cancel or reschedule appointments through your patient dashboard. We recommend
                        giving at least 24 hours notice to allow other patients to book the time slot.
                    </div>
                </details>

                <details class="group bg-white rounded-2xl border border-gray-200 overflow-hidden">
                    <summary
                        class="flex justify-between items-center cursor-pointer p-6 font-semibold text-gray-900 hover:bg-gray-50 transition-colors">
                        <span>Do you offer emergency appointments?</span>
                        <svg class="w-5 h-5 text-gray-500 group-open:rotate-180 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 text-gray-600">
                        For medical emergencies, please call emergency services immediately. For urgent but
                        non-emergency situations, contact our 24/7 helpline for same-day appointment availability.
                    </div>
                </details>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div
                class="bg-gradient-to-r from-primary-600 to-secondary-600 rounded-3xl p-12 md:p-16 text-center relative overflow-hidden">
                <div class="absolute inset-0 bg-black/10"></div>

                <!-- Decorative elements -->
                <div class="absolute top-0 left-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <h2 class="text-3xl md:text-5xl font-bold text-white mb-4">
                        Ready to Transform Your Healthcare Experience?
                    </h2>
                    <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                        Join thousands of patients already managing their health with Kyle-HMS. Get started in less than
                        2 minutes.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-flex items-center justify-center px-8 py-4 bg-white text-primary-600 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-xl hover:shadow-2xl hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                    </path>
                                </svg>
                                Create Free Account
                            </a>
                        @endif
                        <a href="#features"
                            class="inline-flex items-center justify-center px-8 py-4 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-xl border-2 border-white/30 hover:bg-white/20 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Learn More
                        </a>
                    </div>

                    <div class="mt-8 flex items-center justify-center space-x-6 text-white/80 text-sm">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Free forever
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Trust Section -->
    <section class="py-16 px-4 sm:px-6 lg:px-8 bg-gray-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <p class="text-gray-600 font-medium mb-8">Trusted by leading healthcare institutions</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 items-center justify-items-center opacity-60">
                    <div class="text-gray-400 font-bold text-2xl">🏥 City Hospital</div>
                    <div class="text-gray-400 font-bold text-2xl">⚕️ Health Center</div>
                    <div class="text-gray-400 font-bold text-2xl">🩺 MediCare Plus</div>
                    <div class="text-gray-400 font-bold text-2xl">💊 WellCare</div>
                </div>
            </div>

            <div class="flex flex-col md:flex-row justify-center items-center gap-6 md:gap-12 text-gray-500 font-medium">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    No credit card required
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Setup in 2 minutes
                </div>
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Priority Support
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-300 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="grid md:grid-cols-4 gap-8 mb-8">
                <div class="col-span-2 md:col-span-1">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d=" M12
                4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018
                0z"></path>
        </svg>
    </div>
    <span class="text-xl font-bold text-white">Kyle-HMS</span>
    </div>
    <p class="text-sm text-gray-400">Modern healthcare management system for the digital age.</p>
    </div>

    <div>
        <h3 class="text-white font-semibold mb-4">Quick Links</h3>
        <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
            <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
            <li><a href="#" class="hover:text-white transition-colors">Contact</a></li>
        </ul>
    </div>

    <div>
        <h3 class="text-white font-semibold mb-4">Support</h3>
        <ul class="space-y-2 text-sm">
            <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
            <li><a href="#" class="hover:text-white transition-colors">Privacy Policy</a></li>
            <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
        </ul>
    </div>

    <div>
        <h3 class="text-white font-semibold mb-4">Contact</h3>
        <ul class="space-y-2 text-sm">
            <li class="flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                    </path>
                </svg>
                <span>info@kyle-hms.local</span>
            </li>
            <li class="flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                    </path>
                </svg>
                <span>+855 12 345 678</span>
            </li>
        </ul>
    </div>
    </div>

    <div class="pt-8 border-t border-gray-800 text-center text-sm">
        <p>&copy; {{ date('Y') }} Kyle-HMS. All rights reserved. Developed by Noun Sunheng.</p>
    </div>
    </div>
    </footer>
</body>

</html>
