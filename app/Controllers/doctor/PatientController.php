<?php

namespace App\Controllers\Doctor;

use App\Core\Controller;
use App\Repositories\PatientRepository;
use App\Repositories\AppointmentRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

class PatientController extends Controller
{
    public function __construct(
        private PatientRepository $patientRepo,
        private AppointmentRepository $appointmentRepo
    ) {
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['d']))->handle();
    }

    public function index(): void
    {
        $doctorId = userId();
        $search = request('search');

        $appointments = $this->appointmentRepo
            ->getDoctorAppointments($doctorId);

        $patientIds = array_unique(array_column($appointments, 'pid'));

        $patients = $this->patientRepo
            ->getMultipleWithStatistics($patientIds);

        if ($search) {
            $patients = array_filter($patients, fn ($p) =>
                stripos($p['pname'], $search) !== false ||
                stripos($p['pemail'], $search) !== false
            );
        }

        $this->view('doctor.patients', [
            'patients' => $patients,
            'search_term' => $search
        ]);
    }

    public function view(): void
    {
        $patientId = request('id');

        if (!$patientId) {
            flash('Patient not found', 'error');
            redirect('/doctor/patients.php');
        }

        $patient = $this->patientRepo
            ->getWithStatistics($patientId);

        if (!$patient) {
            flash('Patient not found', 'error');
            redirect('/doctor/patients.php');
        }

        $appointments = $this->appointmentRepo
            ->getPatientAppointmentsForDoctor(userId(), $patientId);

        $this->view('doctor.patient-detail', [
            'patient' => $patient,
            'appointments' => $appointments
        ]);
    }
}
