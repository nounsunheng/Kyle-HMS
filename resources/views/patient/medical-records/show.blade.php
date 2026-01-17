<x-layouts.patient>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('patient.medical-records.index') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Medical Records
        </a>

        <!-- Medical Record Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Medical Record</h1>
                <p class="mt-1 text-sm text-gray-600">Visit Date: {{ $medicalRecord->formatted_visit_date }}</p>
            </div>

            <!-- Doctor Information -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Attending Physician
                </h2>
                <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                    <div class="flex items-start">
                        <span class="text-sm text-gray-500 w-32">Name:</span>
                        <span class="font-semibold text-gray-900">Dr. {{ $medicalRecord->doctor->user->name }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-sm text-gray-500 w-32">Specialty:</span>
                        <span class="text-gray-900">{{ $medicalRecord->doctor->specialty->name }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-sm text-gray-500 w-32">License:</span>
                        <span class="text-gray-900 font-mono text-sm">{{ $medicalRecord->doctor->license_number }}</span>
                    </div>
                    <div class="flex items-start">
                        <span class="text-sm text-gray-500 w-32">Contact:</span>
                        <span class="text-gray-900">{{ $medicalRecord->doctor->phone }}</span>
                    </div>
                </div>
            </div>

            <!-- Appointment Reference -->
            @if($medicalRecord->appointment)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Related Appointment
                    </h2>
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-700">Appointment Number</p>
                                <p class="font-mono text-blue-900 font-semibold">{{ $medicalRecord->appointment->appointment_number }}</p>
                            </div>
                            <a href="{{ route('patient.appointments.show', $medicalRecord->appointment) }}"
                               class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                View Appointment →
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Diagnosis -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Diagnosis
                </h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->diagnosis }}</p>
                </div>
            </div>

            <!-- Treatment -->
            @if($medicalRecord->treatment)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Treatment Plan
                    </h2>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->treatment }}</p>
                    </div>
                </div>
            @endif

            <!-- Prescription -->
            @if($medicalRecord->prescription)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        Prescription
                    </h2>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <div class="flex items-start space-x-2 mb-2">
                            <svg class="h-5 w-5 text-orange-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-sm text-orange-800 font-medium">Please follow the prescription as directed by your doctor</p>
                        </div>
                        <p class="text-gray-900 whitespace-pre-line mt-3">{{ $medicalRecord->prescription }}</p>
                    </div>
                </div>
            @endif

            <!-- Additional Notes -->
            @if($medicalRecord->notes)
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4 flex items-center">
                        <svg class="h-6 w-6 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        Additional Notes
                    </h2>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Record Information -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Record Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Visit Date</p>
                        <p class="font-semibold text-gray-900">{{ $medicalRecord->formatted_visit_date }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Record Created</p>
                        <p class="text-gray-900">{{ $medicalRecord->created_at->format('F d, Y \a\t g:i A') }}</p>
                    </div>
                    @if($medicalRecord->updated_at != $medicalRecord->created_at)
                        <div>
                            <p class="text-sm text-gray-500">Last Updated</p>
                            <p class="text-gray-900">{{ $medicalRecord->updated_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Important Notice -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="h-5 w-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-semibold text-blue-900 mb-1">Important Notice</h3>
                        <p class="text-sm text-blue-800">
                            This medical record is confidential. If you have any questions about your diagnosis, treatment, or prescription,
                            please contact Dr. {{ $medicalRecord->doctor->user->name }} at {{ $medicalRecord->doctor->phone }}.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
                <a href="{{ route('patient.medical-records.index') }}"
                   class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                    Back to Records
                </a>
                <button onclick="window.print()"
                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-md transition duration-150 flex items-center">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Record
                </button>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
        }
    </style>
</x-layouts.patient>
