<x-layouts.patient>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Find Doctors</h1>
            <p class="mt-2 text-gray-600">Browse and search for available doctors</p>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="GET" action="{{ route('patient.doctors.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Doctor</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Enter doctor name..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-700 bg-white">
                </div>

                <!-- Specialty Filter -->
                <div>
                    <label for="specialty" class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
                    <select name="specialty"
                            id="specialty"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-gray-700 bg-white">
                        <option value="">All Specialties</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" {{ request('specialty') == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">
                        Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Results Count -->
        <div class="text-sm text-gray-600">
            Showing {{ $doctors->count() }} of {{ $doctors->total() }} doctors
        </div>

        <!-- Doctors Grid -->
        @if($doctors->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($doctors as $doctor)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-150">
                        <!-- Doctor Image -->
                        <div class="h-48 bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center">
                            <svg class="h-24 w-24 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>

                        <!-- Doctor Info -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Dr. {{ $doctor->user->name }}</h3>
                            <p class="text-sm text-secondary-600 font-medium mb-2">{{ $doctor->specialty->name }}</p>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $doctor->years_of_experience }} years experience
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $doctor->phone }}
                                </div>

                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $doctor->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $doctor->is_available ? 'Available' : 'Unavailable' }}
                                    </span>
                                </div>
                            </div>

                            <a href="{{ route('patient.doctors.show', $doctor) }}"
                               class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">
                                View Profile & Book
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $doctors->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No doctors found</h3>
                <p class="mt-2 text-sm text-gray-500">Try adjusting your search or filter criteria</p>
                <a href="{{ route('patient.doctors.index') }}" class="mt-4 inline-block text-primary-600 hover:text-primary-700">
                    Clear filters
                </a>
            </div>
        @endif
    </div>
</x-layouts.patient>
