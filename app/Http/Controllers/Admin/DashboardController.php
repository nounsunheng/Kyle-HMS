<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\Schedule;
use App\Models\MedicalRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ===== BASIC STATISTICS =====
        $stats = [
            'total_doctors' => Doctor::count(),
            'total_patients' => Patient::count(),
            'total_appointments' => Appointment::count(),
            'total_specialties' => Specialty::count(),
            'today_appointments' => $this->getTodayAppointments(),
            'week_appointments' => $this->getWeekAppointments(),
            'month_appointments' => $this->getMonthAppointments(),
            'available_doctors' => Doctor::where('is_available', true)->count(),
            'unavailable_doctors' => Doctor::where('is_available', false)->count(),
            'total_medical_records' => MedicalRecord::count(),
        ];

        // ===== RECENT APPOINTMENTS =====
        $recentAppointments = Appointment::with(['patient.user', 'schedule.doctor.user', 'schedule.doctor.specialty'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // ===== APPOINTMENT STATUS BREAKDOWN =====
        $appointmentsByStatus = [
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
            'no_show' => Appointment::where('status', 'no_show')->count(),
        ];

        // ===== DOCTORS BY SPECIALTY =====
        $doctorsBySpecialty = Specialty::withCount('doctors')
            ->orderBy('doctors_count', 'desc')
            ->limit(8)
            ->get();

        // ===== MONTHLY TRENDS (Last 6 Months) =====
        $monthlyTrends = $this->getMonthlyTrends();

        // ===== REGISTRATION TRENDS =====
        $registrationTrends = $this->getRegistrationTrends();

        // ===== TOP PERFORMING DOCTORS (FIXED) =====
        $topDoctors = $this->getTopDoctors();

        // ===== SYSTEM HEALTH =====
        $systemHealth = $this->getSystemHealth();

        return view('admin.dashboard', compact(
            'stats',
            'recentAppointments',
            'appointmentsByStatus',
            'doctorsBySpecialty',
            'monthlyTrends',
            'registrationTrends',
            'topDoctors',
            'systemHealth'
        ));
    }

    private function getTodayAppointments()
    {
        return Appointment::whereHas('schedule', function ($query) {
            $query->where('schedule_date', now()->toDateString());
        })->count();
    }

    private function getWeekAppointments()
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        return Appointment::whereHas('schedule', function ($query) use ($weekStart, $weekEnd) {
            $query->whereBetween('schedule_date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        })->count();
    }

    private function getMonthAppointments()
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        return Appointment::whereHas('schedule', function ($query) use ($monthStart, $monthEnd) {
            $query->whereBetween('schedule_date', [$monthStart->toDateString(), $monthEnd->toDateString()]);
        })->count();
    }

    private function getMonthlyTrends()
    {
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        $trends = Appointment::whereHas('schedule', function ($query) use ($sixMonthsAgo) {
            $query->where('schedule_date', '>=', $sixMonthsAgo);
        })
        ->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count'),
            'status'
        )
        ->groupBy('month', 'status')
        ->orderBy('month')
        ->get();

        $months = [];
        $completed = [];
        $cancelled = [];
        $pending = [];
        $total = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthName = now()->subMonths($i)->format('M Y');
            $months[] = $monthName;

            $completedCount = $trends->where('month', $month)->where('status', 'completed')->sum('count');
            $cancelledCount = $trends->where('month', $month)->where('status', 'cancelled')->sum('count');
            $pendingCount = $trends->where('month', $month)->whereIn('status', ['pending', 'confirmed'])->sum('count');

            $completed[] = $completedCount;
            $cancelled[] = $cancelledCount;
            $pending[] = $pendingCount;
            $total[] = $completedCount + $cancelledCount + $pendingCount;
        }

        return [
            'labels' => $months,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'pending' => $pending,
            'total' => $total,
        ];
    }

    private function getRegistrationTrends()
    {
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        $patients = Patient::where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $doctors = Doctor::where('created_at', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = [];
        $patientData = [];
        $doctorData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->format('Y-m');
            $monthName = now()->subMonths($i)->format('M Y');
            $months[] = $monthName;

            $patientData[] = $patients->where('month', $month)->sum('count');
            $doctorData[] = $doctors->where('month', $month)->sum('count');
        }

        return [
            'labels' => $months,
            'patients' => $patientData,
            'doctors' => $doctorData,
        ];
    }

    /**
     * FIXED: Get top performing doctors
     * The issue was ambiguous 'status' column - both appointments and schedules have it
     * Solution: Explicitly specify table name with appointments.status
     */
    private function getTopDoctors()
    {
        return Doctor::withCount(['appointments as completed_count' => function ($query) {
            // FIX: Explicitly specify table name to avoid ambiguity
            $query->where('appointments.status', 'completed');
        }])
        ->with(['user', 'specialty'])
        ->orderBy('completed_count', 'desc')
        ->limit(5)
        ->get();
    }

    private function getSystemHealth()
    {
        $totalAppointments = Appointment::count();
        $completedAppointments = Appointment::where('status', 'completed')->count();
        $cancelledAppointments = Appointment::where('status', 'cancelled')->count();

        $completionRate = $totalAppointments > 0
            ? round(($completedAppointments / $totalAppointments) * 100, 1)
            : 0;

        $cancellationRate = $totalAppointments > 0
            ? round(($cancelledAppointments / $totalAppointments) * 100, 1)
            : 0;

        // Calculate system utilization
        $totalScheduleSlots = Schedule::where('status', 'active')->sum('max_appointments');
        $bookedSlots = Schedule::where('status', 'active')->sum('booked_appointments');

        $utilizationRate = $totalScheduleSlots > 0
            ? round(($bookedSlots / $totalScheduleSlots) * 100, 1)
            : 0;

        return [
            'completion_rate' => $completionRate,
            'cancellation_rate' => $cancellationRate,
            'utilization_rate' => $utilizationRate,
            'active_doctors' => Doctor::where('is_available', true)->count(),
            'total_doctors' => Doctor::count(),
        ];
    }
}
