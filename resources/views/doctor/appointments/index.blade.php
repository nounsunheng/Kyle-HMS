<x-layouts.doctor>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Appointments</h1>
            <p class="mt-2 text-gray-600">Manage and view all your appointments</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="GET" action="{{ route('doctor.appointments.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status"
                            id="status"
                            class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date"
                           name="date"
                           id="date"
                           value="{{ request('date') }}"
                           class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-secondary-600 hover:bg-secondary-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <div class="text-sm text-gray-600">
            Showing {{ $appointments->count() }} of {{ $appointments->total() }} appointments
        </div>

        <!-- Appointments List -->
        @if($appointments->count() > 0)
            <div class="space-y-4">
                @foreach($appointments as $appointment)
                    <div class="bg-white rounded-lg shadow-sm p-5 hover:shadow-md transition duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <!-- Appointment Info -->
                                <div class="flex items-center mb-3">
                                    <span class="text-xs font-mono bg-gray-100 text-gray-700 px-2 py-1 rounded">
                                        {{ $appointment->appointment_number }}
                                    </span>
                                    <span class="ml-2 badge {{ $appointment->status_badge_class }} badge-sm">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>

                                <!-- Patient Info -->
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $appointment->patient->user->name }}
                                </h3>

                                <!-- Details Grid -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $appointment->schedule->formatted_date }}
                                    </div>

                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $appointment->formatted_time }}
                                    </div>

                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $appointment->patient->phone }}
                                    </div>
                                </div>

                                @if($appointment->reason)
                                    <div class="mt-3 text-sm text-gray-600">
                                        <span class="font-medium">Reason:</span> {{ Str::limit($appointment->reason, 100) }}
                                    </div>
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="ml-4 flex flex-col space-y-2">
                                <a href="{{ route('doctor.appointments.show', $appointment) }}"
                                   class="text-secondary-600 hover:text-secondary-700 text-sm font-medium">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No appointments found</h3>
                <p class="mt-2 text-sm text-gray-500">Try adjusting your filters or create a schedule to start accepting appointments</p>
                @if(!request()->hasAny(['status', 'date']))
                    <a href="{{ route('doctor.schedule.create') }}" class="mt-4 inline-block text-secondary-600 hover:text-secondary-700">
                        Create Schedule
                    </a>
                @else
                    <a href="{{ route('doctor.appointments.index') }}" class="mt-4 inline-block text-secondary-600 hover:text-secondary-700">
                        Clear Filters
                    </a>
                @endif
            </div>
        @endif
    </div>
</x-layouts.doctor>
