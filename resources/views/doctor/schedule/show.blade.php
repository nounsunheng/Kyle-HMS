<x-layouts.doctor>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.schedule.index') }}"
            class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Schedules
        </a>

        <!-- Schedule Details -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Schedule Details</h1>
                    <p class="mt-1 text-lg text-gray-600">{{ $schedule->formatted_date }}</p>
                </div>
                <span class="badge {{ $schedule->status === 'active' ? 'badge-success' : 'badge-error' }}">
                    {{ ucfirst($schedule->status) }}
                </span>
            </div>

            <!-- Schedule Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Schedule Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Time Range</p>
                                <p class="font-semibold text-gray-900">{{ $schedule->formatted_time_range }}</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Duration per Appointment</p>
                                <p class="font-semibold text-gray-900">{{ $schedule->duration_per_appointment }} minutes
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <svg class="h-5 w-5 mr-3 text-secondary-600 mt-0.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <div>
                                <p class="text-sm text-gray-500">Maximum Appointments</p>
                                <p class="font-semibold text-gray-900">{{ $schedule->max_appointments }} slots</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-sm font-medium text-gray-500 mb-3">Booking Status</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Appointments Booked</span>
                                <span
                                    class="text-sm font-semibold text-gray-900">{{ $schedule->booked_appointments }}/{{ $schedule->max_appointments }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-secondary-600 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ $schedule->max_appointments > 0 ? ($schedule->booked_appointments / $schedule->max_appointments) * 100 : 0 }}%">
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold text-secondary-600">{{ $schedule->available_slots }}</span>
                                slots still available
                            </p>
                        </div>

                        @if ($schedule->is_full)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm font-medium text-yellow-800">This schedule is fully booked</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if ($schedule->status === 'active' && $schedule->schedule_date->isFuture())
                <div class="border-t border-gray-200 pt-6 flex items-center space-x-4">
                    @if ($schedule->booked_appointments == 0)
                        <a href="{{ route('doctor.schedule.edit', $schedule) }}"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                            Edit Schedule
                        </a>
                        <button type="button"
                                onclick="openDeleteModal('{{ route('doctor.schedule.destroy', $schedule) }}', '{{ $schedule->formatted_date }}')"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition duration-150">
                            Delete Schedule
                        </button>
                    @else
                        <button type="button"
                                onclick="openCancelModal('{{ route('doctor.schedule.cancel', $schedule) }}', '{{ $schedule->formatted_date }}')"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition duration-150">
                            Cancel Schedule
                        </button>
                    @endif
                </div>
            @endif
        </div>

        <!-- Appointments List -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Appointments</h2>

            @if ($schedule->appointments->count() > 0)
                <div class="space-y-3">
                    @foreach ($schedule->appointments->sortBy('appointment_time') as $appointment)
                        <div
                            class="border border-gray-200 rounded-lg p-4 hover:border-secondary-500 transition duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                            {{ $appointment->appointment_number }}
                                        </span>
                                        <span class="ml-2 badge {{ $appointment->status_badge_class }} badge-sm">
                                            {{ ucfirst($appointment->status) }}
                                        </span>
                                    </div>

                                    <h3 class="text-lg font-semibold text-gray-900">
                                        {{ $appointment->patient->user->name }}
                                    </h3>

                                    <div class="mt-2 space-y-1">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $appointment->formatted_time }}
                                        </div>

                                        @if ($appointment->reason)
                                            <div class="flex items-start text-sm text-gray-600">
                                                <svg class="h-4 w-4 mr-2 text-gray-400 mt-0.5" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>{{ Str::limit($appointment->reason, 80) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="ml-4 flex flex-col space-y-2">
                                    <a href="{{ route('doctor.appointments.show', $appointment) }}"
                                        class="text-secondary-600 hover:text-secondary-700 text-sm font-medium">
                                        View Details
                                    </a>

                                    @if (in_array($appointment->status, ['pending', 'confirmed']))
                                        <button type="button"
                                            onclick="openCancelAppointmentModal('{{ route('doctor.appointments.cancel', $appointment) }}', '{{ $appointment->appointment_number }}', '{{ $appointment->patient->user->name }}')"
                                            class="text-red-600 hover:text-red-700 text-sm font-medium text-left">
                                            Cancel
                                        </button>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2">No appointments booked yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Schedule Modal -->
    <x-delete-modal
        title="Delete Schedule"
        message="Are you sure you want to delete this schedule?"
    />

    <!-- Cancel Schedule Modal -->
    <div x-data="cancelScheduleModal()"
         x-show="isOpen"
         x-cloak
         @keydown.escape.window="close()"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">

        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-75"
             @click="close()">
        </div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                 class="relative bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-full"
                 @click.stop>

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>

                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-lg font-semibold text-gray-900">Cancel Schedule</h3>
                            <div class="mt-2">
                                <p id="cancel-schedule-message" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                    <button type="button" @click="confirmCancel()"
                            class="inline-flex w-full justify-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-700 sm:w-auto">
                        Cancel Schedule
                    </button>
                    <button type="button" @click="close()"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                        Keep Schedule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Appointment Modal -->
    <div x-data="cancelAppointmentModal()"
         x-show="isOpen"
         x-cloak
         @keydown.escape.window="close()"
         class="fixed inset-0 z-50 overflow-y-auto"
         role="dialog"
         aria-modal="true">

        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-75"
             @click="close()">
        </div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                 class="relative bg-white rounded-lg shadow-xl sm:max-w-lg sm:w-full"
                 @click.stop>

                <form id="cancel-appointment-form" method="POST">
                    @csrf

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                            </div>

                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                                <h3 class="text-lg font-semibold text-gray-900">Cancel Appointment</h3>
                                <div class="mt-2">
                                    <p id="cancel-appointment-message" class="text-sm text-gray-600 mb-4"></p>

                                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">
                                        Reason for Cancellation *
                                    </label>
                                    <textarea id="cancellation_reason"
                                              name="cancellation_reason"
                                              required
                                              rows="4"
                                              placeholder="Please provide a reason for cancelling this appointment..."
                                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-red-500 focus:ring-red-500"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                        <button type="submit"
                                class="inline-flex w-full justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 sm:w-auto">
                            Cancel Appointment
                        </button>
                        <button type="button" @click="close()"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Close
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="cancel-schedule-form" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function openDeleteModal(url, scheduleName) {
            document.getElementById('modal-message').textContent =
                `Are you sure you want to delete the schedule for ${scheduleName}? This action cannot be undone.`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }

        function openCancelModal(url, scheduleName) {
            document.getElementById('cancel-schedule-message').textContent =
                `Are you sure you want to cancel the schedule for ${scheduleName}? All booked appointments will be cancelled.`;
            document.getElementById('cancel-schedule-form').action = url;
            window.dispatchEvent(new CustomEvent('open-cancel-schedule-modal'));
        }

        function openCancelAppointmentModal(url, appointmentNumber, patientName) {
            document.getElementById('cancel-appointment-message').textContent =
                `Cancel appointment ${appointmentNumber} for ${patientName}?`;
            document.getElementById('cancel-appointment-form').action = url;
            document.getElementById('cancellation_reason').value = '';
            window.dispatchEvent(new CustomEvent('open-cancel-appointment-modal'));
        }

        function cancelScheduleModal() {
            return {
                isOpen: false,

                init() {
                    window.addEventListener('open-cancel-schedule-modal', () => {
                        this.open();
                    });
                },

                open() {
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                close() {
                    this.isOpen = false;
                    document.body.style.overflow = 'auto';
                },

                confirmCancel() {
                    const form = document.getElementById('cancel-schedule-form');
                    if (form) {
                        form.submit();
                    }
                    this.close();
                }
            }
        }

        function cancelAppointmentModal() {
            return {
                isOpen: false,

                init() {
                    window.addEventListener('open-cancel-appointment-modal', () => {
                        this.open();
                    });
                },

                open() {
                    this.isOpen = true;
                    document.body.style.overflow = 'hidden';
                },

                close() {
                    this.isOpen = false;
                    document.body.style.overflow = 'auto';
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</x-layouts.doctor>
