<?php

namespace App\Controllers\Doctor;

use App\Core\Controller;
use App\Services\AppointmentService;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $appointmentService
    ) {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['d']))->handle();
    }

    public function index(): void
    {
        $doctorId = userId();

        $appointments = $this->appointmentService->getDoctorAppointments(
            $doctorId,
            request('date'),
            request('status')
        );

        $data = [
            'appointments' => $appointments,
            'selected_date' => request('date'),
            'selected_status' => request('status'),
        ];

        $this->view('doctor.appointments', $data);
    }

    public function updateStatus(): void
    {
        (new CsrfMiddleware())->handle();

        $result = $this->appointmentService->updateStatus(
            request('appointment_id'),
            request('status')
        );

        flash(
            $result['message'],
            $result['success'] ? 'success' : 'error'
        );

        redirect('/doctor/appointments.php');
    }

    public function cancel(): void
    {
        (new CsrfMiddleware())->handle();

        $result = $this->appointmentService->cancelAppointment(
            request('appointment_id'),
            request('reason') ?? 'Cancelled by doctor',
            'doctor'
        );

        flash(
            $result['success']
                ? 'Appointment cancelled successfully'
                : $result['message'],
            $result['success'] ? 'success' : 'error'
        );

        redirect('/doctor/appointments.php');
    }
}
