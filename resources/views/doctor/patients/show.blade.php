<x-layouts.doctor>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.patients.index') }}" class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Patients
        </a>

        <!-- Patient Profile -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center">
                    <!-- Patient Profile Picture -->
                    <div class="h-20 w-20 rounded-full overflow-hidden flex-shrink-0 ring-4 ring-secondary-100 shadow-lg">
                        <img src="{{ $patient->profile_image_url }}"
                             alt="{{ $patient->user->name }}"
                             class="h-full w-full object-cover">
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $patient->user->name }}</h1>
                        <p class="text-lg text-gray-600 mt-1">Patient ID: #{{ str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>

            <!-- Patient Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Personal Information</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">Age</p>
                            <p class="font-semibold text-gray-900">{{ $patient->age }} years</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Gender</p>
                            <p class="font-semibold text-gray-900">{{ ucfirst($patient->gender) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Date of Birth</p>
                            <p class="font-semibold text-gray-900">{{ $patient->date_of_birth->format('F d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Contact Information</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-semibold text-gray-900">{{ $patient->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Phone</p>
                            <p class="font-semibold text-gray-900">{{ $patient->phone }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Emergency Contact</p>
                            <p class="font-semibold text-gray-900">{{ $patient->emergency_contact }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Medical Information</h3>
                    <div class="space-y-2">
                        @if($patient->blood_type)
                            <div>
                                <p class="text-xs text-gray-500">Blood Type</p>
                                <p class="font-semibold text-gray-900">{{ $patient->blood_type }}</p>
                            </div>
                        @endif
                        @if($patient->allergies)
                            <div>
                                <p class="text-xs text-gray-500">Allergies</p>
                                <p class="font-semibold text-red-600">{{ $patient->allergies }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($patient->address)
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Address</h3>
                    <p class="text-gray-900">{{ $patient->address }}</p>
                </div>
            @endif

            @if($patient->medical_history)
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Medical History</h3>
                    <p class="text-gray-900">{{ $patient->medical_history }}</p>
                </div>
            @endif
        </div>

        <!-- Appointments with this Patient -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Appointment History</h2>

            @if($patient->appointments->count() > 0)
                <div class="space-y-3">
                    @foreach($patient->appointments->sortByDesc('created_at') as $appointment)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <div class="flex items-center mb-2">
                                        <span class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                            {{ $appointment->appointment_number }}
                                        </span>
                                        <span class="ml-2 badge {{ $appointment->status_badge_class }} badge-sm">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600">
                                        {{ $appointment->schedule->formatted_date }} at {{ $appointment->formatted_time }}
                                    </p>
                                    @if($appointment->reason)
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="font-medium">Reason:</span> {{ Str::limit($appointment->reason, 80) }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('doctor.appointments.show', $appointment) }}"
                                   class="text-secondary-600 hover:text-secondary-700 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No appointments recorded</p>
            @endif
        </div>

        <!-- Medical Records -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Medical Records (By You)</h2>

            @if($patient->medicalRecords->count() > 0)
                <div class="space-y-4">
                    @foreach($patient->medicalRecords as $record)
                        <div class="border border-gray-200 rounded-lg p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">{{ $record->formatted_visit_date }}</p>
                                    @if($record->appointment)
                                        <p class="text-xs text-gray-500 font-mono">{{ $record->appointment->appointment_number }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Diagnosis</p>
                                    <p class="text-gray-900">{{ $record->diagnosis }}</p>
                                </div>

                                @if($record->treatment)
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Treatment</p>
                                        <p class="text-gray-900">{{ $record->treatment }}</p>
                                    </div>
                                @endif

                                @if($record->prescription)
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Prescription</p>
                                        <p class="text-gray-900">{{ $record->prescription }}</p>
                                    </div>
                                @endif

                                @if($record->notes)
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Notes</p>
                                        <p class="text-gray-900">{{ $record->notes }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2">No medical records created yet</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.doctor>
