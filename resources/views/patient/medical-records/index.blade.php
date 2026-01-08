<x-layouts.patient>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Medical Records</h1>
            <p class="mt-2 text-gray-600">View your medical history and records</p>
        </div>

        <!-- Medical Records List -->
        @if($medicalRecords->count() > 0)
            <div class="space-y-4">
                @foreach($medicalRecords as $record)
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Date -->
                                <div class="flex items-center mb-3">
                                    <svg class="h-5 w-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-lg font-semibold text-gray-900">{{ $record->formatted_visit_date }}</span>
                                </div>

                                <!-- Doctor Info -->
                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">Attending Physician</p>
                                    <p class="font-semibold text-gray-900">Dr. {{ $record->doctor->user->name }}</p>
                                    <p class="text-sm text-secondary-600">{{ $record->doctor->specialty->name }}</p>
                                </div>

                                <!-- Diagnosis Preview -->
                                <div class="mb-3">
                                    <p class="text-sm text-gray-500 font-medium">Diagnosis</p>
                                    <p class="text-gray-900">{{ Str::limit($record->diagnosis, 150) }}</p>
                                </div>

                                <!-- Appointment Reference -->
                                @if($record->appointment)
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium">Appointment:</span>
                                        <span class="font-mono text-xs">{{ $record->appointment->appointment_number }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- View Button -->
                            <div class="ml-4">
                                <a href="{{ route('patient.medical-records.show', $record) }}"
                                   class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-md transition duration-150">
                                    View Details
                                    <svg class="h-4 w-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $medicalRecords->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No medical records yet</h3>
                <p class="mt-2 text-sm text-gray-500">Your medical records will appear here after your appointments</p>
            </div>
        @endif
    </div>
</x-layouts.patient>
