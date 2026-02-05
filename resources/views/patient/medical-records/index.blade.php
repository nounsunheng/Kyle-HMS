<x-layouts.patient>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">My Medical Records</h1>
            <p class="mt-2 text-gray-600">View your medical history and download records</p>
        </div>

        <!-- Medical Records List -->
        @if ($medicalRecords->count() > 0)
            <div class="space-y-4">
                @foreach ($medicalRecords as $record)
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Record Header -->
                                <div class="flex items-center mb-3">
                                    <div class="flex items-center">
                                        <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            Medical Record - {{ $record->formatted_visit_date }}
                                        </h3>
                                    </div>
                                </div>

                                <!-- Doctor Info -->
                                <div class="mb-3">
                                    <p class="text-sm text-gray-600">
                                        <span class="font-medium">Doctor:</span>
                                        Dr. {{ $record->doctor->user->name }}
                                        @if($record->doctor->specialty)
                                            ({{ $record->doctor->specialty->name }})
                                        @endif
                                    </p>
                                </div>

                                <!-- Diagnosis Preview -->
                                <div class="bg-blue-50 p-3 rounded-lg mb-3">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Diagnosis:</p>
                                    <p class="text-sm text-gray-900">{{ Str::limit($record->diagnosis, 150) }}</p>
                                </div>

                                <!-- File Indicator -->
                                @if($record->has_file)
                                    <div class="flex items-center text-sm text-blue-600">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        File attached ({{ $record->formatted_file_size }})
                                    </div>
                                @endif

                                <!-- Related Appointment -->
                                @if($record->appointment)
                                    <div class="mt-2 text-xs text-gray-500">
                                        Related to appointment: {{ $record->appointment->appointment_number }}
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="ml-4">
                                <a href="{{ route('patient.medical-records.show', $record) }}"
                                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                                    <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($medicalRecords->hasPages())
                <div class="mt-6">
                    {{ $medicalRecords->links() }}
                </div>
            @endif
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No Medical Records Yet</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Your medical records will appear here after your doctor completes your appointments.
                </p>
                <a href="{{ route('patient.appointments.index') }}"
                    class="mt-4 inline-block text-blue-600 hover:text-blue-700">
                    View My Appointments
                </a>
            </div>
        @endif
    </div>
</x-layouts.patient>
