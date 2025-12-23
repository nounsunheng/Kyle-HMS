<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers\Patient;

use App\Core\Controller;
use App\Services\AppointmentService;
use App\Services\ScheduleService;
use App\Services\ValidationService;
use App\Repositories\DoctorRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

/**
 * Patient Appointment Controller
 * Handles appointment booking and management
 */
class AppointmentController extends Controller {
    
    private AppointmentService $appointmentService;
    private ScheduleService $scheduleService;
    private ValidationService $validator;
    private DoctorRepository $doctorRepo;
    
    public function __construct() {
        $this->appointmentService = new AppointmentService();
        $this->scheduleService = new ScheduleService();
        $this->validator = new ValidationService();
        $this->doctorRepo = new DoctorRepository();
    }
    
    /**
     * Show booking form
     */
    public function bookingForm(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        $doctorId = $_GET['doctor_id'] ?? null;
        
        // Get available schedules
        $schedules = $this->scheduleService->getAvailableSchedules($doctorId);
        
        // Get doctor info if specified
        $doctor = null;
        if ($doctorId) {
            $doctor = $this->doctorRepo->getWithStatistics($doctorId);
        }
        
        // Get all doctors for selection
        $doctors = $this->doctorRepo->getAllWithSpecialties();
        
        $data = [
            'schedules' => $schedules,
            'doctor' => $doctor,
            'doctors' => $doctors,
            'selected_doctor_id' => $doctorId
        ];
        
        $this->view('patient.book-appointment', $data);
    }
    
    /**
     * Process appointment booking
     */
    public function book(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        // Validate CSRF
        (new CsrfMiddleware())->handle();
        
        // Validate input
        $errors = $this->validator->validateAppointmentBooking($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/patient/book-appointment.php');
        }
        
        // Book appointment
        $result = $this->appointmentService->bookAppointment([
            'patient_id' => userId(),
            'schedule_id' => $_POST['schedule_id'],
            'symptoms' => $_POST['symptoms'] ?? null,
            'notes' => $_POST['notes'] ?? null
        ]);
        
        if ($result['success']) {
            flash(
                "Appointment booked successfully! Reference: {$result['appointment_number']}", 
                'success'
            );
            $this->redirect('/patient/appointments.php');
        } else {
            flash($result['message'], 'error');
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/patient/book-appointment.php');
        }
    }
    
    /**
     * Show appointments list
     */
    public function list(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        $patientId = userId();
        
        // Get filter
        $filter = $_GET['filter'] ?? 'all';
        
        // Get appointments
        $appointments = match($filter) {
            'upcoming' => $this->appointmentService->getPatientAppointments($patientId, 'confirmed'),
            'completed' => $this->appointmentService->getPatientAppointments($patientId, 'completed'),
            'cancelled' => $this->appointmentService->getPatientAppointments($patientId, 'cancelled'),
            default => $this->appointmentService->getPatientAppointments($patientId)
        };
        
        $data = [
            'appointments' => $appointments,
            'filter' => $filter
        ];
        
        $this->view('patient.appointments', $data);
    }
    
    /**
     * Cancel appointment
     */
    public function cancel(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        // Validate CSRF
        (new CsrfMiddleware())->handle();
        
        $appointmentId = $_POST['appointment_id'] ?? null;
        $reason = $_POST['reason'] ?? 'Cancelled by patient';
        
        if (!$appointmentId) {
            flash('Invalid appointment', 'error');
            $this->redirect('/patient/appointments.php');
        }
        
        // Cancel appointment
        $result = $this->appointmentService->cancelAppointment(
            $appointmentId, 
            $reason,
            'patient'
        );
        
        if ($result['success']) {
            flash('Appointment cancelled successfully', 'success');
        } else {
            flash($result['message'], 'error');
        }
        
        $this->redirect('/patient/appointments.php');
    }
}