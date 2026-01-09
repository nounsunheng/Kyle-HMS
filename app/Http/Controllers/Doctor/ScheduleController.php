<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display a listing of schedules
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;

        $upcomingSchedules = Schedule::with('appointments')
            ->where('doctor_id', $doctor->id)
            ->upcoming()
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->get();

        $pastSchedules = Schedule::with('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('schedule_date', '<', now()->toDateString())
            ->orderBy('schedule_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->limit(10)
            ->get();

        return view('doctor.schedule.index', compact('upcomingSchedules', 'pastSchedules'));
    }

    /**
     * Show the form for creating a new schedule
     */
    public function create()
    {
        return view('doctor.schedule.create');
    }

    /**
     * Store a newly created schedule
     */
    public function store(Request $request)
    {
        $request->validate([
            'schedule_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration_per_appointment' => 'required|integer|min:15|max:120',
        ]);

        $doctor = Auth::user()->doctor;

        // Check for conflicting schedules
        $conflict = Schedule::where('doctor_id', $doctor->id)
            ->where('schedule_date', $request->schedule_date)
            ->where('status', 'active')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'You already have a schedule that conflicts with this time slot.');
        }

        // Calculate max appointments based on duration
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $durationMinutes = $request->duration_per_appointment;
        $totalMinutes = $startTime->diffInMinutes($endTime);
        $maxAppointments = floor($totalMinutes / $durationMinutes);

        Schedule::create([
            'doctor_id' => $doctor->id,
            'schedule_date' => $request->schedule_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_per_appointment' => $durationMinutes,
            'max_appointments' => $maxAppointments,
            'booked_appointments' => 0,
            'status' => 'active',
        ]);

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule created successfully!');
    }

    /**
     * Display the specified schedule
     */
    public function show(Schedule $schedule)
    {
        // Ensure doctor can only view their own schedules
        if ($schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        $schedule->load(['appointments.patient.user']);

        return view('doctor.schedule.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified schedule
     */
    public function edit(Schedule $schedule)
    {
        // Ensure doctor can only edit their own schedules
        if ($schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Don't allow editing if there are confirmed appointments
        if ($schedule->booked_appointments > 0) {
            return redirect()->route('doctor.schedule.show', $schedule)
                ->with('error', 'Cannot edit schedule with existing appointments.');
        }

        return view('doctor.schedule.edit', compact('schedule'));
    }

    /**
     * Update the specified schedule
     */
    public function update(Request $request, Schedule $schedule)
    {
        // Ensure doctor can only update their own schedules
        if ($schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Don't allow updating if there are booked appointments
        if ($schedule->booked_appointments > 0) {
            return back()->with('error', 'Cannot update schedule with existing appointments.');
        }

        $request->validate([
            'schedule_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration_per_appointment' => 'required|integer|min:15|max:120',
        ]);

        // Calculate max appointments
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $durationMinutes = $request->duration_per_appointment;
        $totalMinutes = $startTime->diffInMinutes($endTime);
        $maxAppointments = floor($totalMinutes / $durationMinutes);

        $schedule->update([
            'schedule_date' => $request->schedule_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_per_appointment' => $durationMinutes,
            'max_appointments' => $maxAppointments,
        ]);

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule updated successfully!');
    }

    /**
     * Remove the specified schedule
     */
    public function destroy(Schedule $schedule)
    {
        // Ensure doctor can only delete their own schedules
        if ($schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        // Don't allow deletion if there are booked appointments
        if ($schedule->booked_appointments > 0) {
            return back()->with('error', 'Cannot delete schedule with existing appointments. Please cancel the schedule instead.');
        }

        $schedule->delete();

        return redirect()->route('doctor.schedule.index')
            ->with('success', 'Schedule deleted successfully!');
    }

    /**
     * Cancel the specified schedule
     */
    public function cancel(Schedule $schedule)
    {
        // Ensure doctor can only cancel their own schedules
        if ($schedule->doctor_id !== Auth::user()->doctor->id) {
            abort(403, 'Unauthorized access.');
        }

        DB::beginTransaction();

        try {
            // Update schedule status
            $schedule->update(['status' => 'cancelled']);

            // Cancel all pending/confirmed appointments
            $schedule->appointments()
                ->whereIn('status', ['pending', 'confirmed'])
                ->update(['status' => 'cancelled']);

            DB::commit();

            return redirect()->route('doctor.schedule.index')
                ->with('success', 'Schedule cancelled successfully. All appointments have been cancelled.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel schedule. Please try again.');
        }
    }
}
