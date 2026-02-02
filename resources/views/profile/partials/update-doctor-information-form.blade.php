<section>
    <form method="post" action="{{ route('profile.doctor-info.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Contact Information -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Phone -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        Phone Number
                    </span>
                </label>
                <input
                    id="phone"
                    name="phone"
                    type="tel"
                    value="{{ old('phone', $user->doctor->phone) }}"
                    required
                    placeholder="+855 12 345 678"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition duration-150"
                />
                @error('phone')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Specialty (Read-only, admin can change this) -->
            <div>
                <label for="specialty" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        Specialty
                    </span>
                </label>
                <input
                    type="text"
                    value="{{ $user->doctor->specialty->name }}"
                    disabled
                    class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm text-gray-600 cursor-not-allowed"
                />
                <p class="mt-1 text-xs text-gray-500">Contact administrator to change your specialty</p>
            </div>
        </div>

        <!-- Professional Information -->
        <div class="grid md:grid-cols-2 gap-6">
            <!-- License Number (Read-only) -->
            <div>
                <label for="license_number" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        License Number
                    </span>
                </label>
                <input
                    type="text"
                    value="{{ $user->doctor->license_number }}"
                    disabled
                    class="w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm text-gray-600 cursor-not-allowed"
                />
                <p class="mt-1 text-xs text-gray-500">Contact administrator to update license number</p>
            </div>

            <!-- Years of Experience -->
            <div>
                <label for="years_of_experience" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Years of Experience
                    </span>
                </label>
                <input
                    id="years_of_experience"
                    name="years_of_experience"
                    type="number"
                    value="{{ old('years_of_experience', $user->doctor->years_of_experience) }}"
                    required
                    min="0"
                    max="70"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition duration-150"
                />
                @error('years_of_experience')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Qualifications -->
        <div>
            <label for="qualifications" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                    Qualifications
                </span>
            </label>
            <textarea
                id="qualifications"
                name="qualifications"
                rows="3"
                placeholder="MD, PhD, Board Certifications, etc."
                class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition duration-150"
            >{{ old('qualifications', $user->doctor->qualifications) }}</textarea>
            <p class="mt-1 text-xs text-gray-500">List your degrees, certifications, and qualifications</p>
            @error('qualifications')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Bio -->
        <div>
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Professional Bio
                </span>
            </label>
            <textarea
                id="bio"
                name="bio"
                rows="5"
                placeholder="Share your professional background, areas of expertise, and approach to patient care..."
                class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-green-500 focus:ring-2 focus:ring-green-500 transition duration-150"
            >{{ old('bio', $user->doctor->bio) }}</textarea>
            <p class="mt-1 text-xs text-gray-500">This will be visible to patients when they view your profile</p>
            @error('bio')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Statistics Display (Read-only) -->
        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
            <h4 class="text-sm font-semibold text-gray-900 mb-3">Your Statistics</h4>
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $user->doctor->schedules->count() }}</p>
                    <p class="text-xs text-gray-600">Total Schedules</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $user->doctor->appointments->count() }}</p>
                    <p class="text-xs text-gray-600">Total Appointments</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $user->doctor->medicalRecords->count() }}</p>
                    <p class="text-xs text-gray-600">Medical Records</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition duration-150">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Professional Information
            </button>

            @if (session('status') === 'doctor-info-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="ml-4 text-sm text-green-600 font-medium flex items-center">
                    <svg class="h-5 w-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Saved successfully!
                </p>
            @endif
        </div>
    </form>
</section>
