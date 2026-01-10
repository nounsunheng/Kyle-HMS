<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\Schedule;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard
     */
    public function index()
    {
        // Basic statistics
        $totalDoctors = Doctor::count();
        $totalPatients = Patient::count();
        $totalAppointments = Appointment::count();
        $totalSpecialties = Specialty::count();

        // Today's statistics
        $todayAppointments = Appointment::whereHas('schedule', function ($query) {
            $query->where('schedule_date', now()->toDateString());
        })->count();

        // This week's statistics
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        $weekAppointments = Appointment::whereHas('schedule', function ($query) use ($weekStart, $weekEnd) {
            $query->whereBetween('schedule_date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        })->count();

        // Recent appointments
        $recentAppointments = Appointment::with(['patient.user', 'schedule.doctor.user', 'schedule.doctor.specialty'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Appointment status breakdown
        $appointmentsByStatus = [
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
            'no_show' => Appointment::where('status', 'no_show')->count(),
        ];

        // Doctors by specialty
        $doctorsBySpecialty = Specialty::withCount('doctors')
            ->orderBy('doctors_count', 'desc')
            ->limit(5)
            ->get();

        // Available vs Unavailable doctors
        $availableDoctors = Doctor::where('is_available', true)->count();
        $unavailableDoctors = Doctor::where('is_available', false)->count();

        return view('admin.dashboard', compact(
            'totalDoctors',
            'totalPatients',
            'totalAppointments',
            'totalSpecialties',
            'todayAppointments',
            'weekAppointments',
            'recentAppointments',
            'appointmentsByStatus',
            'doctorsBySpecialty',
            'availableDoctors',
            'unavailableDoctors'
        ));
    }
}
