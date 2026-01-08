<x-layouts.patient>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">My Appointments</h1>
            <p class="mt-2 text-gray-600">View and manage your appointments</p>
        </div>

        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Appointments</h2>

            @if($upcomingAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingAppointments as $appointment)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-500 transition duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Appointment Number -->
                                    <div class="flex items-center mb-2">
                                        <span class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                            {{ $appointment->appointment_number }}
                                        </span>
                                        <span class="ml-2 badge {{ $appointment->status_badge_class }} badge-sm">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>

                                    <!-- Doctor Info -->
                                    <div class="mb-3">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Dr. {{ $appointment->schedule->doctor->user->name }}
                                        </h3>
                                        <p class="text-sm text-secondary-600">
                                            {{ $appointment->schedule->doctor->specialty->name }}
                                        </p>
                                    </div>

                                    <!-- Date & Time -->
                                    <div class="space-y-1">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            {{ $appointment->schedule->formatted_date }}
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $appointment->formatted_time }}
                                        </div>
                                    </div>

                                    @if($appointment->reason)
                                        <div class="mt-3 text-sm text-gray-600">
                                            <span class="font-medium">Reason:</span> {{ Str::limit($appointment->reason, 80) }}
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <a href="{{ route('patient.appointments.show', $appointment) }}"
                                       class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        View Details
                                    </a>
                                    @if($appointment->canBeCancelled())
                                        <form method="POST" action="{{ route('patient.appointments.destroy', $appointment) }}"
                                              onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No upcoming appointments</h3>
                    <p class="mt-2 text-sm text-gray-500">Book an appointment with a doctor to get started</p>
                    <a href="{{ route('patient.doctors.index') }}"
                       class="mt-4 inline-block px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-md transition duration-150">
                        Find Doctors
                    </a>
                </div>
            @endif
        </div>

        <!-- Past Appointments -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Past Appointments</h2>

            @if($pastAppointments->count() > 0)
                <div class="space-y-4">
                    @foreach($pastAppointments as $appointment)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Appointment Number -->
                                    <div class="flex items-center mb-2">
                                        <span class="text-xs font-mono bg-white text-gray-700 px-2 py-1 rounded border border-gray-200">
                                            {{ $appointment->appointment_number }}
                                        </span>
                                        <span class="ml-2 badge {{ $appointment->status_badge_class }} badge-sm">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>

                                    <!-- Doctor Info -->
                                    <div class="mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Dr. {{ $appointment->schedule->doctor->user->name }}
                                        </h3>
                                        <p class="text-sm text-secondary-600">
                                            {{ $appointment->schedule->doctor->specialty->name }}
                                        </p>
                                    </div>

                                    <!-- Date & Time -->
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $appointment->schedule->formatted_date }} at {{ $appointment->formatted_time }}
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="ml-4">
                                    <a href="{{ route('patient.appointments.show', $appointment) }}"
                                       class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <p>No past appointments</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.patient>
