<x-layouts.doctor>
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">My Patients</h1>
            <p class="mt-2 text-gray-600">View patients you have treated</p>
        </div>

        <!-- Search -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="GET" action="{{ route('doctor.patients.index') }}" class="flex items-end space-x-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Patient</label>
                    <input type="text"
                           name="search"
                           id="search"
                           value="{{ request('search') }}"
                           placeholder="Enter patient name..."
                           class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-secondary-500 focus:ring-secondary-500">
                </div>
                <button type="submit" class="px-6 py-2 bg-secondary-600 hover:bg-secondary-700 text-white font-semibold rounded-md transition duration-150">
                    Search
                </button>
            </form>
        </div>

        <!-- Results Count -->
        <div class="text-sm text-gray-600">
            Showing {{ $patients->count() }} of {{ $patients->total() }} patients
        </div>

        <!-- Patients List -->
        @if($patients->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($patients as $patient)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition duration-150">
                        <!-- Patient Profile Picture -->
                        <div class="h-48 bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center overflow-hidden">
                            <img src="{{ $patient->profile_image_url }}"
                                 alt="{{ $patient->user->name }}"
                                 class="h-full w-full object-cover">
                        </div>

                        <div class="p-6">
                            <!-- Patient Info -->
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900">{{ $patient->user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $patient->age }} years • {{ ucfirst($patient->gender) }}</p>
                            </div>

                            <!-- Patient Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $patient->phone }}
                                </div>

                                @if($patient->blood_type)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        Blood Type: {{ $patient->blood_type }}
                                    </div>
                                @endif

                                @if($patient->allergies)
                                    <div class="flex items-start text-sm text-gray-600">
                                        <svg class="h-4 w-4 mr-2 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        <span class="text-red-600 font-medium">Allergies: {{ Str::limit($patient->allergies, 30) }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- View Button -->
                            <a href="{{ route('doctor.patients.show', $patient) }}"
                               class="block w-full text-center bg-secondary-600 hover:bg-secondary-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">
                                View Full Record
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $patients->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No patients found</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if(request('search'))
                        No patients match your search criteria
                        <a href="{{ route('doctor.patients.index') }}" class="block mt-2 text-secondary-600 hover:text-secondary-700">
                            Clear search
                        </a>
                    @else
                        Patients will appear here after you complete appointments
                    @endif
                </p>
            </div>
        @endif
    </div>
</x-layouts.doctor>
