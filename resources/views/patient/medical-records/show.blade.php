<x-layouts.patient>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('patient.medical-records.index') }}"
            class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Medical Records
        </a>

        <!-- Medical Record Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Medical Record</h1>
                    <p class="mt-2 text-gray-600">{{ $medicalRecord->formatted_visit_date }}</p>
                </div>
                <span class="px-4 py-2 bg-green-100 text-green-800 font-semibold rounded-lg">
                    Completed
                </span>
            </div>

            <!-- Doctor Information -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Doctor Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Doctor Name</p>
                        <p class="font-semibold text-gray-900">Dr. {{ $medicalRecord->doctor->user->name }}</p>
                    </div>
                    @if($medicalRecord->doctor->specialty)
                        <div>
                            <p class="text-sm text-gray-500">Specialty</p>
                            <p class="font-semibold text-gray-900">{{ $medicalRecord->doctor->specialty->name }}</p>
                        </div>
                    @endif
                    @if($medicalRecord->appointment)
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500">Related Appointment</p>
                            <a href="{{ route('patient.appointments.show', $medicalRecord->appointment) }}"
                                class="font-mono text-blue-600 hover:text-blue-700">
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
                    <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Diagnosis
                    </h3>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->diagnosis }}</p>
                    </div>
                </div>

                @if($medicalRecord->treatment)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Treatment Plan
                        </h3>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->treatment }}</p>
                        </div>
                    </div>
                @endif

                @if($medicalRecord->prescription)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            Prescription
                        </h3>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->prescription }}</p>
                        </div>
                    </div>
                @endif

                @if($medicalRecord->notes)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="h-5 w-5 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Additional Notes
                        </h3>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-900 whitespace-pre-line">{{ $medicalRecord->notes }}</p>
                        </div>
                    </div>
                @endif

                <!-- Medical Record File Download -->
                @if($medicalRecord->has_file)
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Medical Record File
                        </h3>
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-lg border-2 border-blue-200">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-16 h-16 bg-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="h-10 w-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-gray-900 text-lg">{{ $medicalRecord->file_name }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $medicalRecord->formatted_file_size }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Uploaded on {{ $medicalRecord->created_at->format('F d, Y') }}</p>
                                </div>
                                <a href="{{ $medicalRecord->file_url }}" download
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition duration-150 transform hover:scale-105">
                                    <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Download File
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="border-t border-gray-200 pt-6 mt-6">
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex">
                                <svg class="h-5 w-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-yellow-800">No file was attached to this medical record.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Metadata -->
            <div class="border-t border-gray-200 mt-8 pt-4">
                <p class="text-sm text-gray-500">
                    Record created on {{ $medicalRecord->created_at->format('F d, Y \a\t g:i A') }}
                    @if($medicalRecord->updated_at != $medicalRecord->created_at)
                        • Last updated {{ $medicalRecord->updated_at->format('F d, Y \a\t g:i A') }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Print Button -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <button onclick="window.print()"
                class="w-full inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition duration-150">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Medical Record
            </button>
        </div>
    </div>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</x-layouts.patient>
