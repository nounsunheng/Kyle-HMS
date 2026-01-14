<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register - {{ config('app.name', 'Kyle-HMS') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen py-12 px-4">
    <div class="w-full max-w-5xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center text-gray-600 hover:text-primary-600 transition-colors mb-6">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Home
            </a>

            <div class="flex items-center justify-center space-x-3 mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-secondary-500 rounded-xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <span class="text-3xl font-bold bg-gradient-to-r from-primary-600 to-secondary-600 bg-clip-text text-transparent">
                    Kyle-HMS
                </span>
            </div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Create Your Account</h1>
            <p class="text-gray-600">Join thousands managing their healthcare with Kyle-HMS</p>
        </div>

        <!-- Registration Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
            <!-- Progress Bar -->
            <div class="bg-gradient-to-r from-primary-500 to-secondary-500 h-2"></div>

            <div class="p-8 lg:p-12">
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold">Please correct the following errors:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <!-- Personal Information Section -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="font-bold">1</span>
                            </div>
                            Personal Information
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <input id="name"
                                           type="text"
                                           name="name"
                                           value="{{ old('name') }}"
                                           required
                                           autofocus
                                           autocomplete="name"
                                           class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                                           placeholder="Enter your full name">
                                </div>
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
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
                                           autocomplete="username"
                                           class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                                           placeholder="you@example.com">
                                </div>
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Phone Number <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </div>
                                    <input id="phone"
                                           type="tel"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           required
                                           class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('phone') border-red-500 @enderror"
                                           placeholder="+855 12 345 678">
                                </div>
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Date of Birth <span class="text-red-500">*</span>
                                </label>
                                <input id="date_of_birth"
                                       type="date"
                                       name="date_of_birth"
                                       value="{{ old('date_of_birth') }}"
                                       required
                                       max="{{ date('Y-m-d') }}"
                                       class="block w-full px-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('date_of_birth') border-red-500 @enderror">
                                @error('date_of_birth')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Gender -->
                            <div>
                                <label for="gender" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Gender <span class="text-red-500">*</span>
                                </label>
                                <select id="gender"
                                        name="gender"
                                        required
                                        class="block w-full px-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('gender') border-red-500 @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Address <span class="text-red-500">*</span>
                                </label>
                                <textarea id="address"
                                          name="address"
                                          rows="2"
                                          required
                                          class="block w-full px-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('address') border-red-500 @enderror"
                                          placeholder="Enter your full address">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact Section -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-secondary-100 text-secondary-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="font-bold">2</span>
                            </div>
                            Emergency Contact
                        </h2>

                        <div>
                            <label for="emergency_contact" class="block text-sm font-semibold text-gray-700 mb-2">
                                Emergency Contact Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                </div>
                                <input id="emergency_contact"
                                       type="tel"
                                       name="emergency_contact"
                                       value="{{ old('emergency_contact') }}"
                                       required
                                       class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('emergency_contact') border-red-500 @enderror"
                                       placeholder="+855 12 345 678">
                            </div>
                            <p class="mt-2 text-sm text-gray-500">Person to contact in case of emergency</p>
                            @error('emergency_contact')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="pt-6 border-t border-gray-200">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                            <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="font-bold">3</span>
                            </div>
                            Security
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
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
                                           autocomplete="new-password"
                                           class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                           placeholder="••••••••">
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Minimum 8 characters</p>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    </div>
                                    <input id="password_confirmation"
                                           type="password"
                                           name="password_confirmation"
                                           required
                                           autocomplete="new-password"
                                           class="block w-full pl-12 pr-4 py-3.5 border border-gray-300 rounded-xl text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                                           placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms & Submit -->
                    <div class="pt-6 border-t border-gray-200 space-y-6">
                        <!-- Register Button -->
                        <button type="submit"
                                class="w-full px-6 py-4 bg-gradient-to-r from-primary-500 to-secondary-600 text-white font-semibold rounded-xl hover:from-primary-600 hover:to-secondary-700 focus:outline-none focus:ring-4 focus:ring-primary-500/30 transition-all duration-200 shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-0.5 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Create Account
                        </button>

                        <!-- Login Link -->
                        <p class="text-center text-sm text-gray-600">
                            Already have an account?
                            <a href="{{ route('login') }}"
                               class="font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                Sign In
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>By registering, you agree to our Terms of Service and Privacy Policy</p>
        </div>
    </div>
</body>
</html>
