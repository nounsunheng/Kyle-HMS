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
                                        <form method="POST" action="{{ route('doctor.schedule.destroy', $schedule) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                                Delete
                                            </button>
                                        </form>
                                    @elseif($schedule->status === 'active')
                                        <form method="POST" action="{{ route('doctor.schedule.cancel', $schedule) }}"
                                              onsubmit="return confirm('Are you sure you want to cancel this schedule? All appointments will be cancelled.');">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-medium">
                                                Cancel Schedule
                                            </button>
                                        </form>
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
</x-layouts.doctor>
