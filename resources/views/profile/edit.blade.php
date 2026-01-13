@php
    $userRole = auth()->user()->hasRole('admin') ? 'admin' : (auth()->user()->hasRole('doctor') ? 'doctor' : 'patient');
    $layoutComponent = 'layouts.' . $userRole;
@endphp

<x-dynamic-component :component="$layoutComponent">
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
                    <p class="mt-2 text-gray-600">Manage your account settings and preferences</p>
                </div>
                <div class="h-16 w-16 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center">
                <div class="h-20 w-20 bg-white bg-opacity-20 backdrop-blur-sm rounded-full flex items-center justify-center">
                    <span class="text-3xl font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                </div>
                <div class="ml-6">
                    <h2 class="text-2xl font-bold">{{ auth()->user()->name }}</h2>
                    <p class="text-blue-100 mt-1">{{ auth()->user()->email }}</p>
                    <div class="mt-2">
                        <span class="px-3 py-1 bg-white bg-opacity-20 backdrop-blur-sm rounded-full text-sm font-medium">
                            {{ ucfirst($userRole) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Profile Information -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Update Password -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

                <!-- Delete Account -->
                <div class="bg-white rounded-lg shadow-sm overflow-hidden border-2 border-red-200">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                        <h3 class="text-lg font-semibold text-red-900 flex items-center">
                            <svg class="h-5 w-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Account Stats -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                    <div class="space-y-4">
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

                <!-- Security Tips -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-start">
                        <svg class="h-6 w-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-900 mb-2">Security Tips</h4>
                            <ul class="text-xs text-blue-800 space-y-2">
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Use a strong, unique password</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Never share your password</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Update your password regularly</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="mr-2">•</span>
                                    <span>Log out from shared devices</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        @if($userRole === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Go to Dashboard</span>
                            </a>
                        @elseif($userRole === 'doctor')
                            <a href="{{ route('doctor.dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Go to Dashboard</span>
                            </a>
                            <a href="{{ route('doctor.schedule.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Manage Schedule</span>
                            </a>
                        @else
                            <a href="{{ route('patient.dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">Go to Dashboard</span>
                            </a>
                            <a href="{{ route('patient.appointments.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                                <svg class="h-5 w-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-900">My Appointments</span>
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
        </div>
    </div>
</x-dynamic-component>
