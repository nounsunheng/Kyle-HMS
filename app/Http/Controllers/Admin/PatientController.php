<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;

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

        return redirect()->route('admin.patients.index')
            ->with('success', 'Patient updated successfully!');
    }

    /**
     * Remove the specified patient
     */
    public function destroy(Patient $patient)
    {
        try {
            $user = $patient->user;
            $patient->delete();
            $user->delete();

            return redirect()->route('admin.patients.index')
                ->with('success', 'Patient deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete patient. This patient may have associated records.');
        }
    }
}
