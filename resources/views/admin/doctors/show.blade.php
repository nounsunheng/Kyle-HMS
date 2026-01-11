<x-layouts.admin>
    <div class="max-w-6xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Doctors
        </a>

        <!-- Doctor Profile -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center">
                    <div
                        class="h-20 w-20 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                        <svg class="h-10 w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-3xl font-bold text-gray-900">Dr. {{ $doctor->user->name }}</h1>
                        <p class="text-lg text-secondary-600 font-medium mt-1">{{ $doctor->specialty->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span
                        class="px-3 py-1 rounded-full text-sm font-medium {{ $doctor->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $doctor->is_available ? 'Available' : 'Unavailable' }}
                    </span>
                    <a href="{{ route('admin.doctors.edit', $doctor) }}"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                        Edit Doctor
                    </a>
                </div>
            </div>

            <!-- Doctor Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Contact Information</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="font-semibold text-gray-900">{{ $doctor->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Phone</p>
                            <p class="font-semibold text-gray-900">{{ $doctor->phone }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Professional Details</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">License Number</p>
                            <p class="font-semibold text-gray-900">{{ $doctor->license_number }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Years of Experience</p>
                            <p class="font-semibold text-gray-900">{{ $doctor->years_of_experience }} years</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Statistics</h3>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">Total Schedules</p>
                            <p class="font-semibold text-gray-900">{{ $doctor->schedules->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Total Appointments</p>
                            <p class="font-semibold text-gray-900">{{ $doctor->appointments->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($doctor->qualifications)
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Qualifications</h3>
                    <p class="text-gray-900">{{ $doctor->qualifications }}</p>
                </div>
            @endif

            @if ($doctor->bio)
                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-sm font-medium text-gray-500 mb-2">Bio</h3>
                    <p class="text-gray-900">{{ $doctor->bio }}</p>
                </div>
            @endif>
        </div>

        <!-- Recent Schedules -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Schedules</h2>

            @if ($doctor->schedules->count() > 0)
                <div class="space-y-3">
                    @foreach ($doctor->schedules->take(5) as $schedule)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $schedule->formatted_date }}</p>
                                <p class="text-sm text-gray-600">{{ $schedule->formatted_time_range }} •
                                    {{ $schedule->booked_appointments }}/{{ $schedule->max_appointments }} booked</p>
                            </div>
                            <span
                                class="badge {{ $schedule->status === 'active' ? 'badge-success' : 'badge-error' }} badge-sm">
                                {{ ucfirst($schedule->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No schedules created yet</p>
            @endif
        </div>

        <!-- Recent Appointments -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Appointments</h2>

            @if ($doctor->appointments->count() > 0)
                <div class="space-y-3">
                    @foreach ($doctor->appointments->take(5) as $appointment)
                        <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                            <div>
                                <p class="font-semibold text-gray-900">{{ $appointment->patient->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $appointment->schedule->formatted_date }} at
                                    {{ $appointment->formatted_time }}</p>
                            </div>
                            <span class="badge {{ $appointment->status_badge_class }} badge-sm">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center py-8 text-gray-500">No appointments yet</p>
            @endif
        </div>

        <!-- Danger Zone -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-red-200">
            <h2 class="text-xl font-semibold text-red-900 mb-4">Danger Zone</h2>
            <p class="text-sm text-gray-600 mb-4">Once you delete this doctor, there is no going back. Please be
                certain.</p>

            <form method="POST" action="{{ route('admin.doctors.destroy', $doctor) }}"
                onsubmit="return confirm('Are you absolutely sure you want to delete this doctor? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="button"
                    onclick="openDeleteModal('{{ route('admin.doctors.destroy', $doctor) }}', 'Dr. {{ $doctor->user->name }}')"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition duration-150">
                    Delete Doctor Account
                </button>
            </form>
        </div>
    </div>
    <x-delete-modal title="Delete Doctor Account"
        message="Are you sure you want to permanently delete this doctor account?" />

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openDeleteModal(url, name) {
            document.getElementById('modal-message').textContent =
                `Are you sure you want to permanently delete ${name}'s account? This action cannot be undone and will delete all their schedules and affect their appointments.`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }
    </script>
</x-layouts.admin>
