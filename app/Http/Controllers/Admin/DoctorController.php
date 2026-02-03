<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors
     */
    public function index(Request $request)
    {
        $query = Doctor::with(['user', 'specialty']);

        // Search
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by specialty
        if ($request->filled('specialty')) {
            $query->where('specialty_id', $request->specialty);
        }

        // Filter by availability
        if ($request->filled('availability')) {
            $query->where('is_available', $request->availability === 'available');
        }

        $doctors = $query->paginate(15);
        $specialties = Specialty::orderBy('name')->get();

        return view('admin.doctors.index', compact('doctors', 'specialties'));
    }

    /**
     * Show the form for creating a new doctor
     */
    public function create()
    {
        $specialties = Specialty::orderBy('name')->get();
        return view('admin.doctors.create', compact('specialties'));
    }

    /**
     * Store a newly created doctor
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'specialty_id' => 'required|exists:specialties,id',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|max:50|unique:doctors',
            'qualifications' => 'nullable|string',
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'usertype' => 'doctor',
            ]);

            // Create doctor profile
            Doctor::create([
                'user_id' => $user->id,
                'specialty_id' => $request->specialty_id,
                'phone' => $request->phone,
                'license_number' => $request->license_number,
                'qualifications' => $request->qualifications,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'is_available' => true,
            ]);

            // Assign doctor role
            $user->assignRole('doctor');

            DB::commit();

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create doctor. Please try again.');
        }
    }

    /**
     * Display the specified doctor
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'specialty', 'schedules', 'appointments']);
        return view('admin.doctors.show', compact('doctor'));
    }

    /**
     * Show the form for editing the specified doctor
     */
    public function edit(Doctor $doctor)
    {
        $specialties = Specialty::orderBy('name')->get();
        $doctor->load('user');
        return view('admin.doctors.edit', compact('doctor', 'specialties'));
    }

    /**
     * Update the specified doctor
     */
    public function update(Request $request, Doctor $doctor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $doctor->user_id,
            'specialty_id' => 'required|exists:specialties,id',
            'phone' => 'required|string|max:20',
            'license_number' => 'required|string|max:50|unique:doctors,license_number,' . $doctor->id,
            'qualifications' => 'nullable|string',
            'years_of_experience' => 'required|integer|min:0',
            'bio' => 'nullable|string',
            'is_available' => 'required|boolean',
        ]);

        DB::beginTransaction();

        try {
            // Update user
            $doctor->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $doctor->user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // Update doctor profile
            $doctor->update([
                'specialty_id' => $request->specialty_id,
                'phone' => $request->phone,
                'license_number' => $request->license_number,
                'qualifications' => $request->qualifications,
                'years_of_experience' => $request->years_of_experience,
                'bio' => $request->bio,
                'is_available' => $request->is_available,
            ]);

            DB::commit();

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update doctor. Please try again.');
        }
    }

    /**
     * Update doctor's profile picture (Admin)
     */
    public function updateAvatar(Request $request, Doctor $doctor)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max
        ]);

        DB::beginTransaction();

        try {
            // Delete old avatar if exists
            if ($doctor->profile_image && Storage::disk('public')->exists($doctor->profile_image)) {
                Storage::disk('public')->delete($doctor->profile_image);
            }

            // Process and store new avatar
            $image = $request->file('avatar');
            $timestamp = time();
            $filename = "avatars/doctor_{$doctor->id}_{$timestamp}.jpg";

            // Ensure avatars directory exists
            if (!Storage::disk('public')->exists('avatars')) {
                Storage::disk('public')->makeDirectory('avatars');
            }

            // Create optimized image
            $img = Image::read($image);
            $img->cover(500, 500); // Resize and crop to square
            $encodedImage = $img->toJpeg(85); // 85% quality

            // Store image
            Storage::disk('public')->put($filename, $encodedImage);

            // Update database
            $doctor->update(['profile_image' => $filename]);

            DB::commit();

            return redirect()->route('admin.doctors.edit', $doctor)
                ->with('success', 'Profile picture updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update profile picture: ' . $e->getMessage());
        }
    }

    /**
     * Delete doctor's profile picture (Admin)
     */
    public function deleteAvatar(Doctor $doctor)
    {
        DB::beginTransaction();

        try {
            // Delete avatar file
            if ($doctor->profile_image && Storage::disk('public')->exists($doctor->profile_image)) {
                Storage::disk('public')->delete($doctor->profile_image);
            }

            // Update database
            $doctor->update(['profile_image' => null]);

            DB::commit();

            return redirect()->route('admin.doctors.edit', $doctor)
                ->with('success', 'Profile picture removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to remove profile picture.');
        }
    }

    /**
     * Remove the specified doctor
     */
    public function destroy(Doctor $doctor)
    {
        DB::beginTransaction();

        try {
            // Delete avatar if exists
            if ($doctor->profile_image && Storage::disk('public')->exists($doctor->profile_image)) {
                Storage::disk('public')->delete($doctor->profile_image);
            }

            // Delete doctor and associated user
            $user = $doctor->user;
            $doctor->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('admin.doctors.index')
                ->with('success', 'Doctor deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete doctor. This doctor may have associated records.');
        }
    }
}
