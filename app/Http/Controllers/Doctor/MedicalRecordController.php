<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    /**
     * Show the form for creating a new medical record (directly from appointment completion)
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
     * Store medical record with file upload
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
            'medical_record_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'diagnosis.required' => 'Diagnosis is required.',
            'medical_record_file.required' => 'Medical record file is required.',
            'medical_record_file.mimes' => 'File must be PDF, DOC, DOCX, JPG, JPEG, or PNG.',
            'medical_record_file.max' => 'File size must not exceed 10MB.',
        ]);

        DB::beginTransaction();

        try {
            // Handle file upload
            $filePath = null;
            $fileName = null;
            $fileType = null;
            $fileSize = null;

            if ($request->hasFile('medical_record_file')) {
                $file = $request->file('medical_record_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('medical_records', $fileName, 'public');
                $fileType = $file->getClientMimeType();
                $fileSize = $file->getSize();
            }

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
                'file_path' => $filePath,
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
            ]);

            DB::commit();

            return redirect()->route('doctor.medical-records.show', $medicalRecord)
                ->with('success', 'Medical record created successfully with file attachment!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if transaction failed
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->with('error', 'Failed to create medical record. Please try again.');
        }
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
            'medical_record_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();

        try {
            $updateData = [
                'diagnosis' => $request->diagnosis,
                'treatment' => $request->treatment,
                'prescription' => $request->prescription,
                'notes' => $request->notes,
            ];

            // Handle new file upload if provided
            if ($request->hasFile('medical_record_file')) {
                // Delete old file if exists
                if ($medicalRecord->file_path && Storage::disk('public')->exists($medicalRecord->file_path)) {
                    Storage::disk('public')->delete($medicalRecord->file_path);
                }

                $file = $request->file('medical_record_file');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('medical_records', $fileName, 'public');

                $updateData['file_path'] = $filePath;
                $updateData['file_name'] = $fileName;
                $updateData['file_type'] = $file->getClientMimeType();
                $updateData['file_size'] = $file->getSize();
            }

            $medicalRecord->update($updateData);

            DB::commit();

            return redirect()->route('doctor.medical-records.show', $medicalRecord)
                ->with('success', 'Medical record updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update medical record. Please try again.');
        }
    }
}
