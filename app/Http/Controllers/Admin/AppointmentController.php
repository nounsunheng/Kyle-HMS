<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.user', 'schedule.doctor.user', 'schedule.doctor.specialty']);

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

        // Search by patient or doctor name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->whereHas('patient.user', function ($pq) use ($request) {
                    $pq->where('name', 'like', '%' . $request->search . '%');
                })->orWhereHas('schedule.doctor.user', function ($dq) use ($request) {
                    $dq->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }

        $appointments = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.appointments.index', compact('appointments'));
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['patient.user', 'schedule.doctor.user', 'schedule.doctor.specialty', 'medicalRecord']);
        return view('admin.appointments.show', compact('appointment'));
    }

    /**
     * Cancel the specified appointment
     */
    public function cancel(Appointment $appointment)
    {
        if (!in_array($appointment->status, ['pending', 'confirmed'])) {
            return back()->with('error', 'Only pending or confirmed appointments can be cancelled.');
        }

        DB::beginTransaction();

        try {
            $appointment->update(['status' => 'cancelled']);
            $appointment->schedule->decrementBookedAppointments();

            DB::commit();

            return back()->with('success', 'Appointment cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel appointment. Please try again.');
        }
    }
}
