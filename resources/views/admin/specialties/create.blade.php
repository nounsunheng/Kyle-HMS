<x-layouts.admin>
    <div class="max-w-3xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.specialties.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Specialties
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Add New Specialty</h1>
            <p class="mt-2 text-gray-600">Create a new medical specialty</p>
        </div>

        <!-- Create Form -->
        <form method="POST" action="{{ route('admin.specialties.store') }}" class="bg-white rounded-lg shadow-sm p-6">
            @csrf

            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Specialty Name *
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           placeholder="e.g., Cardiology, Pediatrics"
                           class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Brief description of this medical specialty..."
                              class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="border-t border-gray-200 pt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.specialties.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                        Create Specialty
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-layouts.admin>
