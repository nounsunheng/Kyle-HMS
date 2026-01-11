<x-layouts.admin>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h1 class="text-3xl font-bold text-gray-900">System Reports</h1>
            <p class="mt-2 text-gray-600">View system statistics and reports</p>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\User::count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-medium text-gray-500">Active Doctors</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Doctor::where('is_available', true)->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-medium text-gray-500">This Month</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Appointment::whereMonth('created_at', now()->month)->count() }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                <p class="text-3xl font-bold text-gray-900 mt-2">{{ \App\Models\Appointment::where('status', 'completed')->count() }}</p>
            </div>
        </div>

        <!-- Appointment Status Breakdown -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Appointment Status Breakdown</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Appointment::where('status', 'pending')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Pending</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <p class="text-2xl font-bold text-blue-600">{{ \App\Models\Appointment::where('status', 'confirmed')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Confirmed</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <p class="text-2xl font-bold text-green-600">{{ \App\Models\Appointment::where('status', 'completed')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Completed</p>
                </div>
                <div class="text-center p-4 bg-red-50 rounded-lg">
                    <p class="text-2xl font-bold text-red-600">{{ \App\Models\Appointment::where('status', 'cancelled')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">Cancelled</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <p class="text-2xl font-bold text-gray-600">{{ \App\Models\Appointment::where('status', 'no_show')->count() }}</p>
                    <p class="text-sm text-gray-600 mt-1">No Show</p>
                </div>
            </div>
        </div>

        <!-- Top Specialties -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Top Specialties by Doctor Count</h2>
            <div class="space-y-3">
                @foreach(\App\Models\Specialty::withCount('doctors')->orderBy('doctors_count', 'desc')->limit(5)->get() as $specialty)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="font-medium text-gray-900">{{ $specialty->name }}</span>
                        <span class="text-sm text-gray-600">{{ $specialty->doctors_count }} doctors</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
