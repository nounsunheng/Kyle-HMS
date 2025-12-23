<?php

namespace App\Controllers\Doctor;

use App\Core\Controller;
use App\Services\AppointmentService;
use App\Services\ScheduleService;
use App\Repositories\DoctorRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class DashboardController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService,
        private ScheduleService $scheduleService,
        private DoctorRepository $doctorRepo
    ) {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['d']))->handle();
    }

    public function index(): void
    {
        $doctorId = userId();

        $doctor = $this->doctorRepo->getWithStatistics($doctorId);

        $todayAppointments = $this->appointmentService
            ->getDoctorAppointments($doctorId, date('Y-m-d'));

        $upcomingAppointments = $this->appointmentService
            ->getUpcomingAppointments($doctorId, 7);

        $upcomingSchedules = $this->scheduleService
            ->getUpcomingDoctorSchedules($doctorId, 5);

        $data = [
            'doctor' => $doctor,
            'today_appointments' => $todayAppointments,
            'upcoming_appointments' => $upcomingAppointments,
            'upcoming_schedules' => $upcomingSchedules,
            'stats' => [
                'total_appointments' => $doctor['total_appointments'] ?? 0,
                'completed' => $doctor['completed_appointments'] ?? 0,
                'pending' => $doctor['pending_appointments'] ?? 0,
                'confirmed' => $doctor['confirmed_appointments'] ?? 0,
                'total_patients' => $doctor['total_patients'] ?? 0,
                'total_schedules' => $doctor['total_schedules'] ?? 0,
            ]
        ];

        $this->view('doctor.dashboard', $data);
    }
}
