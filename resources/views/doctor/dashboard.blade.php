<x-layouts.doctor>
    {{-- Include Chart.js --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @endpush

    <div class="space-y-6" x-data="{ showWelcome: true }">

        {{-- Welcome Banner --}}
        {{-- Welcome Header with Real-Time Clock --}}
        <div class="bg-gradient-to-r from-secondary-600 via-secondary-700 to-blue-700 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-10 relative">
                {{-- Decorative Elements --}}
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <div class="mb-6 md:mb-0">
                            <h1 class="text-4xl font-bold text-white mb-3">
                                Welcome back, Dr. {{ Auth::user()->name }}!
                            </h1>
                            <p class="text-blue-100 text-lg">
                                Here's your practice overview for today
                            </p>

                            {{-- Real-Time Date and Time Display --}}
                            <div class="mt-6 flex flex-wrap gap-3">
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30">
                                    <span class="text-white text-sm font-medium">
                                        Doctor
                                    </span>
                                </div>
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30"
                                     x-data="{
                                        date: '',
                                        time: '',
                                        updateDateTime() {
                                            const now = new Date();
                                            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                                            this.date = now.toLocaleDateString('en-US', options);
                                            this.time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                                        }
                                     }"
                                     x-init="updateDateTime(); setInterval(() => updateDateTime(), 1000)">
                                    <span class="text-white text-sm font-medium" x-text="date + ' ' + time"></span>
                                </div>
                                <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full border border-white/30 flex items-center">
                                    <span class="inline-block w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                                    <span class="text-white text-sm font-medium">
                                        Active
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Profile Avatar --}}
                        <div class="w-32 h-32 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center border-4 border-white/30 shadow-2xl overflow-hidden">
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}"
                                class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Card 1: Today's Appointments --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-blue-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['today_appointments'] }};
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
                    @if ($stats['today_appointments'] > 0)
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full animate-pulse">
                            Today
                        </span>
                    @endif
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Today's Appointments</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Scheduled for today</p>
            </div>

            {{-- Card 2: Total Patients --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-green-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['total_patients'] }};
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
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Patients</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Under your care</p>
            </div>

            {{-- Card 3: Upcoming Sessions --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-purple-200 transform hover:-translate-y-1"
                x-data="{ count: 0 }" x-init="setTimeout(() => {
                    let target = {{ $stats['upcoming_sessions'] }};
                    let interval = setInterval(() => {
                        if (count < target) count++;
                        else clearInterval(interval);
                    }, 40);
                }, 300)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-purple-400 to-purple-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Upcoming Sessions</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2" x-text="count"></p>
                <p class="text-sm text-gray-600">Future schedules</p>
            </div>

            {{-- Card 4: Completion Rate --}}
            <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 p-6 border border-gray-100 hover:border-orange-200 transform hover:-translate-y-1"
                x-data="{ rate: 0 }" x-init="setTimeout(() => {
                    let target = {{ $performanceMetrics['completion_rate'] }};
                    let interval = setInterval(() => {
                        if (rate < target) rate++;
                        else clearInterval(interval);
                    }, 20);
                }, 400)">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="p-3 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                        Excellent
                    </span>
                </div>
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Completion Rate</h3>
                <p class="text-4xl font-bold text-gray-900 mb-2">
                    <span x-text="rate"></span>%
                </p>
                <p class="text-sm text-gray-600">Of all appointments</p>
            </div>

        </div>

        {{-- Today's Schedule --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Today's Schedule
                    </h3>
                    <p class="text-sm text-gray-500 mt-1" x-data="{
                        currentDate: '',
                        updateDate() {
                            const now = new Date();
                            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                            this.currentDate = now.toLocaleDateString('en-US', options);
                        }
                    }" x-init="updateDate(); setInterval(() => updateDate(), 1000)" x-text="currentDate"></p>
                </div>
                <a href="{{ route('doctor.schedule.index') }}"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition duration-150">
                    Manage Schedule
                </a>
            </div>

            @if ($todaySchedule)
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $todaySchedule->formatted_time_range }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $todaySchedule->booked_appointments }}/{{ $todaySchedule->max_appointments }} slots
                                booked
                            </p>
                        </div>
                        <div class="flex-1 max-w-xs ml-4">
                            <div class="bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $todaySchedule->max_appointments > 0 ? ($todaySchedule->booked_appointments / $todaySchedule->max_appointments) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($todaySchedule->appointments->count() > 0)
                    <div class="space-y-3">
                        @foreach ($todaySchedule->appointments->sortBy('appointment_time') as $appointment)
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition duration-150">
                                <div class="flex items-center space-x-4">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                                        <span class="text-lg font-bold text-blue-600">
                                            {{ substr($appointment->patient->user->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $appointment->patient->user->name }}
                                        </p>
                                        <p class="text-sm text-gray-600">{{ $appointment->formatted_time }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="badge {{ $appointment->status_badge_class }} badge-sm">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                    <a href="{{ route('doctor.appointments.show', $appointment) }}"
                                        class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                        View Details →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-8 text-gray-500">No appointments scheduled yet</p>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Schedule for Today</h3>
                    <p class="text-gray-600 mb-4">You don't have any scheduled sessions today.</p>
                    <a href="{{ route('doctor.schedule.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Create Schedule
                    </a>
                </div>
            @endif
        </div>

        {{-- Appointment Trends Chart --}}
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <svg class="h-6 w-6 text-green-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Appointment History
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Your appointment trends over the last 6 months</p>
                </div>
            </div>

            @if (isset($appointmentTrends) && count($appointmentTrends['labels']) > 0)
                <canvas id="appointmentTrendsChart" height="80"></canvas>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Appointment History Yet</h3>
                    <p class="mt-2 text-sm text-gray-500">Your appointment trends will appear here once you have appointments.</p>
                    <a href="{{ route('doctor.schedule.create') }}"
                        class="mt-4 inline-block px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition duration-150">
                        Create Your First Schedule
                    </a>
                </div>
            @endif
        </div>

        @if (isset($appointmentTrends) && count($appointmentTrends['labels']) > 0)
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('appointmentTrendsChart');
                        if (ctx) {
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
                                            pointRadius: 6,
                                            pointHoverRadius: 8,
                                        },
                                        {
                                            label: 'Cancelled',
                                            data: @json($appointmentTrends['cancelled']),
                                            borderColor: 'rgb(239, 68, 68)',
                                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                            tension: 0.4,
                                            fill: true,
                                            pointRadius: 6,
                                            pointHoverRadius: 8,
                                        },
                                        {
                                            label: 'Pending',
                                            data: @json($appointmentTrends['pending']),
                                            borderColor: 'rgb(250, 204, 21)',
                                            backgroundColor: 'rgba(250, 204, 21, 0.1)',
                                            tension: 0.4,
                                            fill: true,
                                            pointRadius: 6,
                                            pointHoverRadius: 8,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
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
                        }
                    });
                </script>
            @endpush
        @endif

    </div>
</x-layouts.doctor>
