{{-- resources/views/patient/dashboard.blade.php --}}
<x-layouts.patient>
    <div class="space-y-6">
        <!-- Welcome Header - Professional Design -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">
                            Welcome back, {{ Auth::user()->name }}
                        </h1>
                        <p class="text-primary-100">Here's your health dashboard overview</p>
                    </div>
                    <div class="hidden md:block">
                        <div class="bg-white/20 backdrop-blur-sm rounded-lg px-4 py-3 text-right border border-white/30">
                            <p class="text-xs text-primary-100 font-medium uppercase tracking-wide">Patient ID</p>
                            <p class="text-2xl font-bold text-white font-mono">
                                #PT{{ str_pad(Auth::user()->patient->id, 5, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info Bar -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Age</p>
                            <p class="font-semibold text-gray-900">{{ Auth::user()->patient->age }} years</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Blood Type</p>
                            <p class="font-semibold text-gray-900">{{ Auth::user()->patient->blood_type ?? 'Not set' }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Phone</p>
                            <p class="font-semibold text-gray-900">{{ Auth::user()->patient->phone }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Member Since</p>
                            <p class="font-semibold text-gray-900">{{ Auth::user()->created_at->format('M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $upcomingCount = Auth::user()
                    ->patient->appointments()
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->whereHas('schedule', function ($q) {
                        $q->where('schedule_date', '>=', now()->toDateString());
                    })
                    ->count();

                $totalRecords = Auth::user()->patient->medicalRecords()->count();
                $totalVisits = Auth::user()->patient->appointments()->count();
                $completedVisits = Auth::user()->patient->appointments()->where('status', 'completed')->count();
                $availableDoctors = \App\Models\Doctor::where('is_available', true)->count();
            @endphp

            <!-- Upcoming Appointments -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-1 rounded-full">Active</span>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Upcoming Appointments</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $upcomingCount }}</p>
                <a href="{{ route('patient.appointments.index') }}"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium inline-flex items-center">
                    View all
                    <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Medical Records -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Medical Records</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalRecords }}</p>
                <a href="{{ route('patient.medical-records.index') }}"
                    class="text-sm text-green-600 hover:text-green-700 font-medium inline-flex items-center">
                    View records
                    <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <!-- Total Visits -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 rounded-lg">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Total Visits</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $totalVisits }}</p>
                <p class="text-sm text-gray-600">
                    <span class="text-green-600 font-semibold">{{ $completedVisits }}</span> completed
                </p>
            </div>

            <!-- Available Doctors -->
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 rounded-lg">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-sm font-medium text-gray-500 mb-1">Available Doctors</h3>
                <p class="text-3xl font-bold text-gray-900 mb-2">{{ $availableDoctors }}</p>
                <a href="{{ route('patient.doctors.index') }}"
                    class="text-sm text-orange-600 hover:text-orange-700 font-medium inline-flex items-center">
                    Browse doctors
                    <svg class="h-4 w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Next Appointment Preview -->
        @php
            $nextAppointment = Auth::user()
                ->patient->appointments()
                ->with(['schedule.doctor.user', 'schedule.doctor.specialty'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereHas('schedule', function ($q) {
                    $q->where('schedule_date', '>=', now()->toDateString());
                })
                ->orderBy('created_at', 'asc')
                ->first();
        @endphp

        @if ($nextAppointment)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-lg font-bold text-white flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Your Next Appointment
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                        <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-bold text-gray-900">Dr.
                                        {{ $nextAppointment->schedule->doctor->user->name }}</h3>
                                    <p class="text-sm text-gray-600">
                                        {{ $nextAppointment->schedule->doctor->specialty->name }}</p>
                                    <div class="mt-3 space-y-2">
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
                                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                            </svg>
                                            <span
                                                class="text-gray-600">{{ $nextAppointment->schedule->schedule_date->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($nextAppointment->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4">
                            <a href="{{ route('patient.appointments.show', $nextAppointment) }}"
                                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm hover:shadow transition duration-150">
                                View Details
                                <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No Upcoming Appointments</h3>
                <p class="text-gray-600 mb-4">You don't have any scheduled appointments at the moment.</p>
                <a href="{{ route('patient.doctors.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-lg shadow-sm transition duration-150">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Find a Doctor
                </a>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-6">Quick Actions</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('patient.doctors.index') }}"
                    class="group flex items-center space-x-4 p-4 border-2 border-gray-200 rounded-lg hover:border-primary-500 hover:bg-primary-50 transition-all duration-200">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-primary-100 group-hover:bg-primary-200 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-primary-700">Find Doctors</h3>
                        <p class="text-sm text-gray-500">Browse specialists</p>
                    </div>
                </a>

                <a href="{{ route('patient.appointments.index') }}"
                    class="group flex items-center space-x-4 p-4 border-2 border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all duration-200">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-green-700">Appointments</h3>
                        <p class="text-sm text-gray-500">Manage bookings</p>
                    </div>
                </a>

                <a href="{{ route('patient.medical-records.index') }}"
                    class="group flex items-center space-x-4 p-4 border-2 border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-all duration-200">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-purple-700">Medical Records</h3>
                        <p class="text-sm text-gray-500">View history</p>
                    </div>
                </a>

                <a href="{{ route('profile.edit') }}"
                    class="group flex items-center space-x-4 p-4 border-2 border-gray-200 rounded-lg hover:border-orange-500 hover:bg-orange-50 transition-all duration-200">
                    <div
                        class="flex-shrink-0 w-12 h-12 bg-orange-100 group-hover:bg-orange-200 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-orange-700">My Profile</h3>
                        <p class="text-sm text-gray-500">Update info</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Health Tips -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl border border-green-200 p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Health Tips</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 border border-green-200">
                    <div class="text-2xl mb-2">💧</div>
                    <h3 class="font-semibold text-gray-900 mb-1">Stay Hydrated</h3>
                    <p class="text-sm text-gray-600">Drink 8 glasses of water daily.</p>
                </div>

                <div class="bg-white rounded-lg p-4 border border-green-200">
                    <div class="text-2xl mb-2">🏃</div>
                    <h3 class="font-semibold text-gray-900 mb-1">Exercise Regularly</h3>
                    <p class="text-sm text-gray-600">30 minutes daily activity.</p>
                </div>

                <div class="bg-white rounded-lg p-4 border border-green-200">
                    <div class="text-2xl mb-2">😴</div>
                    <h3 class="font-semibold text-gray-900 mb-1">Quality Sleep</h3>
                    <p class="text-sm text-gray-600">Get 7–9 hours each night.</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.patient>
