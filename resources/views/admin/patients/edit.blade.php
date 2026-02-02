<x-layouts.admin>
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Back Button -->
        <a href="{{ route('admin.patients.show', $patient) }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
            <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Patient Details
        </a>

        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">Edit Patient</h1>
            <p class="mt-2 text-gray-600">Update information for {{ $patient->user->name }}</p>
        </div>

        <!-- Profile Picture Section -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Profile Picture</h3>

            <div class="flex items-start space-x-6" x-data="avatarUpload()">
                <!-- Current Avatar Display -->
                <div class="flex-shrink-0">
                    <img :src="previewUrl || '{{ $patient->profile_image_url }}'"
                         alt="{{ $patient->user->name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg ring-2 ring-blue-200">
                </div>

                <div class="flex-1">
                    <p class="text-sm text-gray-600 mb-4">Upload a profile picture for the patient. JPG, PNG, GIF, or WebP. Max 5MB.</p>

                    <div class="flex items-center space-x-3">
                        <button type="button"
                                @click="$refs.fileInput.click()"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150">
                            <span x-show="!selectedFile">Choose Photo</span>
                            <span x-show="selectedFile" x-cloak>Change Photo</span>
                        </button>

                        @if($patient->profile_image)
                            <form method="POST" action="{{ route('admin.patients.avatar.delete', $patient) }}" class="inline">
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
                          action="{{ route('admin.patients.avatar.update', $patient) }}"
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
        <form method="POST" action="{{ route('admin.patients.update', $patient) }}" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <!-- Account Information -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $patient->user->name) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $patient->user->email) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $patient->phone) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact *</label>
                            <input type="tel" id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $patient->emergency_contact) }}" required
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('emergency_contact')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
                            <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth->format('Y-m-d')) }}" required
                                   max="{{ now()->format('Y-m-d') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                            @error('date_of_birth')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                            <select id="gender" name="gender" required
                                    class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                                <option value="male" {{ old('gender', $patient->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender', $patient->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender', $patient->gender) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('gender')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="blood_type" class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                            <select id="blood_type" name="blood_type"
                                    class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Blood Type</option>
                                <option value="A+" {{ old('blood_type', $patient->blood_type) == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ old('blood_type', $patient->blood_type) == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ old('blood_type', $patient->blood_type) == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ old('blood_type', $patient->blood_type) == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ old('blood_type', $patient->blood_type) == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ old('blood_type', $patient->blood_type) == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ old('blood_type', $patient->blood_type) == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ old('blood_type', $patient->blood_type) == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                            @error('blood_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address *</label>
                        <textarea id="address" name="address" rows="2" required
                                  class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">{{ old('address', $patient->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4">
                        <label for="allergies" class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                        <textarea id="allergies" name="allergies" rows="2"
                                  placeholder="List any known allergies..."
                                  class="w-full rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">{{ old('allergies', $patient->allergies) }}</textarea>
                        @error('allergies')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="border-t border-gray-200 pt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.patients.show', $patient) }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 font-semibold hover:bg-gray-50 transition duration-150">
                        Cancel
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md transition duration-150">
                        Update Patient
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
