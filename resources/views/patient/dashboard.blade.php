<x-layouts.patient>
    <div class="space-y-6" x-data="{ showWelcomeModal: @json(session('justRegistered', false)) }">

        {{-- Welcome Banner --}}
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-10 relative">
                {{-- Decorative Elements --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-6 md:mb-0">
                            <h1 class="text-4xl font-bold text-white mb-3">
                                Welcome back, {{ Auth::user()->name }}! 👋
                            </h1>
                            <p class="text-blue-100 text-lg">
                                Track your health journey and manage your appointments
                            </p>

                            {{-- Quick Stats Pills --}}
                            <div class="mt-6 flex flex-wrap gap-3">
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                    <span class="text-white text-sm font-medium">
                                        Patient ID: #PT{{ str_pad(Auth::user()->patient->id, 5, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                    <span class="text-white text-sm font-medium">
                                        Age: {{ Auth::user()->patient->age }} years
                                    </span>
                                </div>
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                    <span class="text-white text-sm font-medium">
                                        Blood Type: {{ Auth::user()->patient->blood_type ?? 'Not set' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Profile Avatar --}}
                        <div class="flex-shrink-0">
                            <div
                                class="w-32 h-32 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border-4 border-white/30 shadow-2xl overflow-hidden">
                                <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                                    class="w-full h-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Card 1: Upcoming Appointments --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-blue-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['upcoming_appointments'] }};
                    let interval = setInterval(() => {
                        if (count < target) count++;
                        else clearInterval(interval);
                    }, 50);
                }, 100)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-blue-400 to-blue-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    @if ($stats['upcoming_appointments'] > 0)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full animate-pulse">
                            Active
                        </span>
                    @endif
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Upcoming</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Appointments scheduled</p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('patient.appointments.index') }}"
                        class="text-blue-600 hover:text-blue-700 font-medium text-sm flex items-center group-hover:translate-x-1 transition-transform">
                        View all
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Card 2: Medical Records --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-green-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['total_records'] }};
                    let interval = setInterval(() => {
                        if (count < target) count++;
                        else clearInterval(interval);
                    }, 30);
                }, 200)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-green-400 to-green-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Medical Records</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Total health records</p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('patient.medical-records.index') }}"
                        class="text-green-600 hover:text-green-700 font-medium text-sm flex items-center group-hover:translate-x-1 transition-transform">
                        View records
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Card 3: Total Visits --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-purple-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['total_appointments'] }};
                    let interval = setInterval(() => {
                        count += 3;
                        if (count >= target) {
                            count = target;
                            clearInterval(interval);
                        }
                    }, 20);
                }, 300)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Visits</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">
                    <span class="text-green-600 font-semibold">{{ $stats['completed_appointments'] }}</span> completed
                </p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center text-sm text-gray-600">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-2 rounded-full"
                                style="width: {{ $stats['total_appointments'] > 0 ? ($stats['completed_appointments'] / $stats['total_appointments']) * 100 : 0 }}%">
                            </div>
                        </div>
                        <span class="ml-2 font-semibold">
                            {{ $stats['total_appointments'] > 0 ? round(($stats['completed_appointments'] / $stats['total_appointments']) * 100) : 0 }}%
                        </span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Available Doctors --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-orange-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['available_doctors'] }};
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
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <span
                        class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center">
                        <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                        Online
                    </span>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Available Doctors</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Ready to help you</p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('patient.doctors.index') }}"
                        class="text-orange-600 hover:text-orange-700 font-medium text-sm flex items-center group-hover:translate-x-1 transition-transform">
                        Browse doctors
                        <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>

        </div>

        {{-- Next Appointment Highlight --}}
        @if ($nextAppointment)
            <div
                class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg border-2 border-blue-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="p-2 bg-blue-600 rounded-lg mr-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">Your Next Appointment</h2>
                    </div>

                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div class="flex items-start space-x-4 mb-4 md:mb-0">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center">
                                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900">
                                        Dr. {{ $nextAppointment->schedule->doctor->user->name }}
                                    </h3>
                                    <p class="text-sm text-gray-600 mb-3">
                                        {{ $nextAppointment->schedule->doctor->specialty->name }}
                                    </p>
                                    <div class="space-y-2">
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span
                                                class="text-gray-700 font-medium">{{ $nextAppointment->schedule->formatted_date }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span
                                                class="text-gray-700 font-medium">{{ $nextAppointment->formatted_time }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <svg class="h-4 w-4 text-gray-400 mr-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <span
                                                class="text-gray-600">{{ $nextAppointment->schedule->schedule_date->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col space-y-2">
                                <a href="{{ route('patient.appointments.show', $nextAppointment) }}"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition duration-200 text-center">
                                    View Details
                                </a>
                                <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold {{ $nextAppointment->status_badge_class }}">
                                    {{ ucfirst($nextAppointment->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center border-2 border-dashed border-gray-300">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                    <svg class="h-10 w-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Upcoming Appointments</h3>
                <p class="text-gray-600 mb-6">You don't have any scheduled appointments at the moment.</p>
                <a href="{{ route('patient.doctors.index') }}"
                    class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition duration-200 transform hover:-translate-y-0.5">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Find a Doctor
                </a>
            </div>
        @endif

        {{-- Appointment Trends Chart - SIMPLIFIED INLINE VERSION --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="h-6 w-6 text-blue-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Appointment History
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Your appointment trends over the last 6 months</p>
                </div>
            </div>

            <div style="position: relative; height: 400px;">
                <canvas id="appointmentTrendsChart"></canvas>
            </div>
        </div>

    </div>

    {{-- LOAD CHART.JS AND INITIALIZE - INLINE AT BOTTOM OF PAGE --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        (function() {
            console.log('Patient chart script starting...');

            function tryInitChart() {
                if (typeof Chart === 'undefined') {
                    console.log('Chart.js not loaded yet, waiting...');
                    return false;
                }

                const ctx = document.getElementById('appointmentTrendsChart');
                if (!ctx) {
                    console.log('Canvas not found yet, waiting...');
                    return false;
                }

                console.log('Initializing patient chart with data...');

                try {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($appointmentTrends['labels']),
                            datasets: [{
                                label: 'Completed',
                                data: @json($appointmentTrends['completed']),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                                pointBackgroundColor: 'rgb(34, 197, 94)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }, {
                                label: 'Cancelled',
                                data: @json($appointmentTrends['cancelled']),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.4,
                                fill: true,
                                borderWidth: 3,
                                pointRadius: 6,
                                pointHoverRadius: 8,
                                pointBackgroundColor: 'rgb(239, 68, 68)',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 15,
                                        font: {
                                            size: 13,
                                            weight: '600'
                                        }
                                    }
                                },
                                tooltip: {
                                    mode: 'index',
                                    intersect: false,
                                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1,
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        color: 'rgba(0, 0, 0, 0.05)'
                                    }
                                },
                                x: {
                                    ticks: {
                                        font: {
                                            size: 12
                                        }
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });

                    console.log('✅ Patient chart created successfully!');
                    return true;

                } catch (error) {
                    console.error('Error creating patient chart:', error);
                    return false;
                }
            }

            // Try immediately
            if (!tryInitChart()) {
                // Try after a delay
                setTimeout(function() {
                    if (!tryInitChart()) {
                        // Try one more time
                        setTimeout(tryInitChart, 1000);
                    }
                }, 500);
            }
        })();
    </script>
</x-layouts.patient>
