<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Doctor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display enhanced patient dashboard with analytics
     */
    public function index()
    {
        $patient = Auth::user()->patient;

        // Cache expensive queries for 5 minutes
        $cacheKey = "patient_dashboard_{$patient->id}";

        $dashboardData = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($patient) {
            return $this->getDashboardData($patient);
        });

        return view('patient.dashboard', $dashboardData);
    }

    /**
     * Get comprehensive dashboard data
     */
    private function getDashboardData($patient)
    {
        // ===== STATISTICS =====
        $stats = [
            'upcoming_appointments' => $this->getUpcomingAppointmentsCount($patient),
            'total_appointments' => $patient->appointments()->count(),
            'completed_appointments' => $patient->appointments()->where('appointments.status', 'completed')->count(),
            'cancelled_appointments' => $patient->appointments()->where('appointments.status', 'cancelled')->count(),
            'total_records' => $patient->medicalRecords()->count(),
            'available_doctors' => Doctor::where('is_available', true)->count(),
        ];

        // ===== NEXT APPOINTMENT =====
        $nextAppointment = $this->getNextAppointment($patient);

        // ===== RECENT APPOINTMENTS =====
        $recentAppointments = $this->getRecentAppointments($patient);

        // ===== APPOINTMENT TRENDS (Last 6 months) =====
        $appointmentTrends = $this->getAppointmentTrends($patient);

        // ===== HEALTH METRICS =====
        $healthMetrics = $this->getHealthMetrics($patient);

        // ===== RECOMMENDED DOCTORS =====
        $recommendedDoctors = $this->getRecommendedDoctors($patient);

        // ===== UPCOMING APPOINTMENTS (Next 7 days) =====
        $upcomingWeek = $this->getUpcomingWeekAppointments($patient);

        // ===== QUICK STATS =====
        $quickStats = $this->getQuickStats($patient);

        return compact(
            'stats',
            'nextAppointment',
            'recentAppointments',
            'appointmentTrends',
            'healthMetrics',
            'recommendedDoctors',
            'upcomingWeek',
            'quickStats'
        );
    }

    /**
     * Get upcoming appointments count
     */
    private function getUpcomingAppointmentsCount($patient)
    {
        return Appointment::where('patient_id', $patient->id)
            ->whereHas('schedule', function($query) {
                $query->where('schedule_date', '>=', now()->toDateString());
            })
            ->whereIn('appointments.status', ['pending', 'confirmed'])
            ->count();
    }

    /**
     * Get next appointment with full details
     */
    private function getNextAppointment($patient)
    {
        return Appointment::where('patient_id', $patient->id)
            ->with([
                'schedule' => function($query) {
                    $query->select('id', 'doctor_id', 'schedule_date', 'start_time', 'end_time');
                },
                'schedule.doctor' => function($query) {
                    $query->select('id', 'user_id', 'specialty_id', 'qualifications', 'years_of_experience');
                },
                'schedule.doctor.user' => function($query) {
                    $query->select('id', 'name', 'email');
                },
                'schedule.doctor.specialty' => function($query) {
                    $query->select('id', 'name');
                }
            ])
            ->whereHas('schedule', function($query) {
                $query->where('schedule_date', '>=', now()->toDateString());
            })
            ->whereIn('appointments.status', ['pending', 'confirmed'])
            ->orderBy('appointments.created_at', 'asc')
            ->first();
    }

    /**
     * Get recent appointments
     */
    private function getRecentAppointments($patient)
    {
        return Appointment::where('patient_id', $patient->id)
            ->with([
                'schedule.doctor.user',
                'schedule.doctor.specialty'
            ])
            ->orderBy('appointments.created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get appointment trends for charts (last 6 months)
     * FIXED: Now properly aggregates data by joining with schedules table
     */
    private function getAppointmentTrends($patient)
    {
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        // Get all appointments with their schedule dates
        $trends = Appointment::where('patient_id', $patient->id)
            ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->where('schedules.schedule_date', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('DATE_FORMAT(schedules.schedule_date, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                'appointments.status'
            )
            ->groupBy('month', 'appointments.status')
            ->orderBy('month')
            ->get();

        // Initialize arrays for all 6 months
        $months = [];
        $completed = [];
        $cancelled = [];

        // Generate last 6 months labels and initialize data arrays
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            $monthName = $date->format('M Y');

            $months[] = $monthName;

            // Get counts for this month
            $completedCount = $trends->where('month', $month)
                                    ->where('status', 'completed')
                                    ->sum('count');

            $cancelledCount = $trends->where('month', $month)
                                     ->where('status', 'cancelled')
                                     ->sum('count');

            $completed[] = (int) $completedCount;
            $cancelled[] = (int) $cancelledCount;
        }

        return [
            'labels' => $months,
            'completed' => $completed,
            'cancelled' => $cancelled,
        ];
    }

    /**
     * Get health metrics
     */
    private function getHealthMetrics($patient)
    {
        $totalVisits = $patient->appointments()->count();
        $completedVisits = $patient->appointments()->where('appointments.status', 'completed')->count();
        $complianceRate = $totalVisits > 0 ? round(($completedVisits / $totalVisits) * 100) : 0;

        // Calculate average days between appointments
        $appointments = $patient->appointments()
            ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->orderBy('schedules.schedule_date')
            ->select('appointments.*', 'schedules.schedule_date')
            ->get();

        $avgDaysBetween = 0;
        if ($appointments->count() > 1) {
            $dates = $appointments->pluck('schedule_date')->toArray();
            $totalDays = 0;
            for ($i = 1; $i < count($dates); $i++) {
                $totalDays += Carbon::parse($dates[$i])->diffInDays(Carbon::parse($dates[$i-1]));
            }
            $avgDaysBetween = round($totalDays / (count($dates) - 1));
        }

        $lastVisit = $patient->appointments()
            ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->where('appointments.status', 'completed')
            ->orderBy('appointments.created_at', 'desc')
            ->select('schedules.schedule_date')
            ->first();

        return [
            'total_visits' => $totalVisits,
            'completed_visits' => $completedVisits,
            'compliance_rate' => $complianceRate,
            'avg_days_between' => $avgDaysBetween,
            'last_visit' => $lastVisit?->schedule_date,
        ];
    }

    /**
     * Get recommended doctors based on specialty visits
     */
    private function getRecommendedDoctors($patient)
    {
        // Get specialties the patient has visited most
        $topSpecialties = Appointment::where('patient_id', $patient->id)
            ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->join('doctors', 'schedules.doctor_id', '=', 'doctors.id')
            ->select('doctors.specialty_id', DB::raw('COUNT(*) as visit_count'))
            ->groupBy('doctors.specialty_id')
            ->orderBy('visit_count', 'desc')
            ->limit(3)
            ->pluck('specialty_id')
            ->toArray();

        if (empty($topSpecialties)) {
            // If no history, return random available doctors
            return Doctor::with(['user', 'specialty'])
                ->where('is_available', true)
                ->inRandomOrder()
                ->limit(4)
                ->get();
        }

        return Doctor::with(['user', 'specialty'])
            ->whereIn('specialty_id', $topSpecialties)
            ->where('is_available', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    /**
     * Get upcoming appointments for next 7 days
     */
    private function getUpcomingWeekAppointments($patient)
    {
        $today = now()->toDateString();
        $nextWeek = now()->addDays(7)->toDateString();

        return Appointment::where('patient_id', $patient->id)
            ->with(['schedule.doctor.user', 'schedule.doctor.specialty'])
            ->whereHas('schedule', function($query) use ($today, $nextWeek) {
                $query->whereBetween('schedule_date', [$today, $nextWeek]);
            })
            ->whereIn('appointments.status', ['pending', 'confirmed'])
            ->orderBy('appointments.created_at')
            ->get();
    }

    /**
     * Get quick stats for cards
     */
    private function getQuickStats($patient)
    {
        $thisMonth = now()->startOfMonth();

        $totalDoctorsVisited = Appointment::where('patient_id', $patient->id)
            ->join('schedules', 'appointments.schedule_id', '=', 'schedules.id')
            ->distinct('schedules.doctor_id')
            ->count('schedules.doctor_id');

        return [
            'this_month_appointments' => Appointment::where('patient_id', $patient->id)
                ->whereHas('schedule', function($query) use ($thisMonth) {
                    $query->where('schedule_date', '>=', $thisMonth);
                })
                ->count(),
            'pending_count' => Appointment::where('patient_id', $patient->id)
                ->where('appointments.status', 'pending')
                ->count(),
            'total_doctors_visited' => $totalDoctorsVisited,
        ];
    }
}
