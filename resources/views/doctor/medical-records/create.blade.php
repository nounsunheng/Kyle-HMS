<x-layouts.doctor>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.appointments.show', $appointment) }}" class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Appointment
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Create Medical Record</h1>
            <p class="mt-2 text-gray-600">
                For: <span class="font-semibold">{{ $appointment->patient->user->name }}</span> •
                {{ $appointment->schedule->formatted_date }} at {{ $appointment->formatted_time }}
            </p>
        </div>

        <!-- Patient Quick Info -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-blue-700 font-medium">Age: {{ $appointment->patient->age }} years</p>
                </div>
                <div>
                    <p class="text-blue-700 font-medium">Gender: {{ ucfirst($appointment->patient->gender) }}</p>
                </div>
                @if($appointment->patient->blood_type)
                    <div>
                        <p class="text-blue-700 font-medium">Blood Type: {{ $appointment->patient->blood_type }}</p>
                    </div>
                @endif
                @if($appointment->patient->allergies)
                    <div class="md:col-span-3">
                        <p class="text-red-700 font-semibold">⚠️ Allergies: {{ $appointment->patient->allergies }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Create Form -->
        <form method="POST" action="{{ route('doctor.medical-records.store', $appointment) }}" class="bg-white rounded-lg shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <!-- Diagnosis -->
                <div>
                    <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-1">
                        Diagnosis *
                    </label>
                    <textarea id="diagnosis"
                              name="diagnosis"
                              rows="4"
                              required
                              placeholder="Enter the diagnosis..."
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('diagnosis') }}</textarea>
                    @error('diagnosis')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Treatment -->
                <div>
                    <label for="treatment" class="block text-sm font-medium text-gray-700 mb-1">
                        Treatment Plan
                    </label>
                    <textarea id="treatment"
                              name="treatment"
                              rows="4"
                              placeholder="Enter the treatment plan..."
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('treatment') }}</textarea>
                    @error('treatment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prescription -->
                <div>
                    <label for="prescription" class="block text-sm font-medium text-gray-700 mb-1">
                        Prescription
                    </label>
                    <textarea id="prescription"
                              name="prescription"
                              rows="4"
                              placeholder="Enter medications and dosages..."
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('prescription') }}</textarea>
                    @error('prescription')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                        Additional Notes
                    </label>
                    <textarea id="notes"
                              name="notes"
                              rows="3"
                              placeholder="Any additional notes or observations..."
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('doctor.appointments.show', $appointment) }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                        Create Medical Record
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.doctor>
