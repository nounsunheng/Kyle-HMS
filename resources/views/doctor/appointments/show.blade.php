<x-layouts.doctor>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.appointments.index') }}" class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Appointments
        </a>

        <!-- Appointment Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Appointment Details</h1>
                    <p class="mt-1 text-sm text-gray-600">{{ $appointment->appointment_number }}</p>
                </div>
                <span class="badge {{ $appointment->status_badge_class }}">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Patient Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h2>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->user->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->phone }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Age</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->age }} years</p>
                            </div>
                        </div>

                        @if($appointment->patient->blood_type)
                            <div class="flex items-start">
                                <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">Blood Type</p>
                                    <p class="font-semibold text-gray-900">{{ $appointment->patient->blood_type }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('doctor.patients.show', $appointment->patient) }}"
                               class="text-secondary-600 hover:text-secondary-700 text-sm font-medium">
                                View Full Patient Record →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Appointment Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h2>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Date</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->schedule->formatted_date }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Time</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->formatted_time }}</p>
                            </div>
                        </div>

                        @if($appointment->reason)
                            <div class="flex items-start">
                                <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">Reason for Visit</p>
                                    <p class="text-gray-900">{{ $appointment->reason }}</p>
                                </div>
                            </div>
                        @endif

                        @if($appointment->notes)
                            <div class="flex items-start">
                                <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <div>
                                    <p class="text-sm text-gray-500">Notes</p>
                                    <p class="text-gray-900">{{ $appointment->notes }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Booked On</p>
                                <p class="text-gray-900">{{ $appointment->created_at->format('F d, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Status -->
            @if(in_array($appointment->status, ['pending', 'confirmed']) && $appointment->schedule->schedule_date->isToday())
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h3>
                    <form method="POST" action="{{ route('doctor.appointments.updateStatus', $appointment) }}" class="flex items-center space-x-4">
                        @csrf
                        @method('PATCH')

                        <select name="status" required
                                class="rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                            <option value="confirmed" {{ $appointment->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="completed">Completed</option>
                            <option value="no_show">No Show</option>
                        </select>

                        <button type="submit"
                                class="px-6 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                            Update Status
                        </button>
                    </form>
                </div>
            @endif

            <!-- Medical Record -->
            @if($appointment->medicalRecord)
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Record</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">A medical record has been created for this appointment.</p>
                        <a href="{{ route('doctor.patients.show', $appointment->patient) }}"
                           class="text-secondary-600 hover:text-secondary-700 text-sm font-medium">
                            View Medical Record →
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.doctor>
