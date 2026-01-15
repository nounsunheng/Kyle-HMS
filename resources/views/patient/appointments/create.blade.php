<x-layouts.patient>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('patient.doctors.show', $schedule->doctor) }}" class="inline-flex items-center text-primary-600 hover:text-primary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Doctor Profile
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Book Appointment</h1>
            <p class="mt-2 text-gray-600">Fill in the details below to book your appointment</p>
        </div>

        <!-- Doctor & Schedule Info -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Appointment Details</h2>

            <div class="space-y-3">
                <div class="flex items-start">
                    <svg class="h-5 w-5 mr-3 text-primary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-500">Doctor</p>
                        <p class="font-semibold text-gray-900">Dr. {{ $schedule->doctor->user->name }}</p>
                        <p class="text-sm text-secondary-600">{{ $schedule->doctor->specialty->name }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <svg class="h-5 w-5 mr-3 text-primary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-500">Date</p>
                        <p class="font-semibold text-gray-900">{{ $schedule->formatted_date }}</p>
                    </div>
                </div>

                <div class="flex items-start">
                    <svg class="h-5 w-5 mr-3 text-primary-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-500">Session Time</p>
                        <p class="font-semibold text-gray-900">{{ $schedule->formatted_time_range }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Form -->
        <form method="POST" action="{{ route('patient.appointments.store') }}" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

            <div class="space-y-6">
                <!-- Time Slot Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Select Time Slot *</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($timeSlots as $slot)
                            <label class="relative flex items-center justify-center p-3 border rounded-lg cursor-pointer transition duration-150
                                {{ $slot['available'] ? 'border-gray-300 hover:border-primary-500 hover:bg-primary-50  text-gray-700' : 'border-gray-200 bg-gray-100 cursor-not-allowed opacity-50' }}">
                                <input type="radio"
                                       name="appointment_time"
                                       value="{{ $slot['time'] }}"
                                       {{ !$slot['available'] ? 'disabled' : '' }}
                                       {{ old('appointment_time') == $slot['time'] ? 'checked' : '' }}
                                       class="sr-only peer"
                                       required>
                                <span class="text-sm font-medium text-gray-700 peer-checked:text-primary-600">
                                    {{ $slot['formatted'] }}
                                </span>
                                <span class="absolute inset-0 rounded-lg border-2 border-transparent peer-checked:border-primary-600 pointer-events-none"></span>
                            </label>
                        @endforeach
                    </div>
                    @error('appointment_time')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <span class="inline-block w-3 h-3 bg-gray-100 border border-gray-200 rounded mr-1"></span>
                        Unavailable slots are shown in gray
                    </p>
                </div>

                <!-- Reason for Visit -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">
                        Reason for Visit *
                    </label>
                    <textarea id="reason"
                              name="reason"
                              rows="4"
                              required
                              placeholder="Please describe your symptoms or reason for consultation..."
                              class="w-full rounded-md border-gray-300 shadow-sm  text-gray-700 focus:border-primary-500 focus:ring-primary-500">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Important Notes -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">Important Notes:</h3>
                    <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                        <li>Please arrive 10 minutes before your appointment time</li>
                        <li>Bring your ID and any relevant medical documents</li>
                        <li>You can cancel your appointment up to 24 hours in advance</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('patient.doctors.show', $schedule->doctor) }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-md transition duration-150">
                        Confirm Booking
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.patient>
