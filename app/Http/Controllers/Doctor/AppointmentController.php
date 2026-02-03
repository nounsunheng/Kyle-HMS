<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;

        $query = Appointment::with(['patient.user', 'schedule'])
            ->whereHas('schedule', function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            });

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('schedule_date', $request->date);
            });
        }

        $appointments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('doctor.appointments.index', compact('appointments'));
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        // Ensure doctor can only view their own appointments
        if ($appointment->schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        $appointment->load(['patient.user', 'schedule', 'medicalRecord']);

        return view('doctor.appointments.show', compact('appointment'));
    }

    /**
     * Confirm a pending appointment
     */
    public function confirm(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        // Ensure doctor can only confirm their own appointments
        if ($appointment->schedule->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Validate that appointment is pending
        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Only pending appointments can be confirmed.');
        }

        // Update appointment status
        $appointment->update([
            'status' => 'confirmed',
            'notes' => ($appointment->notes ?? '') . "\nConfirmed by doctor at " . now()->format('Y-m-d H:i:s'),
        ]);

        return back()->with('success', 'Appointment confirmed successfully!');
    }

    /**
     * Mark appointment as completed WITH medical record creation
     */
    public function complete(Request $request, Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        // Ensure doctor can only complete their own appointments
        if ($appointment->schedule->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Validate that appointment can be completed
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed appointments can be marked as completed.');
        }

        // Validate medical record data
        $request->validate([
            'diagnosis' => 'required|string|max:1000',
            'treatment' => 'nullable|string|max:2000',
            'prescription' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'medical_record_file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'diagnosis.required' => 'Diagnosis is required to complete the appointment.',
            'medical_record_file.required' => 'Medical record file is required to complete the appointment.',
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
            MedicalRecord::create([
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'doctor_id' => $doctor->id,
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

            // Update appointment status
            $appointment->update([
                'status' => 'completed',
                'notes' => ($appointment->notes ?? '') . "\nCompleted by doctor at " . now()->format('Y-m-d H:i:s'),
            ]);

            DB::commit();

            return back()->with('success', 'Appointment marked as completed and medical record saved!');
        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded file if transaction failed
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->with('error', 'Failed to complete appointment. Error: ' . $e->getMessage());
        }
    }

    /**
     * Mark appointment as no-show
     */
    public function noShow(Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        // Ensure doctor can only update their own appointments
        if ($appointment->schedule->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Validate that appointment can be marked as no-show
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed appointments can be marked as no-show.');
        }

        DB::beginTransaction();

        try {
            // Update appointment
            $appointment->update([
                'status' => 'no_show',
                'notes' => ($appointment->notes ?? '') . "\nMarked as no-show by doctor at " . now()->format('Y-m-d H:i:s'),
            ]);

            DB::commit();

            return back()->with('success', 'Appointment marked as no-show.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update appointment. Please try again.');
        }
    }

    /**
     * Cancel appointment with reason
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        $doctor = Auth::user()->doctor;

        // Ensure doctor can only cancel their own appointments
        if ($appointment->schedule->doctor_id !== $doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Validate cancellation
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed appointments can be cancelled.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Update appointment
            $appointment->update([
                'status' => 'cancelled',
                'notes' => ($appointment->notes ?? '') . "\nCancelled by doctor. Reason: " . $request->cancellation_reason . " (at " . now()->format('Y-m-d H:i:s') . ")",
            ]);

            // Decrement booked appointments count
            $appointment->schedule->decrementBookedAppointments();

            DB::commit();

            return redirect()->route('doctor.appointments.index')
                ->with('success', 'Appointment cancelled successfully. Patient will be notified.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel appointment. Please try again.');
        }
    }
}
