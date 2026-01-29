<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Intervention\Image\Laravel\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Get role-specific data
        $roleData = [];

        if ($user->isPatient()) {
            $roleData['patient'] = $user->patient;
        } elseif ($user->isDoctor()) {
            $roleData['doctor'] = $user->doctor;
            $roleData['specialty'] = $user->doctor->specialty;
        } elseif ($user->isAdmin()) {
            $roleData['admin'] = $user->admin;
        }

        return view('profile.edit', array_merge(['user' => $user], $roleData));
    }

    /**
     * Update the user's profile information
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        DB::beginTransaction();

        try {
            // Update base user information
            $user->fill($request->validated());

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();

            // Update role-specific information
            $this->updateRoleSpecificData($user, $request);

            // Clear user dashboard cache
            $this->clearUserCache($user);

            DB::commit();

            return Redirect::route('profile.edit')
                ->with('status', 'profile-updated')
                ->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Profile Update Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update profile avatar/picture
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB max
        ], [
            'avatar.required' => 'Please select an image to upload.',
            'avatar.image' => 'The file must be an image.',
            'avatar.mimes' => 'Only JPEG, PNG, JPG, GIF, and WebP images are allowed.',
            'avatar.max' => 'Image size must not exceed 5MB.',
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            // Delete old avatar if exists
            $this->deleteOldAvatar($user);

            // Process and store new avatar
            $image = $request->file('avatar');
            $filename = $this->processAndStoreAvatar($image, $user);

            // Update database
            $this->updateAvatarInDatabase($user, $filename);

            // Clear cache
            $this->clearUserCache($user);

            DB::commit();

            Log::info('Avatar uploaded successfully', [
                'user_id' => $user->id,
                'filename' => $filename
            ]);

            return Redirect::route('profile.edit')
                ->with('status', 'avatar-updated')
                ->with('success', 'Profile picture updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Avatar Upload Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'file_info' => [
                    'name' => $request->file('avatar')?->getClientOriginalName(),
                    'size' => $request->file('avatar')?->getSize(),
                    'mime' => $request->file('avatar')?->getMimeType(),
                ],
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to update profile picture: ' . $e->getMessage());
        }
    }

    /**
     * Delete profile avatar
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        DB::beginTransaction();

        try {
            $this->deleteOldAvatar($user);
            $this->updateAvatarInDatabase($user, null);
            $this->clearUserCache($user);

            DB::commit();

            return Redirect::route('profile.edit')
                ->with('status', 'avatar-deleted')
                ->with('success', 'Profile picture removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Avatar Delete Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to remove profile picture.');
        }
    }

    /**
     * Delete the user's account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        DB::beginTransaction();

        try {
            // Delete avatar if exists
            $this->deleteOldAvatar($user);

            Auth::logout();

            $user->delete();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            DB::commit();

            return Redirect::to('/')
                ->with('success', 'Your account has been deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Account Delete Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to delete account. Please contact support.');
        }
    }

    /**
     * Process and store avatar with optimization
     */
    private function processAndStoreAvatar($image, $user): string
    {
        try {
            $rolePrefix = $this->getUserRolePrefix($user);
            $timestamp = time();
            $filename = "avatars/{$rolePrefix}_{$user->id}_{$timestamp}.jpg";

            // Ensure avatars directory exists
            if (!Storage::disk('public')->exists('avatars')) {
                Storage::disk('public')->makeDirectory('avatars');
            }

            // Create optimized image using Intervention Image
            $img = Image::read($image);

            // Resize and crop to square (500x500)
            $img->cover(500, 500);

            // Convert to JPEG with 85% quality for optimal size/quality ratio
            $encodedImage = $img->toJpeg(85);

            // Store in public disk
            $success = Storage::disk('public')->put($filename, $encodedImage);

            if (!$success) {
                throw new \Exception('Failed to save image to storage');
            }

            // Verify file was created
            if (!Storage::disk('public')->exists($filename)) {
                throw new \Exception('Image file not found after save');
            }

            Log::info('Avatar processed and stored', [
                'filename' => $filename,
                'path' => Storage::disk('public')->path($filename)
            ]);

            return $filename;

        } catch (\Exception $e) {
            Log::error('Avatar processing error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get user role prefix for filename
     */
    private function getUserRolePrefix($user): string
    {
        if ($user->isPatient()) return 'patient';
        if ($user->isDoctor()) return 'doctor';
        if ($user->isAdmin()) return 'admin';
        return 'user';
    }

    /**
     * Update role-specific data
     */
    private function updateRoleSpecificData($user, Request $request): void
    {
        if ($user->isPatient() && $user->patient) {
            $patientData = $request->only([
                'phone', 'date_of_birth', 'gender', 'address',
                'emergency_contact', 'blood_type', 'allergies', 'medical_history'
            ]);

            // Filter out empty values
            $patientData = array_filter($patientData, function($value) {
                return $value !== null && $value !== '';
            });

            if (!empty($patientData)) {
                $user->patient->update($patientData);
            }
        }

        if ($user->isDoctor() && $user->doctor) {
            $doctorData = $request->only([
                'phone', 'license_number', 'qualifications',
                'years_of_experience', 'bio'
            ]);

            $doctorData = array_filter($doctorData, function($value) {
                return $value !== null && $value !== '';
            });

            if (!empty($doctorData)) {
                $user->doctor->update($doctorData);
            }
        }

        if ($user->isAdmin() && $user->admin) {
            $adminData = $request->only(['phone']);

            if (!empty($adminData['phone'])) {
                $user->admin->update($adminData);
            }
        }
    }

    /**
     * Delete old avatar file
     */
    private function deleteOldAvatar($user): void
    {
        $oldAvatar = null;

        if ($user->isPatient() && $user->patient && $user->patient->profile_image) {
            $oldAvatar = $user->patient->profile_image;
        } elseif ($user->isDoctor() && $user->doctor && $user->doctor->profile_image) {
            $oldAvatar = $user->doctor->profile_image;
        } elseif ($user->isAdmin() && $user->admin && $user->admin->profile_image) {
            $oldAvatar = $user->admin->profile_image;
        }

        if ($oldAvatar) {
            if (Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
                Log::info('Old avatar deleted', ['path' => $oldAvatar]);
            } else {
                Log::warning('Old avatar file not found', ['path' => $oldAvatar]);
            }
        }
    }

    /**
     * Update avatar in database
     */
    private function updateAvatarInDatabase($user, $filename): void
    {
        if ($user->isPatient() && $user->patient) {
            $user->patient->update(['profile_image' => $filename]);
            Log::info('Patient avatar updated in database', [
                'user_id' => $user->id,
                'patient_id' => $user->patient->id,
                'filename' => $filename
            ]);
        } elseif ($user->isDoctor() && $user->doctor) {
            $user->doctor->update(['profile_image' => $filename]);
            Log::info('Doctor avatar updated in database', [
                'user_id' => $user->id,
                'doctor_id' => $user->doctor->id,
                'filename' => $filename
            ]);
        } elseif ($user->isAdmin() && $user->admin) {
            $user->admin->update(['profile_image' => $filename]);
            Log::info('Admin avatar updated in database', [
                'user_id' => $user->id,
                'admin_id' => $user->admin->id,
                'filename' => $filename
            ]);
        } else {
            Log::error('No related profile found for user', [
                'user_id' => $user->id,
                'usertype' => $user->usertype,
                'is_patient' => $user->isPatient(),
                'is_doctor' => $user->isDoctor(),
                'is_admin' => $user->isAdmin(),
                'has_patient' => isset($user->patient),
                'has_doctor' => isset($user->doctor),
                'has_admin' => isset($user->admin),
            ]);
            throw new \Exception('User profile relationship not found');
        }
    }

    /**
     * Clear user-specific cache
     */
    private function clearUserCache($user): void
    {
        try {
            if ($user->isPatient() && $user->patient) {
                Cache::forget("patient_dashboard_{$user->patient->id}");
                Log::info('Patient cache cleared', ['patient_id' => $user->patient->id]);
            } elseif ($user->isDoctor() && $user->doctor) {
                Cache::forget("doctor_dashboard_{$user->doctor->id}");
                Log::info('Doctor cache cleared', ['doctor_id' => $user->doctor->id]);
            } elseif ($user->isAdmin() && $user->admin) {
                // Don't use tags for file cache driver - just forget specific keys
                Cache::forget("admin_dashboard_{$user->admin->id}");
                Cache::forget("admin_stats_{$user->admin->id}");
                Cache::forget("admin_reports_{$user->admin->id}");
                Log::info('Admin cache cleared', ['admin_id' => $user->admin->id]);
            }
        } catch (\Exception $e) {
            // Log cache clear error but don't fail the upload
            Log::warning('Cache clear failed (non-critical)', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
        }
    }

    /**
     * Update patient-specific medical information
     */
    public function updatePatientInfo(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Only allow patients to update
        if (!$user->isPatient() || !$user->patient) {
            return Redirect::route('profile.edit')->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female,other'],
            'address' => ['required', 'string'],
            'emergency_contact' => ['required', 'string', 'max:20'],
            'blood_type' => ['nullable', 'string', 'max:5'],
            'allergies' => ['nullable', 'string'],
            'medical_history' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();

        try {
            $user->patient->update($validated);

            // Clear cache
            Cache::forget("patient_dashboard_{$user->patient->id}");

            DB::commit();

            return Redirect::route('profile.edit')
                ->with('status', 'patient-info-updated')
                ->with('success', 'Medical information updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Patient Info Update Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to update medical information. Please try again.');
        }
    }

    /**
     * Update doctor-specific professional information
     */
    public function updateDoctorInfo(Request $request): RedirectResponse
    {
        $user = $request->user();

        // Only allow doctors to update
        if (!$user->isDoctor() || !$user->doctor) {
            return Redirect::route('profile.edit')->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:70'],
            'qualifications' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::beginTransaction();

        try {
            $user->doctor->update($validated);

            // Clear cache
            Cache::forget("doctor_dashboard_{$user->doctor->id}");

            DB::commit();

            return Redirect::route('profile.edit')
                ->with('status', 'doctor-info-updated')
                ->with('success', 'Professional information updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Doctor Info Update Error: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);

            return Redirect::route('profile.edit')
                ->with('error', 'Failed to update professional information. Please try again.');
        }
    }
}
