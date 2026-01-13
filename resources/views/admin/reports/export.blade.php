<x-layouts.admin>
    <div class="space-y-6">
        <!-- Back Button & Page Header -->
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
        </div>

        <!-- Export Center Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-8 text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-16 w-16 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-6">
                    <h1 class="text-4xl font-bold">Export Center</h1>
                    <p class="text-blue-100 mt-2 text-lg">Download detailed reports in CSV or Excel format with advanced filters</p>
                </div>
            </div>
        </div>

        <!-- Quick Export Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-blue-500">
                <p class="text-sm text-gray-600">Available Reports</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">6</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-green-500">
                <p class="text-sm text-gray-600">Export Formats</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">CSV & Excel</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-purple-500">
                <p class="text-sm text-gray-600">Total Records</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ \App\Models\User::count() + \App\Models\Appointment::count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-4 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-600">Last Updated</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">Real-time</p>
            </div>
        </div>

        <!-- Export Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <!-- Doctors Report -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Doctors Report</h3>
                        <svg class="h-8 w-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Export complete list of all doctors with their details, specialties, qualifications, and availability status.
                    </p>

                    <div class="space-y-2 mb-4 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-700">Includes:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Contact information</li>
                            <li>• Specialty & License details</li>
                            <li>• Years of experience</li>
                            <li>• Availability status</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.reports.export-doctors') }}" method="GET" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Filter by Specialty</label>
                            <select name="specialty_id" class="w-full text-sm rounded-md text-gray-700 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All Specialties</option>
                                @foreach(\App\Models\Specialty::orderBy('name')->get() as $specialty)
                                    <option value="{{ $specialty->id }}">{{ $specialty->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Availability</label>
                            <select name="availability" class="w-full text-sm rounded-md text-gray-700 border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">All</option>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>

                        <div class="flex space-x-2 pt-2">
                            <button type="submit" name="format" value="csv" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 shadow-sm">
                                <span class="flex items-center justify-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    CSV
                                </span>
                            </button>
                            <button type="submit" name="format" value="excel" class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 shadow-sm">
                                <span class="flex items-center justify-center">
                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    Excel
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Patients Report -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Patients Report</h3>
                        <svg class="h-8 w-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Export complete list of all registered patients with their personal information and medical details.
                    </p>

                    <div class="space-y-2 mb-4 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-700">Includes:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Personal information</li>
                            <li>• Contact details</li>
                            <li>• Medical information</li>
                            <li>• Blood type & allergies</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.reports.export-patients') }}" method="GET" class="space-y-3">
                        <div class="h-32"></div> <!-- Spacer to align with other cards -->

                        <button type="submit" name="format" value="csv" class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 shadow-sm">
                            <span class="flex items-center justify-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export as CSV
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Appointments Report -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Appointments</h3>
                        <svg class="h-8 w-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Export appointments with patient and doctor details, dates, times, and status information.
                    </p>

                    <div class="space-y-2 mb-4 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-700">Includes:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Patient & doctor names</li>
                            <li>• Appointment dates & times</li>
                            <li>• Status information</li>
                            <li>• Reason for visit</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.reports.export-appointments') }}" method="GET" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full text-sm rounded-md text-gray-700 border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="">All Statuses</option>
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="no_show">No Show</option>
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">From</label>
                                <input type="date" name="date_from" class="w-full text-sm rounded-md text-gray-700 border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">To</label>
                                <input type="date" name="date_to" class="w-full text-sm rounded-md border-gray-300 shadow-sm text-gray-700 focus:border-purple-500 focus:ring-purple-500">
                            </div>
                        </div>

                        <button type="submit" name="format" value="csv" class="w-full bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 shadow-sm">
                            <span class="flex items-center justify-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export as CSV
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Medical Records Report -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Medical Records</h3>
                        <svg class="h-8 w-8 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Export medical records with diagnoses, treatments, prescriptions, and visit information.
                    </p>

                    <div class="space-y-2 mb-4 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-700">Includes:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• Visit dates & diagnoses</li>
                            <li>• Treatment information</li>
                            <li>• Prescriptions</li>
                            <li>• Doctor notes</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.reports.export-medical-records') }}" method="GET" class="space-y-3">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">From</label>
                                <input type="date" name="date_from" class="w-full text-sm rounded-md text-gray-700 border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">To</label>
                                <input type="date" name="date_to" class="w-full text-sm rounded-md text-gray-700 border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>

                        <div class="h-16"></div> <!-- Spacer -->

                        <button type="submit" name="format" value="csv" class="w-full bg-red-600 hover:bg-red-700 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 shadow-sm">
                            <span class="flex items-center justify-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export as CSV
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- System Summary -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">System Summary</h3>
                        <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Export comprehensive system overview with all statistics, metrics, and key performance indicators.
                    </p>

                    <div class="space-y-2 mb-4 bg-gray-50 rounded-lg p-3">
                        <p class="text-xs font-semibold text-gray-700">Includes:</p>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li>• All system statistics</li>
                            <li>• Status breakdowns</li>
                            <li>• Top specialties</li>
                            <li>• Activity summary</li>
                        </ul>
                    </div>

                    <form action="{{ route('admin.reports.export-summary') }}" method="GET" class="space-y-3">
                        <div class="h-32"></div> <!-- Spacer -->

                        <button type="submit" name="format" value="csv" class="w-full bg-gray-700 hover:bg-gray-800 text-white text-sm font-semibold py-2.5 px-4 rounded-md transition duration-150 shadow-sm">
                            <span class="flex items-center justify-center">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                Export Summary
                            </span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Links Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition duration-300">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Quick Links</h3>
                        <svg class="h-8 w-8 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">
                        Access other management pages and system features quickly.
                    </p>

                    <div class="space-y-3">
                        <a href="{{ route('admin.doctors.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <svg class="h-5 w-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Manage Doctors</span>
                        </a>

                        <a href="{{ route('admin.patients.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <svg class="h-5 w-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Manage Patients</span>
                        </a>

                        <a href="{{ route('admin.appointments.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <svg class="h-5 w-5 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">View Appointments</span>
                        </a>

                        <a href="{{ route('admin.specialties.index') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <svg class="h-5 w-5 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Manage Specialties</span>
                        </a>

                        <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition duration-150">
                            <svg class="h-5 w-5 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="text-sm font-medium text-gray-900">Dashboard</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Information Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-lg p-6">
            <div class="flex">
                <svg class="h-6 w-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-blue-900 mb-1">Export Information</h3>
                    <p class="text-sm text-blue-800">
                        All exports are generated in real-time with the most current data. CSV files can be opened in Excel, Google Sheets, or any spreadsheet application. Files are named with timestamps (e.g., doctors_report_2026-01-12_143022.csv) for easy organization. Use the filters to narrow down your export data before downloading.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
