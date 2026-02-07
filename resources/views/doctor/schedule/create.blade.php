<x-layouts.doctor>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.schedule.index') }}" class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Schedules
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Create New Schedule</h1>
            <p class="mt-2 text-gray-600">Set up your consultation schedule</p>
        </div>

        <!-- Create Form -->
        <form method="POST" action="{{ route('doctor.schedule.store') }}" id="scheduleForm" class="bg-white text-gray-700 rounded-lg shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <!-- Date -->
                <div>
                    <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Schedule Date *
                    </label>
                    <input type="date"
                           id="schedule_date"
                           name="schedule_date"
                           value="{{ old('schedule_date', now()->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           required
                           onchange="validateDateTime()"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                    @error('schedule_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">You can only create schedules for today or future dates</p>
                </div>

                <!-- Time Range -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">
                            Start Time *
                        </label>
                        <input type="time"
                               id="start_time"
                               name="start_time"
                               value="{{ old('start_time', '09:00') }}"
                               required
                               onchange="validateDateTime()"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                        @error('start_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p id="start_time_error" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">
                            End Time *
                        </label>
                        <input type="time"
                               id="end_time"
                               name="end_time"
                               value="{{ old('end_time', '17:00') }}"
                               required
                               onchange="validateDateTime()"
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                        @error('end_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p id="end_time_error" class="mt-1 text-xs text-red-600 hidden"></p>
                    </div>
                </div>

                <!-- Real-time validation message -->
                <div id="time_warning" class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 hidden">
                    <div class="flex items-start">
                        <svg class="h-5 w-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p id="time_warning_text" class="text-sm text-yellow-800"></p>
                    </div>
                </div>

                <!-- Duration per Appointment -->
                <div>
                    <label for="duration_per_appointment" class="block text-sm font-medium text-gray-700 mb-1">
                        Duration per Appointment (minutes) *
                    </label>
                    <select id="duration_per_appointment"
                            name="duration_per_appointment"
                            required
                            onchange="calculateMaxAppointments()"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                        <option value="15" {{ old('duration_per_appointment') == '15' ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ old('duration_per_appointment', '30') == '30' ? 'selected' : '' }}>30 minutes</option>
                        <option value="45" {{ old('duration_per_appointment') == '45' ? 'selected' : '' }}>45 minutes</option>
                        <option value="60" {{ old('duration_per_appointment') == '60' ? 'selected' : '' }}>60 minutes</option>
                        <option value="90" {{ old('duration_per_appointment') == '90' ? 'selected' : '' }}>90 minutes</option>
                        <option value="120" {{ old('duration_per_appointment') == '120' ? 'selected' : '' }}>120 minutes</option>
                    </select>
                    @error('duration_per_appointment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">This determines how many appointments can be booked</p>
                </div>

                <!-- Max Appointments Preview -->
                <div id="max_appointments_preview" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hidden">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm text-blue-800">
                            <span class="font-semibold">Maximum appointments: </span>
                            <span id="max_appointments_value" class="font-bold text-blue-900">0</span>
                        </p>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Important Guidelines:</h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Schedules for today must start at least 30 minutes from now</li>
                        <li>You cannot create schedules for past dates or times</li>
                        <li>Choose a duration that allows adequate time for patient consultation</li>
                        <li>Consider including buffer time between appointments</li>
                        <li>You cannot edit a schedule once appointments are booked</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('doctor.schedule.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit" id="submit_button"
                            class="px-6 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                        Create Schedule
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Real-time validation
        function validateDateTime() {
            const dateInput = document.getElementById('schedule_date');
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const warning = document.getElementById('time_warning');
            const warningText = document.getElementById('time_warning_text');
            const startError = document.getElementById('start_time_error');
            const endError = document.getElementById('end_time_error');
            const submitButton = document.getElementById('submit_button');

            if (!dateInput.value || !startTimeInput.value || !endTimeInput.value) {
                return;
            }

            const selectedDate = new Date(dateInput.value);
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const selectedDateOnly = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate());

            // Check if selected date is today
            const isToday = selectedDateOnly.getTime() === today.getTime();

            startError.classList.add('hidden');
            endError.classList.add('hidden');
            warning.classList.add('hidden');

            if (isToday) {
                // Parse times
                const [startHour, startMin] = startTimeInput.value.split(':').map(Number);
                const [endHour, endMin] = endTimeInput.value.split(':').map(Number);

                const startDateTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), startHour, startMin);
                const endDateTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), endHour, endMin);

                const thirtyMinutesFromNow = new Date(now.getTime() + 30 * 60000);

                // Check if start time is in the past
                if (startDateTime <= now) {
                    startError.textContent = 'Start time must be in the future';
                    startError.classList.remove('hidden');
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    return;
                }

                // Check if start time is at least 30 minutes from now
                if (startDateTime < thirtyMinutesFromNow) {
                    warning.classList.remove('hidden');
                    warningText.textContent = 'For today\'s schedule, start time should be at least 30 minutes from now for optimal preparation.';
                }

                // Check if end time is in the past
                if (endDateTime <= now) {
                    endError.textContent = 'End time must be in the future';
                    endError.classList.remove('hidden');
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                    return;
                }
            }

            // Enable submit button if validation passes
            submitButton.disabled = false;
            submitButton.classList.remove('opacity-50', 'cursor-not-allowed');

            // Calculate max appointments
            calculateMaxAppointments();
        }

        function calculateMaxAppointments() {
            const startTimeInput = document.getElementById('start_time');
            const endTimeInput = document.getElementById('end_time');
            const durationInput = document.getElementById('duration_per_appointment');
            const preview = document.getElementById('max_appointments_preview');
            const valueSpan = document.getElementById('max_appointments_value');

            if (!startTimeInput.value || !endTimeInput.value || !durationInput.value) {
                preview.classList.add('hidden');
                return;
            }

            const [startHour, startMin] = startTimeInput.value.split(':').map(Number);
            const [endHour, endMin] = endTimeInput.value.split(':').map(Number);

            const startMinutes = startHour * 60 + startMin;
            const endMinutes = endHour * 60 + endMin;
            const totalMinutes = endMinutes - startMinutes;

            if (totalMinutes <= 0) {
                preview.classList.add('hidden');
                return;
            }

            const duration = parseInt(durationInput.value);
            const maxAppointments = Math.floor(totalMinutes / duration);

            valueSpan.textContent = maxAppointments;
            preview.classList.remove('hidden');
        }

        // Run validation on page load
        document.addEventListener('DOMContentLoaded', function() {
            validateDateTime();

            // Add event listeners
            document.getElementById('schedule_date').addEventListener('change', validateDateTime);
            document.getElementById('start_time').addEventListener('change', validateDateTime);
            document.getElementById('end_time').addEventListener('change', validateDateTime);
            document.getElementById('duration_per_appointment').addEventListener('change', calculateMaxAppointments);

            // Prevent form submission if validation fails
            document.getElementById('scheduleForm').addEventListener('submit', function(e) {
                const startError = document.getElementById('start_time_error');
                const endError = document.getElementById('end_time_error');

                if (!startError.classList.contains('hidden') || !endError.classList.contains('hidden')) {
                    e.preventDefault();
                    alert('Please fix the time validation errors before submitting.');
                    return false;
                }
            });
        });
    </script>
</x-layouts.doctor>
