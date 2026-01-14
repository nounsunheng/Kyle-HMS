<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Kyle-HMS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-0 bg-white rounded-3xl shadow-2xl overflow-hidden">
        <!-- Left Side - Login Form -->
        <div class="p-8 lg:p-12">
            <!-- Logo & Back Link -->
            <div class="mb-8">
                <a href="/" class="inline-flex items-center text-gray-600 hover:text-primary-600 transition-colors mb-8">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Home
                </a>

                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                        Kyle-HMS
                    </span>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-gray-600">Please enter your credentials to continue</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('status') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <input id="email"
                               type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autofocus
                               autocomplete="username"
                               class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="you@example.com">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <input id="password"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center cursor-pointer">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500 transition-colors">
                        <span class="ml-2 text-sm text-gray-700">Remember me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Login Button -->
                <button type="submit"
                        class="w-full px-6 py-3.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-500/30 transition-all duration-200 shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-0.5">
                    Sign In
                </button>

                <!-- Register Link -->
                @if (Route::has('register'))
                    <p class="text-center text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}"
                           class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                            Create Account
                        </a>
                    </p>
                @endif
            </form>

            <!-- Divider -->
            <div class="mt-8 mb-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">Quick Access (For Texting)</span>
                    </div>
                </div>
            </div>

            <!-- Demo Accounts -->
            <div class="space-y-3">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-blue-900">Demo Patient Account</p>
                            <p class="text-xs text-blue-700 mt-1">sunheng@example.com / patient123</p>
                        </div>
                        <span class="px-3 py-1 bg-blue-200 text-blue-800 text-xs font-medium rounded-full">
                            Patient
                        </span>
                    </div>
                </div>

                <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-green-900">Demo Doctor Account</p>
                            <p class="text-xs text-green-700 mt-1">lina.sok@kyle-hms.local / doctor123</p>
                        </div>
                        <span class="px-3 py-1 bg-green-200 text-green-800 text-xs font-medium rounded-full">
                            Doctor
                        </span>
                    </div>
                </div>

                <div class="p-4 bg-purple-50 border border-purple-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-purple-900">Demo Admin Account</p>
                            <p class="text-xs text-purple-700 mt-1">admin@kyle-hms.local / admin123</p>
                        </div>
                        <span class="px-3 py-1 bg-purple-200 text-purple-800 text-xs font-medium rounded-full">
                            Admin
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Image/Info -->
        <div class="hidden lg:flex bg-gradient-to-br from-primary-600 to-secondary-600 p-12 items-center justify-center relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>

            <!-- Decorative Elements -->
            <div class="absolute top-10 right-10 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 left-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>

            <div class="relative z-10 text-white">
                <div class="mb-8">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mb-6 backdrop-blur-sm">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <h2 class="text-4xl font-bold mb-4">
                        Secure Healthcare<br>Management
                    </h2>
                    <p class="text-white/90 text-lg leading-relaxed mb-8">
                        Access your medical records, book appointments, and manage your healthcare journey all in one secure platform.
                    </p>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">End-to-end Encryption</p>
                            <p class="text-sm text-white/80">Your data is protected with military-grade security</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">24/7 Availability</p>
                            <p class="text-sm text-white/80">Access your healthcare information anytime, anywhere</p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Expert Support</p>
                            <p class="text-sm text-white/80">Connect with healthcare professionals instantly</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-white/20">
                    <div class="flex items-center space-x-8">
                        <div>
                            <div class="text-3xl font-bold">1000+</div>
                            <div class="text-sm text-white/80">Active Users</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold">50+</div>
                            <div class="text-sm text-white/80">Doctors</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold">30+</div>
                            <div class="text-sm text-white/80">Specialties</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
