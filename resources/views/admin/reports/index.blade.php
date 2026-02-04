<x-layouts.admin>
    <div class="space-y-6">
        <!-- Page Header with Export Button -->
        <div class="bg-white rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">System Reports</h1>
                <p class="mt-2 text-gray-600">View system statistics and reports</p>
            </div>
            <a href="{{ route('admin.reports.export') }}"
               class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm transition duration-150">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Reports
            </a>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Active Doctors</h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Doctor::where('is_available', true)->count() }}</p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">This Month</h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Appointment::whereMonth('created_at', now()->month)->count() }}</p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Appointment::where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Status Breakdown -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Appointment Status Breakdown</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div class="text-center p-4 bg-yellow-50 rounded-lg border-2 border-yellow-200">
                    <p class="text-3xl font-bold text-yellow-600">{{ \App\Models\Appointment::where('status', 'pending')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 font-medium">Pending</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg border-2 border-blue-200">
                    <p class="text-3xl font-bold text-blue-600">{{ \App\Models\Appointment::where('status', 'confirmed')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 font-medium">Confirmed</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg border-2 border-green-200">
                    <p class="text-3xl font-bold text-green-600">{{ \App\Models\Appointment::where('status', 'completed')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 font-medium">Completed</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg border-2 border-red-200">
                    <p class="text-3xl font-bold text-red-600">{{ \App\Models\Appointment::where('status', 'cancelled')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 font-medium">Cancelled</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg border-2 border-gray-300">
                    <p class="text-3xl font-bold text-gray-900">{{ \App\Models\Appointment::where('status', 'no_show')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 font-medium">No Show</p>
                </div>
                <div class="text-center p-4 bg-orange-50 rounded-lg border-2 border-orange-200">
                    <p class="text-3xl font-bold text-orange-600">{{ \App\Models\Appointment::where('status', 'expired')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1 font-medium">Expired</p>
                </div>
            </div>
        </div>

        <!-- Top Specialties -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Top Specialties by Doctor Count</h2>
                <a href="{{ route('admin.specialties.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    View All →
                </a>
            </div>
            <div class="space-y-3">
                @foreach(\App\Models\Specialty::withCount('doctors')->orderBy('doctors_count', 'desc')->limit(5)->get() as $specialty)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                        <div class="flex items-center">
                            <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-900">{{ $specialty->name }}</span>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">
                            {{ $specialty->doctors_count }} {{ Str::plural('doctor', $specialty->doctors_count) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity & Export Reminder -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Appointments -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Appointments</h2>
                    <a href="{{ route('admin.appointments.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        View All →
                    </a>
                </div>
                @php
                    $recentAppointments = \App\Models\Appointment::with(['patient.user', 'schedule.doctor.user'])
                        ->orderBy('created_at', 'desc')
                        ->limit(5)
                        ->get();
                @endphp

                @if($recentAppointments->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentAppointments as $appointment)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <div>
                                    <p class="font-medium text-gray-900 text-sm">{{ $appointment->patient->user->name }}</p>
                                    <p class="text-xs text-gray-600">
                                        with Dr. {{ $appointment->schedule->doctor->user->name }} •
                                        {{ $appointment->schedule->schedule_date->format('M d, Y') }}
                                    </p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $appointment->status_badge_class }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center py-8 text-gray-500">No appointments yet</p>
                @endif
            </div>

            <!-- Export Reminder Card -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-xl font-bold mb-2">Need to Export Data?</h3>
                        <p class="text-blue-100 mb-4">
                            Download detailed reports in CSV or Excel format. Export doctors, patients, appointments, medical records, and system summaries with advanced filters.
                        </p>
                        <a href="{{ route('admin.reports.export') }}"
                           class="inline-flex items-center px-5 py-2.5 bg-white text-blue-600 font-semibold rounded-md hover:bg-blue-50 transition duration-150 shadow-md">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            Go to Export Center
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Overview -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">System Health Overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="border-l-4 border-green-500 pl-4">
                    <h3 class="text-sm font-medium text-gray-600">Total Doctors</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\Doctor::count() }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        {{ \App\Models\Doctor::where('is_available', true)->count() }} available
                    </p>
                </div>
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="text-sm font-medium text-gray-600">Total Patients</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\Patient::count() }}</p>
                    <p class="text-sm text-blue-600 mt-1">
                        {{ \App\Models\Patient::whereMonth('created_at', now()->month)->count() }} new this month
                    </p>
                </div>
                <div class="border-l-4 border-purple-500 pl-4">
                    <h3 class="text-sm font-medium text-gray-600">Total Appointments</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\Appointment::count() }}</p>
                    <p class="text-sm text-purple-600 mt-1">
                        {{ \App\Models\Appointment::whereIn('status', ['pending', 'confirmed'])->count() }} upcoming
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
