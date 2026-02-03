<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $query = Patient::with('user');

        // Search
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            })->orWhere('phone', 'like', '%' . $request->search . '%');
        }

        $patients = $query->paginate(15);

        return view('admin.patients.index', compact('patients'));
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $patient->load(['user', 'appointments.schedule.doctor.user', 'medicalRecords.doctor.user']);
        return view('admin.patients.show', compact('patient'));
    }

    /**
     * Show the form for editing the specified patient
     */
    public function edit(Patient $patient)
    {
        $patient->load('user');
        return view('admin.patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient
     */
    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $patient->user_id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'address' => 'required|string|max:500',
            'emergency_contact' => 'required|string|max:20',
            'blood_type' => 'nullable|string|max:5',
            'allergies' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // Update user
            $patient->user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update patient profile
            $patient->update([
                'phone' => $request->phone,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => $request->address,
                'emergency_contact' => $request->emergency_contact,
                'blood_type' => $request->blood_type,
                'allergies' => $request->allergies,
            ]);

            DB::commit();

            return redirect()->route('admin.patients.index')
                ->with('success', 'Patient updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update patient. Please try again.');
        }
    }

    /**
     * Update patient's profile picture (Admin)
     */
    public function updateAvatar(Request $request, Patient $patient)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max
        ]);

        DB::beginTransaction();

        try {
            // Delete old avatar if exists
            if ($patient->profile_image && Storage::disk('public')->exists($patient->profile_image)) {
                Storage::disk('public')->delete($patient->profile_image);
            }

            // Process and store new avatar
            $image = $request->file('avatar');
            $timestamp = time();
            $filename = "avatars/patient_{$patient->id}_{$timestamp}.jpg";

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
            $patient->update(['profile_image' => $filename]);

            DB::commit();

            return redirect()->route('admin.patients.edit', $patient)
                ->with('success', 'Profile picture updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update profile picture: ' . $e->getMessage());
        }
    }

    /**
     * Delete patient's profile picture (Admin)
     */
    public function deleteAvatar(Patient $patient)
    {
        DB::beginTransaction();

        try {
            // Delete avatar file
            if ($patient->profile_image && Storage::disk('public')->exists($patient->profile_image)) {
                Storage::disk('public')->delete($patient->profile_image);
            }

            // Update database
            $patient->update(['profile_image' => null]);

            DB::commit();

            return redirect()->route('admin.patients.edit', $patient)
                ->with('success', 'Profile picture removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to remove profile picture.');
        }
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        DB::beginTransaction();

        try {
            // Delete avatar if exists
            if ($patient->profile_image && Storage::disk('public')->exists($patient->profile_image)) {
                Storage::disk('public')->delete($patient->profile_image);
            }

            $user = $patient->user;
            $patient->delete();
            $user->delete();

            DB::commit();

            return redirect()->route('admin.patients.index')
                ->with('success', 'Patient deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete patient. This patient may have associated records.');
        }
    }
}
