<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        // Cache dashboard data for 5 minutes to improve performance
        $cacheKey = "doctor_dashboard_{$doctor->id}";

        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($doctor) {
            return $this->getDashboardData($doctor);
        });

        return view('doctor.dashboard', $dashboardData);
    }

    private function getDashboardData($doctor)
    {
        // ===== STATISTICS =====
        $stats = [
            'today_appointments' => $this->getTodayAppointmentsCount($doctor),
            'total_patients' => $this->getTotalPatientsCount($doctor),
            'upcoming_sessions' => $this->getUpcomingSessionsCount($doctor),
            'completed_appointments' => $this->getCompletedAppointmentsCount($doctor),
            'this_week_appointments' => $this->getThisWeekAppointmentsCount($doctor),
            'total_medical_records' => $doctor->medicalRecords()->count(),
        ];

        // ===== TODAY'S SCHEDULE =====
        $todaySchedule = Schedule::with(['appointments.patient.user'])
            ->where('doctor_id', $doctor->id)
            ->where('schedule_date', now()->toDateString())
            ->where('status', 'active')
            ->first();

        // ===== RECENT APPOINTMENTS =====
        $recentAppointments = Appointment::with(['patient.user', 'schedule'])
            ->whereHas('schedule', function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // ===== APPOINTMENT TRENDS (Last 6 months) =====
        $appointmentTrends = $this->getAppointmentTrends($doctor);

        // ===== UPCOMING THIS WEEK =====
        $upcomingThisWeek = $this->getUpcomingWeekAppointments($doctor);

        // ===== PERFORMANCE METRICS =====
        $performanceMetrics = $this->getPerformanceMetrics($doctor);

        return compact(
            'stats',
            'todaySchedule',
            'recentAppointments',
            'appointmentTrends',
            'upcomingThisWeek',
            'performanceMetrics'
        );
    }

    private function getTodayAppointmentsCount($doctor)
    {
        return Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->where('schedule_date', now()->toDateString());
        })
        ->whereIn('appointments.status', ['pending', 'confirmed'])
        ->count();
    }

    private function getTotalPatientsCount($doctor)
    {
        return Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })
        ->distinct('patient_id')
        ->count('patient_id');
    }

    private function getUpcomingSessionsCount($doctor)
    {
        return Schedule::where('doctor_id', $doctor->id)
            ->where('schedule_date', '>=', now()->toDateString())
            ->where('status', 'active')
            ->count();
    }

    private function getCompletedAppointmentsCount($doctor)
    {
        return Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })
        ->where('appointments.status', 'completed')
        ->count();
    }

    private function getThisWeekAppointmentsCount($doctor)
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        return Appointment::whereHas('schedule', function ($query) use ($doctor, $weekStart, $weekEnd) {
            $query->where('doctor_id', $doctor->id)
                  ->whereBetween('schedule_date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        })->count();
    }

    private function getAppointmentTrends($doctor)
    {
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        $trends = Appointment::whereHas('schedule', function ($query) use ($doctor, $sixMonthsAgo) {
            $query->where('doctor_id', $doctor->id)
                  ->where('schedule_date', '>=', $sixMonthsAgo);
        })
        ->select(
            DB::raw('DATE_FORMAT(appointments.created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count'),
            'appointments.status'
        )
        ->groupBy('month', 'appointments.status')
        ->orderBy('month')
        ->get();

        $months = [];
        $completed = [];
        $cancelled = [];
        $pending = [];

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
        }

        return [
            'labels' => $months,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'pending' => $pending,
        ];
    }

    private function getUpcomingWeekAppointments($doctor)
    {
        $today = now()->toDateString();
        $nextWeek = now()->addDays(7)->toDateString();

        return Appointment::with(['patient.user', 'schedule'])
            ->whereHas('schedule', function ($query) use ($doctor, $today, $nextWeek) {
                $query->where('doctor_id', $doctor->id)
                      ->whereBetween('schedule_date', [$today, $nextWeek]);
            })
            ->whereIn('appointments.status', ['pending', 'confirmed'])
            ->orderBy('appointments.created_at')
            ->get();
    }

    private function getPerformanceMetrics($doctor)
    {
        $totalAppointments = Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })->count();

        $completedAppointments = Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })
        ->where('appointments.status', 'completed')
        ->count();

        $completionRate = $totalAppointments > 0
            ? round(($completedAppointments / $totalAppointments) * 100)
            : 0;

        $avgAppointmentsPerDay = Schedule::where('doctor_id', $doctor->id)
            ->where('schedules.status', 'completed')
            ->avg('booked_appointments');

        $cancelledAppointments = Appointment::whereHas('schedule', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })
        ->where('appointments.status', 'cancelled')
        ->count();

        return [
            'total_appointments' => $totalAppointments,
            'completion_rate' => $completionRate,
            'avg_per_day' => round($avgAppointmentsPerDay ?? 0, 1),
            'cancellation_rate' => $totalAppointments > 0
                ? round(($cancelledAppointments / $totalAppointments) * 100)
                : 0,
        ];
    }
}
