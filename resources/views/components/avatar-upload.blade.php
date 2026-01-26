@props(['user', 'currentImage' => null])

<div x-data="avatarUpload()" class="space-y-4">
    <!-- Current Avatar Display -->
    <div class="flex items-center space-x-6">
        <div class="relative">
            <img :src="previewUrl || '{{ $currentImage }}'"
                 alt="Profile Picture"
                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg ring-2 ring-gray-200">

            <!-- Upload Badge -->
            <div class="absolute bottom-0 right-0 bg-blue-600 rounded-full p-2 shadow-lg cursor-pointer hover:bg-blue-700 transition"
                 @click="$refs.fileInput.click()">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Profile Picture</h3>
            <p class="text-sm text-gray-600 mb-4">Upload a new profile picture. JPG, PNG or GIF. Max 2MB.</p>

            <div class="flex items-center space-x-3">
                <button type="button"
                        @click="$refs.fileInput.click()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-150">
                    <span x-show="!selectedFile">Choose Photo</span>
                    <span x-show="selectedFile" x-cloak>Change Photo</span>
                </button>

                @if($currentImage)
                    <button type="button"
                            @click="deleteAvatar()"
                            class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-medium rounded-lg transition duration-150">
                        Remove
                    </button>
                @endif
            </div>

            <!-- File Name Display -->
            <p x-show="selectedFile" x-cloak class="mt-2 text-sm text-gray-600">
                Selected: <span x-text="selectedFile?.name"></span>
            </p>
        </div>
    </div>

    <!-- Hidden File Input -->
    <input type="file"
           x-ref="fileInput"
           @change="handleFileSelect($event)"
           accept="image/jpeg,image/png,image/jpg,image/gif"
           class="hidden">

    <!-- Upload Progress -->
    <div x-show="uploading" x-cloak class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-sm text-blue-900 font-medium">Uploading...</span>
        </div>
    </div>

    <!-- Error Message -->
    <div x-show="error" x-cloak class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm text-red-900" x-text="error"></span>
        </div>
    </div>
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

            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                this.error = 'File size must be less than 2MB';
                return;
            }

            // Validate file type
            if (!file.type.match(/^image\/(jpeg|png|jpg|gif)$/)) {
                this.error = 'Only JPG, PNG, and GIF files are allowed';
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

        async uploadAvatar() {
            if (!this.selectedFile) return;

            this.uploading = true;
            this.error = null;

            const formData = new FormData();
            formData.append('avatar', this.selectedFile);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch('{{ route("profile.avatar.update") }}', {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    const data = await response.json();
                    this.error = data.message || 'Upload failed. Please try again.';
                }
            } catch (err) {
                this.error = 'Network error. Please try again.';
            } finally {
                this.uploading = false;
            }
        },

        async deleteAvatar() {
            if (!confirm('Are you sure you want to remove your profile picture?')) {
                return;
            }

            this.uploading = true;
            this.error = null;

            try {
                const response = await fetch('{{ route("profile.avatar.delete") }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    this.error = 'Failed to remove profile picture.';
                }
            } catch (err) {
                this.error = 'Network error. Please try again.';
            } finally {
                this.uploading = false;
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
