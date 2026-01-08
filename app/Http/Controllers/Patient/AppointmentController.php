<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a listing of appointments
     */
    public function index()
    {
        $patient = Auth::user()->patient;

        $upcomingAppointments = Appointment::with(['schedule.doctor.user', 'schedule.doctor.specialty'])
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->whereHas('schedule', function ($query) {
                $query->where('schedule_date', '>=', now()->toDateString());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $pastAppointments = Appointment::with(['schedule.doctor.user', 'schedule.doctor.specialty'])
            ->where('patient_id', $patient->id)
            ->where(function ($query) {
                $query->whereIn('status', ['completed', 'cancelled', 'no_show'])
                      ->orWhereHas('schedule', function ($q) {
                          $q->where('schedule_date', '<', now()->toDateString());
                      });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('patient.appointments.index', compact('upcomingAppointments', 'pastAppointments'));
    }

    /**
     * Show the form for creating a new appointment
     */
    public function create(Request $request)
    {
        $scheduleId = $request->query('schedule');

        if (!$scheduleId) {
            return redirect()->route('patient.doctors.index')
                ->with('error', 'Please select a doctor schedule first.');
        }

        $schedule = Schedule::with(['doctor.user', 'doctor.specialty'])
            ->findOrFail($scheduleId);

        // Check if schedule is available
        if ($schedule->is_full) {
            return redirect()->back()
                ->with('error', 'This schedule is fully booked.');
        }

        // Generate available time slots
        $timeSlots = $this->generateTimeSlots($schedule);

        return view('patient.appointments.create', compact('schedule', 'timeSlots'));
    }

    /**
     * Store a newly created appointment
     */
    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'appointment_time' => 'required',
            'reason' => 'required|string|max:500',
        ]);

        $patient = Auth::user()->patient;
        $schedule = Schedule::findOrFail($request->schedule_id);

        // Check if schedule is still available
        if ($schedule->is_full) {
            return back()->with('error', 'This schedule is fully booked.');
        }

        // Check if time slot is already taken
        $existingAppointment = Appointment::where('schedule_id', $schedule->id)
            ->where('appointment_time', $request->appointment_time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($existingAppointment) {
            return back()->with('error', 'This time slot is already taken.');
        }

        DB::beginTransaction();

        try {
            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'schedule_id' => $schedule->id,
                'appointment_time' => $request->appointment_time,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            // Increment booked appointments count
            $schedule->incrementBookedAppointments();

            DB::commit();

            return redirect()->route('patient.appointments.show', $appointment)
                ->with('success', 'Appointment booked successfully! Your appointment number is: ' . $appointment->appointment_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to book appointment. Please try again.');
        }
    }

    /**
     * Display the specified appointment
     */
    public function show(Appointment $appointment)
    {
        // Ensure patient can only view their own appointments
        if ($appointment->patient_id !== Auth::user()->patient->id) {
            abort(403, 'Unauthorized access.');
        }

        $appointment->load(['schedule.doctor.user', 'schedule.doctor.specialty']);

        return view('patient.appointments.show', compact('appointment'));
    }

    /**
     * Cancel the specified appointment
     */
    public function destroy(Appointment $appointment)
    {
        // Ensure patient can only cancel their own appointments
        if ($appointment->patient_id !== Auth::user()->patient->id) {
            abort(403, 'Unauthorized access.');
        }

        // Check if appointment can be cancelled
        if (!$appointment->canBeCancelled()) {
            return back()->with('error', 'This appointment cannot be cancelled.');
        }

        DB::beginTransaction();

        try {
            // Update appointment status
            $appointment->update(['status' => 'cancelled']);

            // Decrement booked appointments count
            $appointment->schedule->decrementBookedAppointments();

            DB::commit();

            return redirect()->route('patient.appointments.index')
                ->with('success', 'Appointment cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel appointment. Please try again.');
        }
    }

    /**
     * Generate available time slots for a schedule
     */
    private function generateTimeSlots(Schedule $schedule)
    {
        $slots = [];
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $duration = $schedule->duration_per_appointment;

        // Get already booked time slots
        $bookedSlots = Appointment::where('schedule_id', $schedule->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->toArray();

        while ($startTime->copy()->addMinutes($duration) <= $endTime) {
            $slotTime = $startTime->format('H:i:s');

            $slots[] = [
                'time' => $slotTime,
                'formatted' => $startTime->format('g:i A'),
                'available' => !in_array($slotTime, $bookedSlots),
            ];

            $startTime->addMinutes($duration);
        }

        return $slots;
    }
}
