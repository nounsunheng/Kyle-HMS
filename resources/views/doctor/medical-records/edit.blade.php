<x-layouts.doctor>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.medical-records.show', $medicalRecord) }}" class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Medical Record
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Medical Record</h1>
            <p class="mt-2 text-gray-600">
                For: <span class="font-semibold">{{ $medicalRecord->patient->user->name }}</span> •
                {{ $medicalRecord->formatted_visit_date }}
            </p>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('doctor.medical-records.update', $medicalRecord) }}" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            @method('PUT')

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
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
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
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('treatment', $medicalRecord->treatment) }}</textarea>
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
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('prescription', $medicalRecord->prescription) }}</textarea>
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
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">{{ old('notes', $medicalRecord->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('doctor.medical-records.show', $medicalRecord) }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                        Update Medical Record
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.doctor>
