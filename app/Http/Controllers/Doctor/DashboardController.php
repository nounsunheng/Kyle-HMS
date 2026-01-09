<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display doctor dashboard
     */
    public function index()
    {
        $doctor = Auth::user()->doctor;

        // Today's appointments count
        $todayAppointments = Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->where('schedule_date', now()->toDateString());
        })->whereIn('status', ['pending', 'confirmed'])->count();

        // Total patients (unique)
        $totalPatients = Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })->distinct('patient_id')->count('patient_id');

        // Upcoming sessions
        $upcomingSessions = Schedule::where('doctor_id', $doctor->id)
            ->upcoming()
            ->count();

        // Today's schedule
        $todaySchedule = Schedule::with(['appointments.patient.user'])
            ->where('doctor_id', $doctor->id)
            ->where('schedule_date', now()->toDateString())
            ->first();

        // Recent appointments
        $recentAppointments = Appointment::with(['patient.user', 'schedule'])
            ->whereHas('schedule', function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('doctor.dashboard', compact(
            'todayAppointments',
            'totalPatients',
            'upcomingSessions',
            'todaySchedule',
            'recentAppointments'
        ));
    }
}
