<x-layouts.admin>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Doctors Management</h1>
                <p class="mt-2 text-gray-600">Manage all doctors in the system</p>
            </div>
            <a href="{{ route('admin.doctors.create') }}"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                Add New Doctor
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="GET" action="{{ route('admin.doctors.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Name or email..."
                        class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Specialty Filter -->
                <div>
                    <label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
                    <select name="specialty" id="specialty"
                        class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Specialties</option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}"
                                {{ request('specialty') == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Availability Filter -->
                <div>
                    <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                    <select name="availability" id="availability"
                        class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All</option>
                        <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>
                            Available</option>
                        <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>
                            Unavailable</option>
                    </select>
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

        <!-- Results -->
        <div class="text-sm text-gray-600">
            Showing {{ $doctors->count() }} of {{ $doctors->total() }} doctors
        </div>

        <!-- Doctors Table -->
        @if ($doctors->count() > 0)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Specialty</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Experience</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($doctors as $doctor)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <!-- Doctor Profile Picture -->
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full overflow-hidden ring-2 ring-green-100">
                                            <img src="{{ $doctor->profile_image_url }}"
                                                 alt="Dr. {{ $doctor->user->name }}"
                                                 class="h-full w-full object-cover">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">Dr.
                                                {{ $doctor->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $doctor->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $doctor->specialty->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $doctor->phone }}</div>
                                    <div class="text-sm text-gray-500">{{ $doctor->license_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $doctor->years_of_experience }} years
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $doctor->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $doctor->is_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.doctors.show', $doctor) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <button type="button"
                                        onclick="openDeleteModal('{{ route('admin.doctors.destroy', $doctor) }}', 'Dr. {{ $doctor->user->name }}')"
                                        class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $doctors->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No doctors found</h3>
                <p class="mt-2 text-sm text-gray-500">Get started by adding a new doctor</p>
                <a href="{{ route('admin.doctors.create') }}"
                    class="mt-4 inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                    Add Doctor
                </a>
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <x-delete-modal
        title="Delete Doctor"
        message="Are you sure you want to delete this doctor? This will also delete all their schedules and may affect appointments."
    />

    <!-- Hidden delete form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openDeleteModal(url, name) {
            document.getElementById('modal-message').textContent =
                `Are you sure you want to delete ${name}? This will also delete all their schedules and may affect appointments.`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }
    </script>
</x-layouts.admin>
