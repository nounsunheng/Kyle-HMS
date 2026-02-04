<x-layouts.admin>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Appointments Management</h1>
            <p class="mt-2 text-gray-600">View and manage all appointments in the system</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="GET" action="{{ route('admin.appointments.index') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Patient or doctor name..."
                        class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status"
                        class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No Show</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" id="date" value="{{ request('date') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <div class="text-sm text-gray-600">
            Showing {{ $appointments->count() }} of {{ $appointments->total() }} appointments
        </div>

        <!-- Appointments Table -->
        @if ($appointments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Reason</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($appointments as $appointment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="h-10 w-10 flex-shrink-0 bg-green-100 rounded-full flex items-center justify-center">
                                            <span
                                                class="text-green-600 font-semibold">{{ substr($appointment->patient->user->name, 0, 2) }}</span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $appointment->patient->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $appointment->patient->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Dr.
                                        {{ $appointment->schedule->doctor->user->name }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $appointment->schedule->doctor->specialty->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $appointment->schedule->formatted_date }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $appointment->formatted_time }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $appointment->status_badge_class }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        {{ Str::limit($appointment->reason ?? 'N/A', 30) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.appointments.show', $appointment) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">View</a>

                                    @if (in_array($appointment->status, ['pending', 'confirmed']))
                                        <button type="button"
                                            onclick="openCancelModal('{{ route('admin.appointments.cancel', $appointment) }}', '{{ $appointment->appointment_number }}')"
                                            class="text-red-600 hover:text-red-900">
                                            Cancel
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No appointments found</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if (request('search') || request('status') || request('date'))
                        No appointments match your search criteria. Try adjusting your filters.
                    @else
                        No appointments have been scheduled yet.
                    @endif
                </p>
                @if (request()->hasAny(['search', 'status', 'date']))
                    <a href="{{ route('admin.appointments.index') }}"
                        class="mt-4 inline-block text-blue-600 hover:text-blue-700">
                        Clear filters and view all appointments
                    </a>
                @endif
            </div>
        @endif
    </div>
    <x-delete-modal title="Cancel Appointment" message="Are you sure you want to cancel this appointment?" />

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        function openCancelModal(url, number) {
            document.getElementById('modal-title').textContent = 'Cancel Appointment';
            document.getElementById('modal-message').textContent =
                `Are you sure you want to cancel appointment ${number}?`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }
    </script>
</x-layouts.admin>
