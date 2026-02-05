<x-layouts.patient>
    <div class="space-y-6">
        <!-- Back Button -->
        <a href="{{ route('patient.doctors.index') }}" class="inline-flex items-center text-primary-600 hover:text-primary-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Doctors
        </a>

        <!-- Doctor Profile -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="md:flex">
                <!-- Doctor Image -->
                <div class="md:w-1/3 bg-gradient-to-br from-primary-100 to-primary-200 flex items-center justify-center p-12">
                    <div class="relative">
                        <img src="{{ $doctor->profile_image_url }}"
                             alt="Dr. {{ $doctor->user->name }}"
                             class="h-64 w-64 rounded-2xl object-cover shadow-2xl ring-4 ring-white">

                        <!-- Availability Badge -->
                        <span class="absolute top-4 right-4 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $doctor->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} shadow-lg">
                            {{ $doctor->is_available ? 'Available' : 'Unavailable' }}
                        </span>
                    </div>
                </div>

                <!-- Doctor Details -->
                <div class="md:w-2/3 p-8">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Dr. {{ $doctor->user->name }}</h1>
                            <p class="text-xl text-secondary-600 font-medium mt-1">{{ $doctor->specialty->name }}</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-gray-600">
                            <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span>License: {{ $doctor->license_number }}</span>
                        </div>

                        <div class="flex items-center text-gray-600">
                            <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $doctor->years_of_experience }} years of experience</span>
                        </div>

                        <div class="flex items-center text-gray-600">
                            <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>{{ $doctor->phone }}</span>
                        </div>

                        <div class="flex items-center text-gray-600">
                            <svg class="h-5 w-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $doctor->user->email }}</span>
                        </div>
                    </div>

                    @if($doctor->qualifications)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Qualifications</h3>
                            <p class="text-gray-600">{{ $doctor->qualifications }}</p>
                        </div>
                    @endif

                    @if($doctor->bio)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">About</h3>
                            <p class="text-gray-600">{{ $doctor->bio }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Schedules -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Available Schedules</h2>

            @if($doctor->schedules->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($doctor->schedules as $schedule)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-primary-500 transition duration-150">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center text-gray-700">
                                    <svg class="h-5 w-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-semibold">{{ $schedule->formatted_date }}</span>
                                </div>
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $schedule->formatted_time_range }}
                                </div>

                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    {{ $schedule->available_slots }} slots available
                                </div>
                            </div>

                            @if($schedule->is_full)
                                <button disabled class="w-full bg-gray-300 text-gray-500 font-semibold py-2 px-4 rounded-md cursor-not-allowed">
                                    Fully Booked
                                </button>
                            @else
                                <a href="{{ route('patient.appointments.create', ['schedule' => $schedule->id]) }}"
                                   class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white font-semibold py-2 px-4 rounded-md transition duration-150">
                                    Book Appointment
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No available schedules</h3>
                    <p class="mt-2 text-sm text-gray-500">This doctor has no upcoming available schedules at the moment.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.patient>
