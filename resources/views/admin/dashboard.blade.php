<x-layouts.admin>
    {{-- Include Chart.js --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endpush

    <div class="space-y-6">

        {{-- Welcome Header --}}
        <div class="bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-700 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-10 relative">
                {{-- Decorative Elements --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-6 md:mb-0">
                            <h1 class="text-4xl font-bold text-white mb-3">
                                Admin Dashboard 🎯
                            </h1>
                            <p class="text-purple-100 text-lg">
                                System overview and management control center
                            </p>

                            {{-- Quick Stats Pills --}}
                            <div class="mt-6 flex flex-wrap gap-3">
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                    <span class="text-white text-sm font-medium">
                                        Administrator
                                    </span>
                                </div>
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                    <span class="text-white text-sm font-medium">
                                        {{ now()->format('l, F d, Y') }}
                                    </span>
                                </div>
                                <div
                                    class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30 flex items-center">
                                    <span
                                        class="inline-block w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                    <span class="text-white text-sm font-medium">
                                        System Active
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Profile Avatar --}}
                        <div
                            class="w-32 h-32 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border-4 border-white/30 shadow-2xl overflow-hidden">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Statistics Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Total Doctors --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-blue-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['total_doctors'] }};
                    let interval = setInterval(() => {
                        if (count < target) count++;
                        else clearInterval(interval);
                    }, 30);
                }, 100)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">
                        {{ $stats['available_doctors'] }} Active
                    </span>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Doctors</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <div class="flex items-center text-sm text-gray-600">
                    <svg class="w-4 h-4 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $stats['available_doctors'] }} available
                </div>
            </div>

            {{-- Total Patients --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-green-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['total_patients'] }};
                    let interval = setInterval(() => {
                        count += 2;
                        if (count >= target) {
                            count = target;
                            clearInterval(interval);
                        }
                    }, 20);
                }, 200)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Patients</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Registered users</p>
            </div>

            {{-- Total Appointments --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-purple-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['total_appointments'] }};
                    let interval = setInterval(() => {
                        count += 3;
                        if (count >= target) {
                            count = target;
                            clearInterval(interval);
                        }
                    }, 15);
                }, 300)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full">
                        {{ $stats['today_appointments'] }} Today
                    </span>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Appointments</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">
                    <span class="text-purple-600 font-semibold">{{ $stats['week_appointments'] }}</span> this week
                </p>
            </div>

            {{-- Medical Specialties --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-orange-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['total_specialties'] }};
                    let interval = setInterval(() => {
                        if (count < target) count++;
                        else clearInterval(interval);
                    }, 40);
                }, 400)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Specialties</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Medical departments</p>
            </div>

        </div>

        {{-- System Health & Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- System Health --}}
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    System Health
                </h3>

                <div class="space-y-4">
                    {{-- Completion Rate --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Completion Rate</span>
                            <span
                                class="text-sm font-bold text-green-600">{{ $systemHealth['completion_rate'] }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ $systemHealth['completion_rate'] }}%"></div>
                        </div>
                    </div>

                    {{-- Utilization Rate --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Utilization Rate</span>
                            <span
                                class="text-sm font-bold text-blue-600">{{ $systemHealth['utilization_rate'] }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ $systemHealth['utilization_rate'] }}%"></div>
                        </div>
                    </div>

                    {{-- Cancellation Rate --}}
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Cancellation Rate</span>
                            <span
                                class="text-sm font-bold text-orange-600">{{ $systemHealth['cancellation_rate'] }}%</span>
                        </div>
                        <div class="bg-gray-200 rounded-full h-2.5">
                            <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-2.5 rounded-full transition-all duration-500"
                                style="width: {{ $systemHealth['cancellation_rate'] }}%"></div>
                        </div>
                    </div>

                    {{-- Active Doctors --}}
                    <div class="pt-4 border-t border-gray-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Active Doctors</span>
                            <span class="text-lg font-bold text-gray-900">
                                {{ $systemHealth['active_doctors'] }}/{{ $systemHealth['total_doctors'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <a href="{{ route('admin.doctors.index') }}"
                        class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl hover:from-blue-100 hover:to-blue-200 transition duration-150 group">
                        <div
                            class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">Manage Doctors</span>
                    </a>

                    <a href="{{ route('admin.patients.index') }}"
                        class="flex flex-col items-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl hover:from-green-100 hover:to-green-200 transition duration-150 group">
                        <div
                            class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">Manage Patients</span>
                    </a>

                    <a href="{{ route('admin.specialties.index') }}"
                        class="flex flex-col items-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl hover:from-purple-100 hover:to-purple-200 transition duration-150 group">
                        <div
                            class="w-12 h-12 bg-purple-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">Specialties</span>
                    </a>

                    <a href="{{ route('admin.appointments.index') }}"
                        class="flex flex-col items-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl hover:from-yellow-100 hover:to-yellow-200 transition duration-150 group">
                        <div
                            class="w-12 h-12 bg-yellow-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">Appointments</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                        class="flex flex-col items-center p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-xl hover:from-red-100 hover:to-red-200 transition duration-150 group">
                        <div
                            class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">Reports</span>
                    </a>

                    <a href="{{ route('profile.edit') }}"
                        class="flex flex-col items-center p-4 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl hover:from-gray-100 hover:to-gray-200 transition duration-150 group">
                        <div
                            class="w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">Settings</span>
                    </a>
                </div>
            </div>

        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Appointment Status Breakdown (Pie Chart) --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                    </svg>
                    Appointment Status Distribution
                </h3>
                <canvas id="statusChart" height="250"></canvas>
            </div>

            {{-- Monthly Trends (Line Chart) --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    Appointment Trends (6 Months)
                </h3>
                <canvas id="trendsChart" height="250"></canvas>
            </div>

        </div>

        {{-- Registration Trends & Top Doctors --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Registration Trends --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    User Registration Trends
                </h3>
                <canvas id="registrationChart" height="250"></canvas>
            </div>

            {{-- Top Performing Doctors --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                    <svg class="h-5 w-5 text-yellow-600 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                    Top Performing Doctors
                </h3>

                <div class="space-y-3">
                    @foreach ($topDoctors as $index => $doctor)
                        <div
                            class="flex items-center justify-between p-3 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:from-gray-100 hover:to-gray-50 transition duration-150 border border-transparent hover:border-gray-200">

                            <div class="flex items-center space-x-4">
                                <div
                                    class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-full flex items-center justify-center font-bold text-white shadow-sm">
                                    #{{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 leading-tight">Dr. {{ $doctor->user->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $doctor->specialty->name }}</p>
                                </div>
                            </div>

                            <div class="text-right">
                                <p class="text-lg font-bold text-green-600 leading-none">
                                    {{ $doctor->completed_count }}</p>
                                <p class="text-[10px] uppercase tracking-wider font-semibold text-gray-400 mt-1">
                                    Completed</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Recent Appointments --}}
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Recent Appointments
                    </h3>
                    <a href="{{ route('admin.appointments.index') }}"
                        class="text-sm font-medium text-purple-600 hover:text-purple-700">
                        View All →
                    </a>
                </div>

                @if ($recentAppointments->count() > 0)
                    <div class="space-y-3">
                        @foreach ($recentAppointments as $appointment)
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition duration-150">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-full flex items-center justify-center">
                                            <span class="text-sm font-bold text-purple-600">
                                                {{ substr($appointment->patient->user->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-900">
                                                {{ $appointment->patient->user->name }}</p>
                                            <p class="text-sm text-gray-600">
                                                with Dr. {{ $appointment->schedule->doctor->user->name }}
                                                • {{ $appointment->schedule->formatted_date }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="badge {{ $appointment->status_badge_class }} badge-sm">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                    <a href="{{ route('admin.appointments.show', $appointment) }}"
                                        class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-8 text-gray-500">No appointments yet</p>
                @endif
            </div>

        </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // Appointment Status Pie Chart
                    const statusCtx = document.getElementById('statusChart');
                    if (statusCtx) {
                        new Chart(statusCtx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled', 'No Show'],
                                datasets: [{
                                    data: [
                                        {{ $appointmentsByStatus['pending'] }},
                                        {{ $appointmentsByStatus['confirmed'] }},
                                        {{ $appointmentsByStatus['completed'] }},
                                        {{ $appointmentsByStatus['cancelled'] }},
                                        {{ $appointmentsByStatus['no_show'] }}
                                    ],
                                    backgroundColor: [
                                        'rgb(250, 204, 21)',
                                        'rgb(59, 130, 246)',
                                        'rgb(34, 197, 94)',
                                        'rgb(239, 68, 68)',
                                        'rgb(156, 163, 175)'
                                    ],
                                    borderWidth: 2,
                                    borderColor: '#fff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: {
                                            padding: 15,
                                            font: {
                                                size: 12
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Monthly Trends Line Chart
                    const trendsCtx = document.getElementById('trendsChart');
                    if (trendsCtx) {
                        new Chart(trendsCtx, {
                            type: 'line',
                            data: {
                                labels: @json($monthlyTrends['labels']),
                                datasets: [{
                                        label: 'Completed',
                                        data: @json($monthlyTrends['completed']),
                                        borderColor: 'rgb(34, 197, 94)',
                                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                        tension: 0.4,
                                        fill: true
                                    },
                                    {
                                        label: 'Cancelled',
                                        data: @json($monthlyTrends['cancelled']),
                                        borderColor: 'rgb(239, 68, 68)',
                                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                        tension: 0.4,
                                        fill: true
                                    },
                                    {
                                        label: 'Pending',
                                        data: @json($monthlyTrends['pending']),
                                        borderColor: 'rgb(250, 204, 21)',
                                        backgroundColor: 'rgba(250, 204, 21, 0.1)',
                                        tension: 0.4,
                                        fill: true
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    }

                    // Registration Trends Chart
                    const regCtx = document.getElementById('registrationChart');
                    if (regCtx) {
                        new Chart(regCtx, {
                            type: 'bar',
                            data: {
                                labels: @json($registrationTrends['labels']),
                                datasets: [{
                                        label: 'Patients',
                                        data: @json($registrationTrends['patients']),
                                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                        borderColor: 'rgb(34, 197, 94)',
                                        borderWidth: 2
                                    },
                                    {
                                        label: 'Doctors',
                                        data: @json($registrationTrends['doctors']),
                                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                                        borderColor: 'rgb(59, 130, 246)',
                                        borderWidth: 2
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    }

                });
            </script>
        @endpush
</x-layouts.admin>
