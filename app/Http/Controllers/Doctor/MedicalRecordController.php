<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    /**
     * Show the form for creating a new medical record
     */
    public function create(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        // Verify doctor owns this appointment
        if ($appointment->schedule->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if appointment is completed
        if ($appointment->status !== 'completed') {
            return back()->with('error', 'Medical records can only be created for completed appointments.');
        }

        // Check if medical record already exists
        if ($appointment->medicalRecord) {
            return redirect()->route('doctor.medical-records.show', $appointment->medicalRecord)
                ->with('error', 'A medical record already exists for this appointment.');
        }

        $appointment->load('patient.user');

        return view('doctor.medical-records.create', compact('appointment'));
    }

    /**
     * Store a newly created medical record
     */
    public function store(Request $request, Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        // Verify doctor owns this appointment
        if ($appointment->schedule->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Validate
        $request->validate([
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'nullable|string|max:2000',
            'prescription' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);

        // Create medical record
        $medicalRecord = MedicalRecord::create([
            'patient_id' => $appointment->patient_id,
            'doctor_id' => $doctor->id,
            'appointment_id' => $appointment->id,
            'visit_date' => $appointment->schedule->schedule_date,
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
        ]);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Medical record created successfully!');
    }

    /**
     * Display the specified medical record
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $doctor = Auth::user()->doctor;

        // Verify doctor owns this medical record
        if ($medicalRecord->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        $medicalRecord->load(['patient.user', 'appointment']);

        return view('doctor.medical-records.show', compact('medicalRecord'));
    }

    /**
     * Show the form for editing the specified medical record
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $doctor = Auth::user()->doctor;

        // Verify doctor owns this medical record
        if ($medicalRecord->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        $medicalRecord->load(['patient.user', 'appointment']);

        return view('doctor.medical-records.edit', compact('medicalRecord'));
    }

    /**
     * Update the specified medical record
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $doctor = Auth::user()->doctor;

        // Verify doctor owns this medical record
        if ($medicalRecord->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'nullable|string|max:2000',
            'prescription' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
        ]);

        $medicalRecord->update([
            'diagnosis' => $request->diagnosis,
            'treatment' => $request->treatment,
            'prescription' => $request->prescription,
            'notes' => $request->notes,
        ]);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Medical record updated successfully!');
    }
}
