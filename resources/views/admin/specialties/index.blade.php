<x-layouts.admin>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Specialties Management</h1>
                <p class="mt-2 text-gray-600">Manage medical specialties</p>
            </div>
            <a href="{{ route('admin.specialties.create') }}"
                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                Add Specialty
            </a>
        </div>

        <!-- Specialties Table -->
        @if ($specialties->count() > 0)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Specialty Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctors</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($specialties as $specialty)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $specialty->name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600">{{ $specialty->description ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $specialty->doctors_count }} doctors
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <a href="{{ route('admin.specialties.edit', $specialty) }}"
                                        class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    @if ($specialty->doctors_count == 0)
                                        <form method="POST"
                                            action="{{ route('admin.specialties.destroy', $specialty) }}" class="inline"
                                            onsubmit="return confirm('Are you sure?');">
                                            @csrf
                                            @method('DELETE')
                                            @if ($specialty->doctors_count == 0)
                                                <button type="button"
                                                    onclick="openDeleteModal('{{ route('admin.specialties.destroy', $specialty) }}', '{{ $specialty->name }}')"
                                                    class="text-red-600 hover:text-red-900">
                                                    Delete
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">{{ $specialties->links() }}</div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <h3 class="text-lg font-medium text-gray-900">No specialties found</h3>
                <a href="{{ route('admin.specialties.create') }}"
                    class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md">
                    Add Specialty
                </a>
            </div>
        @endif
    </div>
    <x-delete-modal title="Delete Specialty" message="Are you sure you want to delete this specialty?" />

    <form id="delete-form" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function openDeleteModal(url, name) {
            document.getElementById('modal-message').textContent =
                `Are you sure you want to delete the ${name} specialty?`;
            document.getElementById('delete-form').action = url;
            window.dispatchEvent(new CustomEvent('open-delete-modal'));
        }
    </script>
</x-layouts.admin>
