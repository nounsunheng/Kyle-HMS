<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Schedule;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;

        // Clear any cache
        \Cache::forget("doctor_dashboard_{$doctor->id}");

        // Get fresh data - NO CACHING
        $dashboardData = $this->getDashboardData($doctor);

        // Disable browser cache
        return response()
            ->view('doctor.dashboard', $dashboardData)
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function getDashboardData($doctor)
    {
        // Get current date/time - USE SERVER TIME
        $now = Carbon::now();
        $today = $now->toDateString(); // YYYY-MM-DD format

        \Log::info('Dashboard Loading', [
            'doctor_id' => $doctor->id,
            'server_time' => $now->toDateTimeString(),
            'today_date' => $today,
        ]);

        // ===== STATISTICS =====
        $stats = [
            'today_appointments' => $this->getTodayAppointmentsCount($doctor, $today),
            'total_patients' => $this->getTotalPatientsCount($doctor),
            'upcoming_sessions' => $this->getUpcomingSessionsCount($doctor, $today),
            'completed_appointments' => $this->getCompletedAppointmentsCount($doctor),
            'this_week_appointments' => $this->getThisWeekAppointmentsCount($doctor),
            'total_medical_records' => $doctor->medicalRecords()->count(),
        ];

        // ===== TODAY'S SCHEDULE =====
        // Get ALL schedules for today first to see what we have
        $allTodaySchedules = Schedule::where('doctor_id', $doctor->id)
            ->where('schedule_date', $today)
            ->get();

        \Log::info('All Today Schedules', [
            'count' => $allTodaySchedules->count(),
            'schedules' => $allTodaySchedules->map(function($s) {
                return [
                    'id' => $s->id,
                    'date' => $s->schedule_date,
                    'time' => $s->start_time . ' - ' . $s->end_time,
                    'status' => $s->status,
                ];
            })->toArray()
        ]);

        // Get today's ACTIVE schedule
        $todaySchedule = Schedule::with(['appointments.patient.user'])
            ->where('doctor_id', $doctor->id)
            ->where('schedule_date', $today)
            ->where('status', 'active')
            ->orderBy('start_time', 'asc')
            ->first();

        \Log::info('Today Schedule Result', [
            'found' => $todaySchedule ? 'YES' : 'NO',
            'schedule' => $todaySchedule ? [
                'id' => $todaySchedule->id,
                'date' => $todaySchedule->schedule_date->format('Y-m-d'),
                'time' => $todaySchedule->start_time . ' - ' . $todaySchedule->end_time,
                'status' => $todaySchedule->status,
            ] : null
        ]);

        // ===== RECENT APPOINTMENTS =====
        $recentAppointments = Appointment::with(['patient.user', 'schedule'])
            ->whereHas('schedule', function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // ===== APPOINTMENT TRENDS =====
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

    private function getTodayAppointmentsCount($doctor, $today)
    {
        return Appointment::whereHas('schedule', function ($query) use ($doctor, $today) {
            $query->where('doctor_id', $doctor->id)
                  ->where('schedule_date', $today);
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

    private function getUpcomingSessionsCount($doctor, $today)
    {
        return Schedule::where('doctor_id', $doctor->id)
            ->where('status', 'active')
            ->where('schedule_date', '>=', $today)
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
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        return Appointment::whereHas('schedule', function ($query) use ($doctor, $weekStart, $weekEnd) {
            $query->where('doctor_id', $doctor->id)
                  ->whereBetween('schedule_date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        })->count();
    }

    /**
     * Get appointment trends for charts (last 6 months)
     * FIXED: Now properly aggregates data by joining with schedules table
     */
    private function getAppointmentTrends($doctor)
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        // Get appointments with schedule dates for proper monthly grouping
        $trends = Appointment::join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->where('schedules.doctor_id', $doctor->id)
            ->where('schedules.schedule_date', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('DATE_FORMAT(schedules.schedule_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                'appointments.status'
            )
            ->groupBy('month', 'appointments.status')
            ->orderBy('month')
            ->get();

        // Initialize arrays
        $months = [];
        $completed = [];
        $cancelled = [];
        $pending = [];

        // Always generate 6 months of data
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('Y-m');
            $monthName = $date->format('M Y');

            $months[] = $monthName;

            // Get counts for each status for this month
            $completedCount = $trends->where('month', $month)
                                    ->where('status', 'completed')
                                    ->sum('count');

            $cancelledCount = $trends->where('month', $month)
                                     ->where('status', 'cancelled')
                                     ->sum('count');

            $pendingCount = $trends->where('month', $month)
                                   ->whereIn('status', ['pending', 'confirmed'])
                                   ->sum('count');

            $completed[] = (int) $completedCount;
            $cancelled[] = (int) $cancelledCount;
            $pending[] = (int) $pendingCount;
        }

        // Log for debugging
        \Log::info('Appointment Trends Generated', [
            'doctor_id' => $doctor->id,
            'months' => $months,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'pending' => $pending,
            'has_data' => array_sum($completed) + array_sum($cancelled) + array_sum($pending) > 0
        ]);

        // Always return data structure (even if all zeros)
        return [
            'labels' => $months,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'pending' => $pending,
        ];
    }

    private function getUpcomingWeekAppointments($doctor)
    {
        $today = Carbon::now()->toDateString();
        $nextWeek = Carbon::now()->addDays(7)->toDateString();

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
