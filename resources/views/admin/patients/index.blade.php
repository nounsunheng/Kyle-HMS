<x-layouts.admin>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Patients Management</h1>
            <p class="mt-2 text-gray-600">View and manage all registered patients</p>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="GET" action="{{ route('admin.patients.index') }}" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Patient</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Search by name, email or phone..."
                        class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-md transition duration-150">
                    Search
                </button>
                @if (request('search'))
                    <a href="{{ route('admin.patients.index') }}"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Results Count -->
        <div class="text-sm text-gray-600">
            Showing {{ $patients->count() }} of {{ $patients->total() }} patients
        </div>

        <!-- Patients Table -->
        @if ($patients->count() > 0)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Age/Gender</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Blood Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Registered</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($patients as $patient)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <!-- Patient Profile Picture -->
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full overflow-hidden ring-2 ring-blue-100">
                                            <img src="{{ $patient->profile_image_url }}"
                                                 alt="{{ $patient->user->name }}"
                                                 class="h-full w-full object-cover">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $patient->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $patient->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $patient->phone }}</div>
                                    @if ($patient->allergies)
                                        <div class="text-xs text-red-600 mt-1">⚠️ Allergies</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $patient->age }} yrs / {{ ucfirst($patient->gender) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $patient->blood_type ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $patient->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.patients.show', $patient) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                    <a href="{{ route('admin.patients.edit', $patient) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <button type="button"
                                        onclick="openDeleteModal('{{ route('admin.patients.destroy', $patient) }}', '{{ $patient->user->name }}')"
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
                {{ $patients->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No patients found</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if (request('search'))
                        No patients match your search criteria. Try different keywords.
                    @else
                        No patients have registered yet. Patients will appear here after registration.
                    @endif
                </p>
                @if (request('search'))
                    <a href="{{ route('admin.patients.index') }}"
                        class="mt-4 inline-block text-blue-600 hover:text-blue-700">
                        Clear search and view all patients
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <x-delete-modal
        title="Delete Patient"
        message="Are you sure you want to delete this patient? This will also delete all their appointments and medical records."
    />

    <!-- Hidden delete form -->
    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openDeleteModal(url, name) {
            document.getElementById('modal-message').textContent =
                `Are you sure you want to delete ${name}? This will also delete all their appointments and medical records.`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }
    </script>
</x-layouts.admin>
