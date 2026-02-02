<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.doctors.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Doctors
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Doctor</h1>
            <p class="mt-2 text-gray-600">Update information for Dr. {{ $doctor->user->name }}</p>
        </div>

        <!-- Profile Picture Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h3>

            <div class="flex items-start space-x-6" x-data="avatarUpload()">
                <!-- Current Avatar Display -->
                <div class="flex-shrink-0">
                    <img :src="previewUrl || '{{ $doctor->profile_image_url }}'"
                         alt="Dr. {{ $doctor->user->name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg ring-2 ring-green-200">
                </div>

                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-4">Upload a professional profile picture for the doctor. JPG, PNG, GIF, or WebP. Max 5MB.</p>

                    <div class="flex items-center space-x-3">
                        <button type="button"
                                @click="$refs.fileInput.click()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150">
                            <span x-show="!selectedFile">Choose Photo</span>
                            <span x-show="selectedFile" x-cloak>Change Photo</span>
                        </button>

                        @if($doctor->profile_image)
                            <form method="POST" action="{{ route('admin.doctors.avatar.delete', $doctor) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to remove this profile picture?')"
                                        class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition duration-150">
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- File Name Display -->
                    <p x-show="selectedFile" x-cloak class="mt-2 text-sm text-gray-600">
                        Selected: <span x-text="selectedFile?.name"></span>
                    </p>

                    <!-- Upload Form (Hidden) -->
                    <form method="POST"
                          action="{{ route('admin.doctors.avatar.update', $doctor) }}"
                          enctype="multipart/form-data"
                          x-ref="uploadForm"
                          class="hidden">
                        @csrf
                        <input type="file"
                               name="avatar"
                               x-ref="fileInput"
                               @change="handleFileSelect($event)"
                               accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                    </form>

                    <!-- Upload Progress -->
                    <div x-show="uploading" x-cloak class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm text-blue-900 font-medium">Uploading...</span>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div x-show="error" x-cloak class="mt-3 bg-red-50 border border-red-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm text-red-900" x-text="error"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- User Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $doctor->user->name) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $doctor->user->email) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                            <input type="password" id="password" name="password"
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password</p>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $doctor->phone) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Professional Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Professional Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="specialty_id" class="block text-sm font-medium text-gray-700 mb-1">Specialty *</label>
                            <select id="specialty_id" name="specialty_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                                @foreach($specialties as $specialty)
                                    <option value="{{ $specialty->id }}" {{ old('specialty_id', $doctor->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                        {{ $specialty->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialty_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">License Number *</label>
                            <input type="text" id="license_number" name="license_number" value="{{ old('license_number', $doctor->license_number) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('license_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="years_of_experience" class="block text-sm font-medium text-gray-700 mb-1">Years of Experience *</label>
                            <input type="number" id="years_of_experience" name="years_of_experience"
                                   value="{{ old('years_of_experience', $doctor->years_of_experience) }}" min="0" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('years_of_experience')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="is_available" class="block text-sm font-medium text-gray-700 mb-1">Availability Status *</label>
                            <select id="is_available" name="is_available" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                                <option value="1" {{ old('is_available', $doctor->is_available) == '1' ? 'selected' : '' }}>Available</option>
                                <option value="0" {{ old('is_available', $doctor->is_available) == '0' ? 'selected' : '' }}>Unavailable</option>
                            </select>
                            @error('is_available')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="qualifications" class="block text-sm font-medium text-gray-700 mb-1">Qualifications</label>
                        <textarea id="qualifications" name="qualifications" rows="2"
                                  class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">{{ old('qualifications', $doctor->qualifications) }}</textarea>
                        @error('qualifications')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                        <textarea id="bio" name="bio" rows="3"
                                  class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $doctor->bio) }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="border-t border-gray-200 pt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.doctors.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                        Update Doctor
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        function avatarUpload() {
            return {
                selectedFile: null,
                previewUrl: null,
                uploading: false,
                error: null,

                handleFileSelect(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    // Validate file size (5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        this.error = 'File size must be less than 5MB';
                        return;
                    }

                    // Validate file type
                    if (!file.type.match(/^image\/(jpeg|png|jpg|gif|webp)$/)) {
                        this.error = 'Only JPG, PNG, GIF, and WebP files are allowed';
                        return;
                    }

                    this.error = null;
                    this.selectedFile = file;

                    // Create preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.previewUrl = e.target.result;
                    };
                    reader.readAsDataURL(file);

                    // Auto-upload
                    this.uploadAvatar();
                },

                uploadAvatar() {
                    if (!this.selectedFile) return;

                    this.uploading = true;
                    this.error = null;

                    // Submit the form
                    this.$refs.uploadForm.submit();
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-layouts.admin>
