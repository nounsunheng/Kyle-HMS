<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.appointments.index') }}"
            class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Name</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->user->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->user->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->phone }}</p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('admin.patients.show', $appointment->patient) }}"
                                class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View Full Patient Record →
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Doctor Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Doctor Information</h2>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Doctor</p>
                                <p class="font-semibold text-gray-900">Dr.
                                    {{ $appointment->schedule->doctor->user->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Specialty</p>
                                <p class="font-semibold text-gray-900">
                                    {{ $appointment->schedule->doctor->specialty->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Contact</p>
                                <p class="font-semibold text-gray-900">{{ $appointment->schedule->doctor->phone }}</p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('admin.doctors.show', $appointment->schedule->doctor) }}"
                                class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View Doctor Profile →
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointment Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Date</p>
                            <p class="font-semibold text-gray-900">{{ $appointment->schedule->formatted_date }}</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Time</p>
                            <p class="font-semibold text-gray-900">{{ $appointment->formatted_time }}</p>
                        </div>
                    </div>

                    @if ($appointment->reason)
                        <div class="flex items-start md:col-span-2">
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Reason for Visit</p>
                                <p class="text-gray-900">{{ $appointment->reason }}</p>
                            </div>
                        </div>
                    @endif

                    @if ($appointment->notes)
                        <div class="flex items-start md:col-span-2">
                            <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Notes</p>
                                <p class="text-gray-900">{{ $appointment->notes }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-start">
                        <svg class="h-5 w-5 mr-3 text-blue-600 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm text-gray-500">Booked On</p>
                            <p class="text-gray-900">{{ $appointment->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Record -->
            @if ($appointment->medicalRecord)
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Medical Record</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Diagnosis</p>
                                <p class="text-gray-900">{{ $appointment->medicalRecord->diagnosis }}</p>
                            </div>
                            @if ($appointment->medicalRecord->treatment)
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Treatment</p>
                                    <p class="text-gray-900">{{ $appointment->medicalRecord->treatment }}</p>
                                </div>
                            @endif
                            @if ($appointment->medicalRecord->prescription)
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Prescription</p>
                                    <p class="text-gray-900">{{ $appointment->medicalRecord->prescription }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            @if (in_array($appointment->status, ['pending', 'confirmed']))
                <button type="button"
                    onclick="openCancelModal('{{ route('admin.appointments.cancel', $appointment) }}', '{{ $appointment->appointment_number }}')"
                    class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition duration-150">
                    Cancel Appointment
                </button>
            @endif
        </div>
    </div>
    <x-delete-modal id="cancelModal" title="Cancel Appointment"
        message="Are you sure you want to cancel this appointment?" />

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function openCancelModal(url, number) {
            document.getElementById('modal-title').textContent = 'Cancel Appointment';
            document.getElementById('modal-message').textContent =
                `Are you sure you want to cancel appointment ${number}? The patient will need to rebook if they still need this appointment.`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }
    </script>
</x-layouts.admin>
