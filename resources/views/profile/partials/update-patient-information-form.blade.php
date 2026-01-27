<section>
    <form method="post" action="{{ route('profile.patient-info.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Basic Information -->
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
                    value="{{ old('phone', $user->patient->phone) }}"
                    required
                    placeholder="+855 12 345 678"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
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

            <!-- Date of Birth -->
            <div>
                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Date of Birth
                    </span>
                </label>
                <input
                    id="date_of_birth"
                    name="date_of_birth"
                    type="date"
                    value="{{ old('date_of_birth', $user->patient->date_of_birth->format('Y-m-d')) }}"
                    required
                    max="{{ date('Y-m-d') }}"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
                />
                <p class="mt-1 text-xs text-gray-500">Current age: {{ $user->patient->age }} years</p>
                @error('date_of_birth')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Gender -->
            <div>
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Gender
                    </span>
                </label>
                <select
                    id="gender"
                    name="gender"
                    required
                    class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
                >
                    <option value="male" {{ old('gender', $user->patient->gender) == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->patient->gender) == 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Blood Type -->
            <div>
                <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        Blood Type
                    </span>
                </label>
                <select
                    id="blood_type"
                    name="blood_type"
                    class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
                >
                    <option value="">Select Blood Type</option>
                    <option value="A+" {{ old('blood_type', $user->patient->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                    <option value="A-" {{ old('blood_type', $user->patient->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                    <option value="B+" {{ old('blood_type', $user->patient->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                    <option value="B-" {{ old('blood_type', $user->patient->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                    <option value="AB+" {{ old('blood_type', $user->patient->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                    <option value="AB-" {{ old('blood_type', $user->patient->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                    <option value="O+" {{ old('blood_type', $user->patient->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                    <option value="O-" {{ old('blood_type', $user->patient->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                </select>
                @error('blood_type')
                    <p class="mt-2 text-sm text-red-600 flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Address
                </span>
            </label>
            <textarea
                id="address"
                name="address"
                rows="3"
                required
                placeholder="Enter your full address"
                class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
            >{{ old('address', $user->patient->address) }}</textarea>
            @error('address')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Emergency Contact -->
        <div>
            <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Emergency Contact Number
                </span>
            </label>
            <input
                id="emergency_contact"
                name="emergency_contact"
                type="tel"
                value="{{ old('emergency_contact', $user->patient->emergency_contact) }}"
                required
                placeholder="+855 12 345 678"
                class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
            />
            <p class="mt-1 text-xs text-gray-500">Person to contact in case of emergency</p>
            @error('emergency_contact')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Allergies -->
        <div>
            <label for="allergies" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Allergies
                </span>
            </label>
            <textarea
                id="allergies"
                name="allergies"
                rows="2"
                placeholder="List any allergies (medications, foods, environmental, etc.)"
                class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
            >{{ old('allergies', $user->patient->allergies) }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Enter "None" if you have no known allergies</p>
            @error('allergies')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Medical History -->
        <div>
            <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                <span class="flex items-center">
                    <svg class="h-4 w-4 mr-1 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Medical History
                </span>
            </label>
            <textarea
                id="medical_history"
                name="medical_history"
                rows="4"
                placeholder="Describe any chronic conditions, past surgeries, or significant medical history"
                class="w-full rounded-lg border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition duration-150"
            >{{ old('medical_history', $user->patient->medical_history) }}</textarea>
            @error('medical_history')
                <p class="mt-2 text-sm text-red-600 flex items-center">
                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex items-center justify-end pt-4 border-t border-gray-200">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition duration-150">
                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save Medical Information
            </button>

            @if (session('status') === 'patient-info-updated')
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
