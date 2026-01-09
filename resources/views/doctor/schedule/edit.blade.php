<x-layouts.doctor>
    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('doctor.schedule.show', $schedule) }}" class="inline-flex items-center text-secondary-600 hover:text-secondary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Schedule Details
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Schedule</h1>
            <p class="mt-2 text-gray-600">Update your schedule details</p>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('doctor.schedule.update', $schedule) }}" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Date -->
                <div>
                    <label for="schedule_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Schedule Date *
                    </label>
                    <input type="date"
                           id="schedule_date"
                           name="schedule_date"
                           value="{{ old('schedule_date', $schedule->schedule_date->format('Y-m-d')) }}"
                           min="{{ now()->format('Y-m-d') }}"
                           required
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                    @error('schedule_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
                               value="{{ old('start_time', $schedule->start_time) }}"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                        @error('start_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">
                            End Time *
                        </label>
                        <input type="time"
                               id="end_time"
                               name="end_time"
                               value="{{ old('end_time', $schedule->end_time) }}"
                               required
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                        @error('end_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-secondary-500 focus:ring-secondary-500">
                        <option value="15" {{ old('duration_per_appointment', $schedule->duration_per_appointment) == '15' ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ old('duration_per_appointment', $schedule->duration_per_appointment) == '30' ? 'selected' : '' }}>30 minutes</option>
                        <option value="45" {{ old('duration_per_appointment', $schedule->duration_per_appointment) == '45' ? 'selected' : '' }}>45 minutes</option>
                        <option value="60" {{ old('duration_per_appointment', $schedule->duration_per_appointment) == '60' ? 'selected' : '' }}>60 minutes</option>
                        <option value="90" {{ old('duration_per_appointment', $schedule->duration_per_appointment) == '90' ? 'selected' : '' }}>90 minutes</option>
                        <option value="120" {{ old('duration_per_appointment', $schedule->duration_per_appointment) == '120' ? 'selected' : '' }}>120 minutes</option>
                    </select>
                    @error('duration_per_appointment')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Warning Box -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <h3 class="text-sm font-semibold text-yellow-900">Important</h3>
                            <p class="text-sm text-yellow-800 mt-1">You can only edit schedules with no booked appointments. Maximum appointments will be recalculated based on your changes.</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('doctor.schedule.show', $schedule) }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                        Update Schedule
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.doctor>
