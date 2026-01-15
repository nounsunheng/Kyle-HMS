<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
     * Update appointment status
     */
    public function updateStatus(Request $request, Appointment $appointment)
    {
        // Ensure doctor can only update their own appointments
        if ($appointment->schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'status' => 'required|in:confirmed,completed,no_show',
        ]);

        $appointment->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Appointment status updated successfully!');
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
                'notes' => 'Cancelled by doctor. Reason: ' . $request->cancellation_reason,
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
