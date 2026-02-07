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

        // Auto-expire past schedules AND their pending/confirmed appointments
        $this->autoExpirePastSchedules($doctor);

        // Get upcoming schedules (today + future, but only if not expired)
        $upcomingSchedules = Schedule::with('appointments')
            ->where('doctor_id', $doctor->id)
            ->where('status', 'active')
            ->where(function ($query) {
                // Future dates
                $query->where('schedule_date', '>', now()->toDateString())
                    // OR today but end time hasn't passed
                    ->orWhere(function ($q) {
                        $q->where('schedule_date', '=', now()->toDateString())
                          ->where('end_time', '>', now()->format('H:i:s'));
                    });
            })
            ->orderBy('schedule_date')
            ->orderBy('start_time')
            ->get();

        // Get past schedules (expired or completed)
        $pastSchedules = Schedule::with('appointments')
            ->where('doctor_id', $doctor->id)
            ->where(function ($query) {
                // Past dates
                $query->where('schedule_date', '<', now()->toDateString())
                    // OR today but end time has passed
                    ->orWhere(function ($q) {
                        $q->where('schedule_date', '=', now()->toDateString())
                          ->where('end_time', '<=', now()->format('H:i:s'));
                    })
                    // OR any expired/cancelled status
                    ->orWhereIn('status', ['expired', 'cancelled']);
            })
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
            'schedule_date' => [
                'required',
                'date',
                'after_or_equal:' . now()->toDateString(),
            ],
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration_per_appointment' => 'required|integer|min:15|max:120',
        ], [
            'schedule_date.after_or_equal' => 'You cannot create a schedule for past dates. Please select today or a future date.',
        ]);

        $doctor = Auth::user()->doctor;

        // REAL WORLD VALIDATION: Cannot create schedule for past date/time
        $scheduleDate = Carbon::parse($request->schedule_date);
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);

        // If schedule is for today, check if times are in the future
        if ($scheduleDate->isToday()) {
            $now = now();
            $scheduleStartDateTime = Carbon::parse($request->schedule_date . ' ' . $request->start_time);
            $scheduleEndDateTime = Carbon::parse($request->schedule_date . ' ' . $request->end_time);

            if ($scheduleStartDateTime->isPast()) {
                return back()->withInput()->with('error', 'Cannot create schedule with past start time. Start time must be in the future.');
            }

            if ($scheduleEndDateTime->isPast()) {
                return back()->withInput()->with('error', 'Cannot create schedule with past end time.');
            }

            // Require at least 30 minutes from now to start
            // Calculate minutes from now to start time (must be at least 30)
            $minutesFromNow = $now->diffInMinutes($scheduleStartDateTime, false);

            if ($minutesFromNow < 30) {
                return back()->withInput()->with('error', 'Schedule must start at least 30 minutes from now.');
            }
        }

        // If schedule is in the past (somehow bypassed client validation)
        if ($scheduleDate->isPast() && !$scheduleDate->isToday()) {
            return back()->withInput()->with('error', 'You cannot create schedules for past dates.');
        }

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
        $startTimeCarbon = Carbon::parse($request->start_time);
        $endTimeCarbon = Carbon::parse($request->end_time);
        $durationMinutes = $request->duration_per_appointment;
        $totalMinutes = $startTimeCarbon->diffInMinutes($endTimeCarbon);
        $maxAppointments = floor($totalMinutes / $durationMinutes);

        if ($maxAppointments < 1) {
            return back()->withInput()->with('error', 'Time range too short. Please increase the duration or select a longer time range.');
        }

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

        // Auto-mark no-shows for past appointments
        $this->autoMarkNoShows($schedule);

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

        // REAL WORLD: Don't allow editing past schedules or schedules that have already started
        if ($schedule->schedule_date->isPast()) {
            return redirect()->route('doctor.schedule.show', $schedule)
                ->with('error', 'Cannot edit past schedules.');
        }

        // If today, check if start time has passed
        if ($schedule->schedule_date->isToday()) {
            $scheduleStartDateTime = Carbon::parse($schedule->schedule_date->format('Y-m-d') . ' ' . $schedule->start_time);
            if ($scheduleStartDateTime->isPast()) {
                return redirect()->route('doctor.schedule.show', $schedule)
                    ->with('error', 'Cannot edit schedule that has already started.');
            }
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

        // Don't allow updating past schedules
        if ($schedule->schedule_date->isPast()) {
            return back()->with('error', 'Cannot update past schedules.');
        }

        $request->validate([
            'schedule_date' => [
                'required',
                'date',
                'after_or_equal:' . now()->toDateString(),
            ],
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'duration_per_appointment' => 'required|integer|min:15|max:120',
        ], [
            'schedule_date.after_or_equal' => 'You cannot schedule for past dates.',
        ]);

        // REAL WORLD: Validate time if updating to today
        $scheduleDate = Carbon::parse($request->schedule_date);
        if ($scheduleDate->isToday()) {
            $scheduleStartDateTime = Carbon::parse($request->schedule_date . ' ' . $request->start_time);
            if ($scheduleStartDateTime->isPast()) {
                return back()->withInput()->with('error', 'Cannot set schedule with past start time.');
            }
        }

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
                ->update([
                    'status' => 'cancelled',
                    'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\nSchedule cancelled by doctor at " . now()->format('Y-m-d H:i:s') . "')")
                ]);

            DB::commit();

            return redirect()->route('doctor.schedule.index')
                ->with('success', 'Schedule cancelled successfully. All appointments have been cancelled.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to cancel schedule. Please try again.');
        }
    }

    /**
     * Auto-expire past schedules AND their appointments (REAL WORLD LOGIC)
     * Expires schedules from yesterday AND today's schedules whose end time has passed
     * Also expires all pending/confirmed appointments in expired schedules
     */
    private function autoExpirePastSchedules($doctor)
    {
        $now = now();
        $today = $now->toDateString();
        $yesterday = $now->subDay()->toDateString();

        DB::beginTransaction();

        try {
            // Find schedules to expire (past dates + today's ended schedules)
            $schedulesToExpire = Schedule::where('doctor_id', $doctor->id)
                ->where('status', 'active')
                ->where(function ($query) use ($yesterday, $today) {
                    // Past dates (before yesterday)
                    $query->where('schedule_date', '<', $yesterday)
                        // OR yesterday
                        ->orWhere('schedule_date', '=', $yesterday);
                })
                ->get();

            foreach ($schedulesToExpire as $schedule) {
                // Expire the schedule
                $schedule->update(['status' => 'expired']);

                // Expire all pending/confirmed appointments in this schedule
                $schedule->appointments()
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->update([
                        'status' => 'expired',
                        'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\nAuto-expired (schedule appointment expired) at " . now()->format('Y-m-d H:i:s') . "')")
                    ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Auto-expire schedules failed: ' . $e->getMessage());
        }
    }

    /**
     * Auto-mark no-shows for appointments that weren't completed
     * REAL WORLD: Mark as no-show if appointment time + 1 hour has passed
     */
    private function autoMarkNoShows($schedule)
    {
        // Only process if schedule date has passed OR if it's today and past the schedule end time
        $now = now();
        $scheduleDate = $schedule->schedule_date;

        // Don't process future schedules
        if ($scheduleDate->isFuture()) {
            return;
        }

        // For today's schedule, only mark no-shows for appointments whose time + 1 hour has passed
        if ($scheduleDate->isToday()) {
            $schedule->appointments()
                ->whereIn('status', ['pending', 'confirmed'])
                ->each(function ($appointment) use ($now) {
                    $appointmentTime = Carbon::parse($appointment->schedule->schedule_date->format('Y-m-d') . ' ' . $appointment->appointment_time);

                    // Mark as no-show if appointment time + 1 hour has passed
                    if ($appointmentTime->copy()->addHour()->isPast()) {
                        $appointment->update([
                            'status' => 'no_show',
                            'notes' => ($appointment->notes ?? '') . "\nAuto-marked as no-show (patient did not show up)"
                        ]);
                    }
                });
        } else {
            // For past dates, mark all pending/confirmed as no-show
            $schedule->appointments()
                ->whereIn('status', ['pending', 'confirmed'])
                ->update([
                    'status' => 'no_show',
                    'notes' => DB::raw("CONCAT(COALESCE(notes, ''), '\nAuto-marked as no-show (patient did not show up)')")
                ]);
        }
    }
}
