@php
    $userRole = auth()->user()->hasRole('admin') ? 'admin' : (auth()->user()->hasRole('doctor') ? 'doctor' : 'patient');
    $layoutComponent = 'layouts.' . $userRole;
@endphp

<x-dynamic-component :component="$layoutComponent">
    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-10 relative">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-96 h-96 bg-indigo-500/20 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <h1 class="text-4xl font-bold text-white mb-3 flex items-center gap-3">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        My Profile
                    </h1>
                    <p class="text-blue-100 text-lg">Manage your account settings and preferences</p>
                    <div class="mt-4">
                        <span class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium border border-white/30">
                            {{ ucfirst($userRole) }} Account
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Success/Error Messages --}}
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-transition class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    <button @click="show = false" class="ml-auto text-green-500 hover:text-green-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div x-data="{ show: true }" x-show="show" x-transition class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Sidebar --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Profile Picture Card --}}
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200"
                     x-data="{
                         selectedFile: null,
                         selectedFileName: '',
                         avatarPreview: null,
                         uploading: false,

                         handleFileSelect(event) {
                             const file = event.target.files[0];

                             if (!file) return;

                             if (!file.type.match('image.*')) {
                                 alert('Please select an image file');
                                 return;
                             }

                             if (file.size > 5 * 1024 * 1024) {
                                 alert('File size must not exceed 5MB');
                                 return;
                             }

                             this.selectedFile = file;
                             this.selectedFileName = file.name;

                             const reader = new FileReader();
                             reader.onload = (e) => {
                                 this.avatarPreview = e.target.result;
                             };
                             reader.readAsDataURL(file);
                         },

                         async uploadAvatar() {
                             if (!this.selectedFile) {
                                 alert('Please select a file first');
                                 return;
                             }

                             this.uploading = true;

                             const formData = new FormData();
                             formData.append('avatar', this.selectedFile);

                             try {
                                 const response = await fetch('{{ route("profile.avatar.update") }}', {
                                     method: 'POST',
                                     body: formData,
                                     headers: {
                                         'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                         'Accept': 'application/json'
                                     }
                                 });

                                 if (response.ok) {
                                     window.location.reload();
                                 } else {
                                     const data = await response.json();
                                     alert(data.message || 'Failed to upload image');
                                 }
                             } catch (error) {
                                 alert('Failed to upload image. Please try again.');
                             } finally {
                                 this.uploading = false;
                             }
                         },

                         cancelSelection() {
                             this.selectedFile = null;
                             this.selectedFileName = '';
                             this.avatarPreview = null;
                             document.getElementById('avatar-input').value = '';
                         }
                     }">

                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Profile Picture
                    </h3>

                    <div class="flex flex-col items-center">
                        {{-- Avatar Display --}}
                        <div class="relative group">
                            <img :src="avatarPreview || '{{ auth()->user()->avatar_url }}'"
                                 alt="Profile Picture"
                                 class="w-48 h-48 rounded-2xl object-cover shadow-xl border-4 border-white ring-2 ring-gray-200 transition-transform group-hover:scale-105">

                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 rounded-2xl transition-all flex items-center justify-center">
                                <svg class="h-12 w-12 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Upload Controls --}}
                        <div class="w-full mt-6 space-y-4">
                            <input type="file"
                                   id="avatar-input"
                                   accept="image/*"
                                   @change="handleFileSelect($event)"
                                   style="display: none;">

                            <button type="button"
                                    @click="$event.preventDefault(); document.getElementById('avatar-input').click();"
                                    class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition duration-200 flex items-center justify-center">
                                <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                Choose New Picture
                            </button>

                            {{-- Selected File Info --}}
                            <div x-show="selectedFile" x-transition class="space-y-3">
                                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                    <p class="text-sm font-semibold text-green-900 flex items-center">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        File Selected
                                    </p>
                                    <p class="text-xs text-green-700 mt-1 truncate" x-text="selectedFileName"></p>
                                </div>

                                <div class="flex gap-2">
                                    <button type="button"
                                            @click="uploadAvatar"
                                            :disabled="uploading"
                                            class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-semibold rounded-lg transition duration-200 flex items-center justify-center">
                                        <svg x-show="!uploading" class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <svg x-show="uploading" class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span x-text="uploading ? 'Uploading...' : 'Upload'"></span>
                                    </button>

                                    <button type="button"
                                            @click="cancelSelection"
                                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition duration-200">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Avatar --}}
                        @if(auth()->user()->avatar_url && !str_contains(auth()->user()->avatar_url, 'ui-avatars.com'))
                            <form action="{{ route('profile.avatar.delete') }}" method="POST" class="w-full mt-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Remove profile picture?')"
                                        class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-medium rounded-lg border border-red-200 transition duration-200 flex items-center justify-center">
                                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Remove Picture
                                </button>
                            </form>
                        @endif

                        {{-- Guidelines --}}
                        <div class="w-full mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Upload Guidelines</h4>
                            <ul class="text-xs text-blue-800 space-y-1">
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>JPEG, PNG, GIF, or WebP format</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Maximum file size: 5MB</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Square images work best</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Minimum 500x500 pixels recommended</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- Account Info Card --}}
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Account Information</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Member Since</span>
                            <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Account Type</span>
                            <span class="text-sm font-semibold text-gray-900">{{ ucfirst($userRole) }}</span>
                        </div>
                        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Email Status</span>
                            @if(auth()->user()->email_verified_at)
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Verified</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs font-semibold rounded-full">Unverified</span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Last Updated</span>
                            <span class="text-sm font-semibold text-gray-900">{{ auth()->user()->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        @if($userRole === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Dashboard</span>
                            </a>
                        @elseif($userRole === 'doctor')
                            <a href="{{ route('doctor.dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('patient.dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Dashboard</span>
                            </a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center p-3 bg-red-50 rounded-lg hover:bg-red-100 transition duration-150">
                                <svg class="h-5 w-5 text-red-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="text-sm font-medium text-red-900">Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Right Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Profile Information --}}
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profile Information
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Update your account's profile information and email address.</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                {{-- Doctor-Specific Information --}}
                @if($userRole === 'doctor')
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-green-50 to-teal-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                                Professional Information
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Keep your professional information up to date.</p>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-doctor-information-form')
                        </div>
                    </div>
                @endif

                {{-- Patient-Specific Information --}}
                @if($userRole === 'patient')
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                        <div class="bg-gradient-to-r from-green-50 to-teal-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center">
                                <svg class="h-5 w-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Medical Information
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Keep your medical information up to date for better care.</p>
                        </div>
                        <div class="p-6">
                            @include('profile.partials.update-patient-information-form')
                        </div>
                    </div>
                @endif

                {{-- Update Password --}}
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Update Password
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">Ensure your account is using a long, random password to stay secure.</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                {{-- Delete Account --}}
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-red-200">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-bold text-red-900 flex items-center">
                            <svg class="h-5 w-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Danger Zone
                        </h3>
                        <p class="text-sm text-red-700 mt-1">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                    </div>
                    <div class="p-6">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
