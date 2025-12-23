<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\AppointmentService;
use App\Services\NotificationService;
use App\Repositories\AppointmentRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DoctorRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

/**
 * Admin Appointment Controller
 * Manages all appointments in the system
 */
class AppointmentController extends Controller {
    
    private AppointmentService $appointmentService;
    private AppointmentRepository $appointmentRepo;
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;
    private NotificationService $notificationService;
    
    public function __construct() {
        $this->appointmentService = new AppointmentService();
        $this->appointmentRepo = new AppointmentRepository();
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * List all appointments
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        // Get filter parameters
        $status = $this->input('status', '');
        $date = $this->input('date', '');
        $doctorId = $this->input('doctor', '');
        $patientId = $this->input('patient', '');
        
        // Build filters array
        $filters = [];
        if (!empty($status)) $filters['status'] = $status;
        if (!empty($date)) $filters['date'] = $date;
        if (!empty($doctorId)) $filters['doctor_id'] = $doctorId;
        if (!empty($patientId)) $filters['patient_id'] = $patientId;
        
        // Get appointments with filters
        if (!empty($filters)) {
            $appointments = $this->appointmentRepo->getWithFilters($filters);
        } else {
            $appointments = $this->appointmentRepo->getAllWithDetails();
        }
        
        // Get doctors and patients for filter dropdowns
        $doctors = $this->doctorRepo->getAllActive();
        $patients = $this->patientRepo->getAll();
        
        $data = [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'patients' => $patients,
            'filters' => $filters
        ];
        
        $this->view('admin.appointments', $data);
    }
    
    /**
     * View appointment details
     */
    public function show(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $appointment = $this->appointmentRepo->getWithDetails($id);
        
        if (!$appointment) {
            flash('Appointment not found', 'error');
            $this->redirect('/admin/appointments');
            return;
        }
        
        $data = [
            'appointment' => $appointment
        ];
        
        $this->view('admin.appointment-details', $data);
    }
    
    /**
     * Update appointment status
     */
    public function updateStatus(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/appointments');
            return;
        }
        
        $appointment = $this->appointmentRepo->find($id);
        
        if (!$appointment) {
            flash('Appointment not found', 'error');
            $this->redirect('/admin/appointments');
            return;
        }
        
        $newStatus = $this->input('status');
        $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no_show'];
        
        if (!in_array($newStatus, $validStatuses)) {
            flash('Invalid status', 'error');
            $this->redirect('/admin/appointments/' . $id);
            return;
        }
        
        try {
            $updated = $this->appointmentService->updateStatus($id, $newStatus);
            
            if (!$updated) {
                throw new \Exception('Failed to update status');
            }
            
            // Get appointment details for notification
            $appointmentDetails = $this->appointmentRepo->getWithDetails($id);
            
            // Notify patient
            $this->notificationService->create([
                'user_email' => $appointmentDetails['patient_email'],
                'title' => 'Appointment Status Updated',
                'message' => "Your appointment (#{$appointmentDetails['appointment_number']}) status has been updated to: " . ucfirst($newStatus),
                'type' => 'appointment'
            ]);
            
            flash('Appointment status updated successfully', 'success');
            $this->redirect('/admin/appointments/' . $id);
            
        } catch (\Exception $e) {
            error_log('Update appointment status error: ' . $e->getMessage());
            flash('Failed to update appointment status', 'error');
            $this->redirect('/admin/appointments/' . $id);
        }
    }
    
    /**
     * Cancel appointment
     */
    public function cancel(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/appointments');
            return;
        }
        
        $appointment = $this->appointmentRepo->find($id);
        
        if (!$appointment) {
            flash('Appointment not found', 'error');
            $this->redirect('/admin/appointments');
            return;
        }
        
        if ($appointment['status'] === 'cancelled') {
            flash('Appointment is already cancelled', 'warning');
            $this->redirect('/admin/appointments/' . $id);
            return;
        }
        
        $reason = $this->input('reason', 'Cancelled by administrator');
        
        try {
            $cancelled = $this->appointmentService->cancelAppointment($id, $reason, 'admin');
            
            if (!$cancelled) {
                throw new \Exception('Failed to cancel appointment');
            }
            
            flash('Appointment cancelled successfully', 'success');
            $this->redirect('/admin/appointments');
            
        } catch (\Exception $e) {
            error_log('Cancel appointment error: ' . $e->getMessage());
            flash('Failed to cancel appointment', 'error');
            $this->redirect('/admin/appointments/' . $id);
        }
    }
}