<x-layouts.doctor>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">My Schedule</h1>
                <p class="mt-2 text-gray-600">Manage your consultation schedules</p>
            </div>
            <a href="{{ route('doctor.schedule.create') }}"
               class="px-6 py-3 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                Create Schedule
            </a>
        </div>

        <!-- Upcoming Schedules -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Schedules</h2>

            @if($upcomingSchedules->count() > 0)
                <div class="space-y-4">
                    @foreach($upcomingSchedules as $schedule)
                        <div class="border border-gray-200 rounded-lg p-5 hover:border-secondary-500 transition duration-150">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <!-- Date -->
                                    <div class="flex items-center mb-3">
                                        <svg class="h-5 w-5 mr-2 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-lg font-semibold text-gray-900">{{ $schedule->formatted_date }}</span>
                                        <span class="ml-3 badge {{ $schedule->status === 'active' ? 'badge-success' : 'badge-error' }} badge-sm">
                                            {{ ucfirst($schedule->status) }}
                                        </span>
                                    </div>

                                    <!-- Details -->
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $schedule->formatted_time_range }}
                                        </div>

                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $schedule->duration_per_appointment }} min/appointment
                                        </div>

                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            {{ $schedule->booked_appointments }}/{{ $schedule->max_appointments }} booked
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-3">
                                        <div class="bg-secondary-600 h-2 rounded-full"
                                             style="width: {{ ($schedule->booked_appointments / $schedule->max_appointments) * 100 }}%">
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="ml-4 flex flex-col space-y-2">
                                    <a href="{{ route('doctor.schedule.show', $schedule) }}"
                                       class="text-secondary-600 hover:text-secondary-700 text-sm font-medium">
                                        View Details
                                    </a>
                                    @if($schedule->booked_appointments == 0)
                                        <a href="{{ route('doctor.schedule.edit', $schedule) }}"
                                           class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                                            Edit
                                        </a>
                                        <button type="button"
                                                onclick="openDeleteModal('{{ route('doctor.schedule.destroy', $schedule) }}', '{{ $schedule->formatted_date }} ({{ $schedule->formatted_time_range }})')"
                                                class="text-red-600 hover:text-red-700 text-sm font-medium text-left">
                                            Delete
                                        </button>
                                    @elseif($schedule->status === 'active')
                                        <button type="button"
                                                onclick="openCancelModal('{{ route('doctor.schedule.cancel', $schedule) }}', '{{ $schedule->formatted_date }}')"
                                                class="text-red-600 hover:text-red-700 text-sm font-medium text-left">
                                            Cancel Schedule
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No upcoming schedules</h3>
                    <p class="mt-2 text-sm text-gray-500">Create a schedule to start accepting appointments</p>
                    <a href="{{ route('doctor.schedule.create') }}"
                       class="mt-4 inline-block px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                        Create Schedule
                    </a>
                </div>
            @endif
        </div>

        <!-- Past Schedules -->
        @if($pastSchedules->count() > 0)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Past Schedules</h2>
                <div class="space-y-3">
                    @foreach($pastSchedules as $schedule)
                        <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $schedule->formatted_date }}</p>
                                    <p class="text-sm text-gray-600">{{ $schedule->formatted_time_range }} • {{ $schedule->booked_appointments }} appointments</p>
                                </div>
                                <a href="{{ route('doctor.schedule.show', $schedule) }}"
                                   class="text-secondary-600 hover:text-secondary-700 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Delete Schedule Modal -->
    <x-delete-modal
        title="Delete Schedule"
        message="Are you sure you want to delete this schedule?"
    />

    <!-- Cancel Schedule Modal -->
    <div x-data="cancelModal()"
         x-show="isOpen"
         x-cloak
         @keydown.escape.window="close()"
         class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <div x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
             @click="close()">
        </div>

        <div class="flex min-h-screen items-center justify-center p-4">
            <div x-show="isOpen"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                 @click.stop>

                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>

                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-lg font-semibold leading-6 text-gray-900">
                                Cancel Schedule
                            </h3>
                            <div class="mt-2">
                                <p id="cancel-modal-message" class="text-sm text-gray-600"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                    <button type="button"
                            @click="confirmCancel()"
                            class="inline-flex w-full justify-center rounded-md bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-700 sm:w-auto transition duration-150">
                        Cancel Schedule
                    </button>
                    <button type="button"
                            @click="close()"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-4 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition duration-150">
                        Keep Schedule
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Forms -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <form id="cancel-form" method="POST" style="display: none;">
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
            document.getElementById('cancel-modal-message').textContent =
                `Are you sure you want to cancel the schedule for ${scheduleName}? All booked appointments will be cancelled and patients will be notified.`;
            document.getElementById('cancel-form').action = url;
            window.dispatchEvent(new CustomEvent('open-cancel-modal'));
        }

        function cancelModal() {
            return {
                isOpen: false,

                init() {
                    window.addEventListener('open-cancel-modal', () => {
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
                    const form = document.getElementById('cancel-form');
                    if (form) {
                        form.submit();
                    }
                    this.close();
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
