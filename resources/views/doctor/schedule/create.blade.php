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
        <form method="POST" action="{{ route('doctor.schedule.store') }}" class="bg-white  text-gray-700 rounded-lg shadow-sm p-6">
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
                           value="{{ old('schedule_date', now()->addDay()->format('Y-m-d')) }}"
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
                               value="{{ old('start_time', '09:00') }}"
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
                               value="{{ old('end_time', '17:00') }}"
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

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Tips:</h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Choose a duration that allows adequate time for patient consultation</li>
                        <li>Consider including buffer time between appointments</li>
                        <li>You cannot edit a schedule once appointments are booked</li>
                        <li>Maximum appointments will be calculated automatically</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('doctor.schedule.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                        Create Schedule
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.doctor>
