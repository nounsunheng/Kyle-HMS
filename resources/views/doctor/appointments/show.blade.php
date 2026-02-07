<x-layouts.doctor>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.appointments.index') }}"
            class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Appointments
        </a>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Whoops! There were some problems:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Appointment Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Appointment Details</h1>
                    <p class="mt-1 text-sm font-mono text-gray-600">{{ $appointment->appointment_number }}</p>
                </div>
                <span class="badge {{ $appointment->status_badge_class }} badge-lg">
                    {{ ucfirst($appointment->status) }}
                </span>
            </div>

            <!-- Patient Information -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Patient Name</label>
                        <p class="mt-1 text-base font-semibold text-gray-900">{{ $appointment->patient->user->name }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-base text-gray-900">{{ $appointment->patient->user->email }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Phone</label>
                        <p class="mt-1 text-base text-gray-900">{{ $appointment->patient->phone }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Date of Birth</label>
                        <p class="mt-1 text-base text-gray-900">
                            {{ $appointment->patient->date_of_birth->format('F d, Y') }}
                            ({{ $appointment->patient->age }} years old)
                        </p>
                    </div>

                    @if ($appointment->patient->blood_type)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Blood Type</label>
                            <p class="mt-1 text-base text-gray-900">{{ $appointment->patient->blood_type }}</p>
                        </div>
                    @endif

                    @if ($appointment->patient->allergies)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Allergies</label>
                            <p class="mt-1 text-base text-red-600 font-semibold">⚠️ {{ $appointment->patient->allergies }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="border-t border-gray-200 mt-6 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Appointment Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Date</label>
                        <p class="mt-1 text-base text-gray-900">{{ $appointment->schedule->formatted_date }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Time</label>
                        <p class="mt-1 text-base text-gray-900">{{ $appointment->formatted_time }}</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Duration</label>
                        <p class="mt-1 text-base text-gray-900">{{ $appointment->schedule->duration_per_appointment }}
                            minutes</p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500">Booked On</label>
                        <p class="mt-1 text-base text-gray-900">{{ $appointment->created_at->format('F d, Y g:i A') }}
                        </p>
                    </div>
                </div>

                @if ($appointment->reason)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Reason for Visit</label>
                        <p class="mt-2 text-base text-gray-900 bg-gray-50 p-4 rounded-lg">{{ $appointment->reason }}</p>
                    </div>
                @endif

                @if ($appointment->notes)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Notes</label>
                        <p class="mt-2 text-base text-gray-900 bg-gray-50 p-4 rounded-lg whitespace-pre-line">{{ $appointment->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            @if (in_array($appointment->status, ['pending', 'confirmed']))
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    <div class="flex flex-wrap gap-3">

                        <!-- Confirm Button (only for pending) -->
                        @if ($appointment->status === 'pending')
                            <form method="POST" action="{{ route('doctor.appointments.confirm', $appointment) }}">
                                @csrf
                                <button type="submit"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150 flex items-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Confirm Appointment
                                </button>
                            </form>
                        @endif

                        <!-- Complete Button -->
                        <button type="button" onclick="showCompleteModal()"
                            class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition duration-150 flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Complete & Submit Medical Record
                        </button>

                        <!-- No Show Button -->
                        <form method="POST" action="{{ route('doctor.appointments.no-show', $appointment) }}"
                            onsubmit="return confirm('Are you sure the patient did not show up for this appointment?');">
                            @csrf
                            <button type="submit"
                                class="px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150 flex items-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Mark as No Show
                            </button>
                        </form>

                        <!-- Cancel Button -->
                        <button type="button" onclick="showCancelModal()"
                            class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition duration-150 flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel Appointment
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Medical Record (if completed) -->
        @if ($appointment->status === 'completed' && $appointment->medicalRecord)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Medical Record
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Diagnosis</label>
                        <p class="mt-1 text-base text-gray-900 bg-blue-50 p-3 rounded-lg">{{ $appointment->medicalRecord->diagnosis }}</p>
                    </div>

                    @if ($appointment->medicalRecord->treatment)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Treatment</label>
                            <p class="mt-1 text-base text-gray-900 bg-purple-50 p-3 rounded-lg whitespace-pre-line">{{ $appointment->medicalRecord->treatment }}</p>
                        </div>
                    @endif

                    @if ($appointment->medicalRecord->prescription)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Prescription</label>
                            <p class="mt-1 text-base text-gray-900 bg-green-50 p-3 rounded-lg whitespace-pre-line">{{ $appointment->medicalRecord->prescription }}</p>
                        </div>
                    @endif

                    @if ($appointment->medicalRecord->notes)
                        <div>
                            <label class="text-sm font-medium text-gray-500">Additional Notes</label>
                            <p class="mt-1 text-base text-gray-900 bg-gray-50 p-3 rounded-lg whitespace-pre-line">{{ $appointment->medicalRecord->notes }}</p>
                        </div>
                    @endif

                    @if ($appointment->medicalRecord->has_file)
                        <div class="border-t border-gray-200 pt-4">
                            <label class="text-sm font-medium text-gray-500">Medical Record File</label>
                            <div class="mt-2 flex items-center space-x-4 bg-blue-50 p-4 rounded-lg">
                                <div class="flex-shrink-0">
                                    <svg class="h-12 w-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $appointment->medicalRecord->file_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $appointment->medicalRecord->formatted_file_size }}</p>
                                </div>
                                <a href="{{ $appointment->medicalRecord->file_url }}" download
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
            </div>
        @endif
    </div>

    <!-- Complete Appointment Modal with Medical Record Form -->
    <div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-10 mx-auto p-6 border w-full max-w-3xl shadow-lg rounded-lg bg-white my-8">
            <div class="mt-3">
                <h3 class="text-xl font-bold text-gray-900 mb-2 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Complete Appointment & Submit Medical Record
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    Fill in the medical details and upload the medical record file to complete this appointment.
                </p>

                <form id="completeForm" method="POST" action="{{ route('doctor.appointments.complete', $appointment) }}" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-5 max-h-[60vh] overflow-y-auto pr-2">
                        <!-- Diagnosis -->
                        <div>
                            <label for="diagnosis" class="block text-sm font-semibold text-gray-700 mb-2">
                                Diagnosis *
                            </label>
                            <textarea id="diagnosis" name="diagnosis" required rows="3"
                                placeholder="Enter the patient's diagnosis..."
                                class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-green-500">{{ old('diagnosis') }}</textarea>
                        </div>

                        <!-- Treatment -->
                        <div>
                            <label for="treatment" class="block text-sm font-semibold text-gray-700 mb-2">
                                Treatment Plan (Optional)
                            </label>
                            <textarea id="treatment" name="treatment" rows="3"
                                placeholder="Enter the treatment plan..."
                                class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-green-500">{{ old('treatment') }}</textarea>
                        </div>

                        <!-- Prescription -->
                        <div>
                            <label for="prescription" class="block text-sm font-semibold text-gray-700 mb-2">
                                Prescription (Optional)
                            </label>
                            <textarea id="prescription" name="prescription" rows="3"
                                placeholder="Enter prescribed medications and dosages..."
                                class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-green-500">{{ old('prescription') }}</textarea>
                        </div>

                        <!-- Additional Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                Additional Notes (Optional)
                            </label>
                            <textarea id="notes" name="notes" rows="3"
                                placeholder="Add any additional notes about the consultation..."
                                class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-green-500">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Medical Record File Upload -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50">
                            <label for="medical_record_file" class="block text-sm font-semibold text-gray-700 mb-3">
                                Medical Record File *
                                <span class="text-xs font-normal text-gray-500">(PDF, DOC, DOCX, JPG, PNG - Max 10MB)</span>
                            </label>
                            <input type="file"
                                id="medical_record_file"
                                name="medical_record_file"
                                required
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                onchange="showFileName(this)"
                                class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p id="file-name" class="mt-2 text-sm text-gray-600 hidden"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                        <button type="button" onclick="closeCompleteModal()"
                            class="px-5 py-2.5 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-md transition duration-150">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md transition duration-150 flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Complete Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Appointment Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50" style="display: none;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Cancel Appointment</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Please provide a reason for cancelling this appointment.
                </p>

                <form method="POST" action="{{ route('doctor.appointments.cancel', $appointment) }}">
                    @csrf

                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                            Reason for Cancellation *
                        </label>
                        <textarea id="cancellation_reason" name="cancellation_reason" required rows="4"
                            placeholder="Please provide a reason for cancelling this appointment..."
                            class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-red-500 focus:ring-red-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeCancelModal()"
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold rounded-md transition duration-150">
                            Close
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition duration-150">
                            Cancel Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showCompleteModal() {
            const modal = document.getElementById('completeModal');
            modal.style.display = 'block';
            modal.classList.remove('hidden');
            console.log('Complete modal opened');
        }

        function closeCompleteModal() {
            const modal = document.getElementById('completeModal');
            modal.style.display = 'none';
            modal.classList.add('hidden');
            // Clear form
            document.getElementById('completeForm').reset();
            document.getElementById('file-name').classList.add('hidden');
        }

        function showCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.style.display = 'block';
            modal.classList.remove('hidden');
            document.getElementById('cancellation_reason').value = '';
        }

        function closeCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.style.display = 'none';
            modal.classList.add('hidden');
        }

        function showFileName(input) {
            const fileNameDisplay = document.getElementById('file-name');
            if (input.files && input.files[0]) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2);
                fileNameDisplay.textContent = `Selected: ${fileName} (${fileSize} MB)`;
                fileNameDisplay.classList.remove('hidden');
                console.log('File selected:', fileName, fileSize + 'MB');
            } else {
                fileNameDisplay.classList.add('hidden');
            }
        }

        // Close modals when clicking outside
        document.getElementById('completeModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCompleteModal();
            }
        });

        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });

        // Debug form submission
        document.getElementById('completeForm').addEventListener('submit', function(e) {
            console.log('Form submitting...');
            console.log('Diagnosis:', document.getElementById('diagnosis').value);
            console.log('File:', document.getElementById('medical_record_file').files[0]);

            // Validate
            if (!document.getElementById('diagnosis').value) {
                e.preventDefault();
                alert('Diagnosis is required!');
                return false;
            }
            if (!document.getElementById('medical_record_file').files[0]) {
                e.preventDefault();
                alert('Medical record file is required!');
                return false;
            }
        });
    </script>
</x-layouts.doctor>
