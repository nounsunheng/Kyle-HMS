<x-layouts.doctor>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.patients.show', $medicalRecord->patient) }}" class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Patient
        </a>

        <!-- Medical Record Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Medical Record</h1>
                    <p class="mt-2 text-gray-600">{{ $medicalRecord->formatted_visit_date }}</p>
                </div>
                <a href="{{ route('doctor.medical-records.edit', $medicalRecord) }}"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                    Edit Record
                </a>
            </div>

            <!-- Patient Info -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Patient Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Name</p>
                        <p class="font-semibold text-gray-900">{{ $medicalRecord->patient->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Age</p>
                        <p class="font-semibold text-gray-900">{{ $medicalRecord->patient->age }} years</p>
                    </div>
                    @if($medicalRecord->appointment)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Related Appointment</p>
                            <a href="{{ route('doctor.appointments.show', $medicalRecord->appointment) }}"
                               class="font-mono text-secondary-600 hover:text-secondary-700">
                                {{ $medicalRecord->appointment->appointment_number }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Medical Information -->
            <div class="space-y-6">
                <!-- Diagnosis -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Diagnosis</h3>
                    <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->diagnosis }}</p>
                </div>

                @if($medicalRecord->treatment)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Treatment Plan</h3>
                        <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->treatment }}</p>
                    </div>
                @endif

                @if($medicalRecord->prescription)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Prescription</h3>
                        <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->prescription }}</p>
                    </div>
                @endif

                @if($medicalRecord->notes)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">Additional Notes</h3>
                        <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->notes }}</p>
                    </div>
                @endif

                @if($medicalRecord->has_file)
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Medical Record File</h3>
                        <div class="flex items-center space-x-4 bg-blue-50 p-4 rounded-lg">
                            <div class="flex-shrink-0">
                                <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $medicalRecord->file_name }}</p>
                                <p class="text-sm text-gray-600">{{ $medicalRecord->formatted_file_size }}</p>
                            </div>
                            <a href="{{ $medicalRecord->file_url }}" download
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Metadata -->
            <div class="border-t border-gray-200 mt-6 pt-4">
                <p class="text-sm text-gray-500">
                    Created on {{ $medicalRecord->created_at->format('F d, Y \a\t g:i A') }}
                    @if($medicalRecord->updated_at != $medicalRecord->created_at)
                        • Last updated {{ $medicalRecord->updated_at->format('F d, Y \a\t g:i A') }}
                    @endif
                </p>
            </div>
        </div>
    </div>
</x-layouts.doctor>
