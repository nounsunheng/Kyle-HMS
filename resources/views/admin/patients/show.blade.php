<x-layouts.admin>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.patients.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Patients
        </a>

        <!-- Patient Profile -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center">
                    <!-- Patient Profile Picture -->
                    <div class="h-20 w-20 rounded-full overflow-hidden flex-shrink-0 ring-4 ring-blue-100 shadow-lg">
                        <img src="{{ $patient->profile_image_url }}"
                             alt="{{ $patient->user->name }}"
                             class="h-full w-full object-cover">
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $patient->user->name }}</h1>
                        <p class="text-lg text-gray-600 mt-1">Patient ID: #{{ str_pad($patient->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.patients.edit', $patient) }}"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                    Edit Patient
                </a>
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
                        @if ($patient->blood_type)
                            <div>
                                <p class="text-xs text-gray-500">Blood Type</p>
                                <p class="font-semibold text-gray-900">{{ $patient->blood_type }}</p>
                            </div>
                        @endif
                        @if ($patient->allergies)
                            <div>
                                <p class="text-xs text-gray-500">Allergies</p>
                                <p class="font-semibold text-red-600">{{ $patient->allergies }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs text-gray-500">Total Appointments</p>
                            <p class="font-semibold text-gray-900">{{ $patient->appointments->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($patient->address)
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Address</h3>
                    <p class="text-gray-900">{{ $patient->address }}</p>
                </div>
            @endif

            @if ($patient->medical_history)
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Medical History</h3>
                    <p class="text-gray-900">{{ $patient->medical_history }}</p>
                </div>
            @endif
        </div>

        <!-- Appointments History -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Appointment History</h2>

            @if ($patient->appointments->count() > 0)
                <div class="space-y-3">
                    @foreach ($patient->appointments->sortByDesc('created_at')->take(10) as $appointment)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div class="flex items-center space-x-4">
                                <!-- Doctor Profile Picture in Appointment -->
                                <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-100">
                                    <img src="{{ $appointment->schedule->doctor->profile_image_url }}"
                                         alt="Dr. {{ $appointment->schedule->doctor->user->name }}"
                                         class="h-full w-full object-cover">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">Dr.
                                        {{ $appointment->schedule->doctor->user->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ $appointment->schedule->doctor->specialty->name }} •
                                        {{ $appointment->schedule->formatted_date }} at {{ $appointment->formatted_time }}
                                    </p>
                                    @if ($appointment->reason)
                                        <p class="text-sm text-gray-500 mt-1">Reason:
                                            {{ Str::limit($appointment->reason, 60) }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="badge {{ $appointment->status_badge_class }} badge-sm">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No appointments recorded</p>
            @endif
        </div>

        <!-- Medical Records -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Medical Records</h2>

            @if ($patient->medicalRecords->count() > 0)
                <div class="space-y-4">
                    @foreach ($patient->medicalRecords->sortByDesc('visit_date')->take(5) as $record)
                        <div class="border border-gray-200 rounded-lg p-5">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <!-- Doctor Profile Picture in Medical Record -->
                                    <div class="h-10 w-10 rounded-full overflow-hidden flex-shrink-0 ring-2 ring-green-100">
                                        <img src="{{ $record->doctor->profile_image_url }}"
                                             alt="Dr. {{ $record->doctor->user->name }}"
                                             class="h-full w-full object-cover">
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">{{ $record->formatted_visit_date }}</p>
                                        <p class="font-semibold text-gray-900">Dr. {{ $record->doctor->user->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $record->doctor->specialty->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Diagnosis</p>
                                    <p class="text-gray-900">{{ $record->diagnosis }}</p>
                                </div>

                                @if ($record->treatment)
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Treatment</p>
                                        <p class="text-gray-900">{{ $record->treatment }}</p>
                                    </div>
                                @endif

                                @if ($record->prescription)
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Prescription</p>
                                        <p class="text-gray-900">{{ $record->prescription }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No medical records yet</p>
            @endif
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-red-200">
            <h2 class="text-xl font-semibold text-red-900 mb-4">Danger Zone</h2>
            <p class="text-sm text-gray-600 mb-4">Once you delete this patient, there is no going back. Please be
                certain.</p>

            <form method="POST" action="{{ route('admin.patients.destroy', $patient) }}"
                onsubmit="return confirm('Are you absolutely sure you want to delete this patient? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="button"
                    onclick="openDeleteModal('{{ route('admin.patients.destroy', $patient) }}', '{{ $patient->user->name }}')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition duration-150">
                    Delete Patient Account
                </button>
            </form>
        </div>
    </div>
    <x-delete-modal title="Delete Patient Account"
        message="Are you sure you want to permanently delete this patient account?" />

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openDeleteModal(url, name) {
            document.getElementById('modal-message').textContent =
                `Are you sure you want to permanently delete ${name}'s account? This will also delete all their appointments and medical records. This action cannot be undone.`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }
    </script>
</x-layouts.admin>
