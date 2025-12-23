<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Services;

use App\Repositories\AppointmentRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DoctorRepository;

/**
 * Appointment Service
 * Handles appointment booking, cancellation, and management
 */
class AppointmentService {
    
    private AppointmentRepository $appointmentRepo;
    private ScheduleRepository $scheduleRepo;
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;
    private NotificationService $notificationService;
    
    public function __construct() {
        $this->appointmentRepo = new AppointmentRepository();
        $this->scheduleRepo = new ScheduleRepository();
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Book appointment for patient
     * 
     * @param array $data Appointment data
     * @return array Result with success status
     */
    public function bookAppointment(array $data): array {
        // Validate required fields
        if (empty($data['patient_id']) || empty($data['schedule_id'])) {
            return [
                'success' => false,
                'message' => 'Missing required information'
            ];
        }
        
        // Get schedule details
        $schedule = $this->scheduleRepo->findById($data['schedule_id']);
        
        if (!$schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found'
            ];
        }
        
        // Check schedule status
        if ($schedule['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'This schedule is no longer available'
            ];
        }
        
        // Check availability
        $availability = $this->scheduleRepo->checkAvailability($data['schedule_id']);
        
        if ($availability['available_slots'] <= 0) {
            return [
                'success' => false,
                'message' => 'No available slots for this schedule'
            ];
        }
        
        // Check if patient already has appointment for this schedule
        $hasExisting = $this->appointmentRepo->hasExistingAppointment(
            $data['patient_id'],
            $data['schedule_id']
        );
        
        if ($hasExisting) {
            return [
                'success' => false,
                'message' => 'You already have an appointment for this schedule'
            ];
        }
        
        // Check minimum booking time (2 hours before)
        $scheduleDateTime = $schedule['scheduledate'] . ' ' . $schedule['scheduletime'];
        $minBookingTime = strtotime($scheduleDateTime) - (MIN_BOOKING_HOURS * 3600);
        
        if (time() > $minBookingTime) {
            return [
                'success' => false,
                'message' => 'Cannot book appointment less than ' . MIN_BOOKING_HOURS . ' hours before schedule'
            ];
        }
        
        // Generate appointment number
        $appointmentNumber = $this->generateAppointmentNumber();
        
        // Calculate appointment number in schedule
        $appoNum = $schedule['booked'] + 1;
        
        // Create appointment
        try {
            $appointmentId = $this->appointmentRepo->create([
                'pid' => $data['patient_id'],
                'scheduleid' => $data['schedule_id'],
                'apponum' => $appoNum,
                'appodate' => $schedule['scheduledate'],
                'appotime' => $schedule['scheduletime'],
                'appointment_number' => $appointmentNumber,
                'status' => 'pending',
                'symptoms' => $data['symptoms'] ?? null,
                'notes' => $data['notes'] ?? null
            ]);
            
            if (!$appointmentId) {
                throw new \Exception('Failed to create appointment');
            }
            
            // Get patient and doctor info for notifications
            $patient = $this->patientRepo->findById($data['patient_id']);
            $doctor = $this->doctorRepo->findById($schedule['docid']);
            
            // Send notification to patient
            $this->notificationService->create([
                'user_email' => $patient['pemail'],
                'title' => 'Appointment Confirmed',
                'message' => "Your appointment with Dr. {$doctor['docname']} has been scheduled for {$schedule['scheduledate']} at {$schedule['scheduletime']}",
                'type' => 'appointment'
            ]);
            
            // Send notification to doctor
            $this->notificationService->create([
                'user_email' => $doctor['docemail'],
                'title' => 'New Appointment',
                'message' => "New appointment with {$patient['pname']} on {$schedule['scheduledate']} at {$schedule['scheduletime']}",
                'type' => 'appointment'
            ]);
            
            // Log activity
            logActivity('book_appointment', "Booked appointment: $appointmentNumber");
            
            return [
                'success' => true,
                'message' => 'Appointment booked successfully',
                'appointment_id' => $appointmentId,
                'appointment_number' => $appointmentNumber
            ];
            
        } catch (\Exception $e) {
            error_log("Booking Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to book appointment. Please try again.'
            ];
        }
    }
    
    /**
     * Cancel appointment
     * 
     * @param int $appointmentId Appointment ID
     * @param string $reason Cancellation reason
     * @param string $cancelledBy Who cancelled (patient/doctor/admin)
     * @return array Result
     */
    public function cancelAppointment(int $appointmentId, string $reason, string $cancelledBy = 'patient'): array {
        // Get appointment details
        $appointment = $this->appointmentRepo->getWithDetails($appointmentId);
        
        if (!$appointment) {
            return [
                'success' => false,
                'message' => 'Appointment not found'
            ];
        }
        
        // Check if already cancelled
        if ($appointment['appointment_status'] === 'cancelled') {
            return [
                'success' => false,
                'message' => 'Appointment is already cancelled'
            ];
        }
        
        // Check cancellation time restriction (24 hours before)
        if ($cancelledBy === 'patient') {
            $appointmentDateTime = $appointment['appodate'] . ' ' . $appointment['appotime'];
            $minCancellationTime = strtotime($appointmentDateTime) - (CANCELLATION_HOURS * 3600);
            
            if (time() > $minCancellationTime) {
                return [
                    'success' => false,
                    'message' => 'Cannot cancel appointment less than ' . CANCELLATION_HOURS . ' hours before schedule'
                ];
            }
        }
        
        // Cancel appointment
        $cancelled = $this->appointmentRepo->updateStatus($appointmentId, 'cancelled', $reason);
        
        if ($cancelled) {
            // Send cancellation notification to patient
            $this->notificationService->create([
                'user_email' => $appointment['patient_email'],
                'title' => 'Appointment Cancelled',
                'message' => "Your appointment #{$appointment['appointment_number']} has been cancelled. Reason: $reason",
                'type' => 'cancellation'
            ]);
            
            // Send notification to doctor
            $this->notificationService->create([
                'user_email' => $appointment['doctor_email'],
                'title' => 'Appointment Cancelled',
                'message' => "Appointment with {$appointment['patient_name']} on {$appointment['appodate']} has been cancelled",
                'type' => 'cancellation'
            ]);
            
            // Log activity
            logActivity('cancel_appointment', "Cancelled appointment #{$appointment['appointment_number']}");
            
            return [
                'success' => true,
                'message' => 'Appointment cancelled successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to cancel appointment'
        ];
    }
    
    /**
     * Update appointment status (for doctors)
     * 
     * @param int $appointmentId Appointment ID
     * @param string $status New status
     * @return array Result
     */
    public function updateStatus(int $appointmentId, string $status): array {
        $appointment = $this->appointmentRepo->getWithDetails($appointmentId);
        
        if (!$appointment) {
            return [
                'success' => false,
                'message' => 'Appointment not found'
            ];
        }
        
        $updated = $this->appointmentRepo->updateStatus($appointmentId, $status);
        
        if ($updated) {
            // Send notification to patient
            $statusText = ucfirst($status);
            $this->notificationService->create([
                'user_email' => $appointment['patient_email'],
                'title' => 'Appointment Status Updated',
                'message' => "Your appointment #{$appointment['appointment_number']} status has been updated to: $statusText",
                'type' => 'appointment'
            ]);
            
            // Log activity
            logActivity('update_appointment_status', "Updated appointment #{$appointment['appointment_number']} to $status");
            
            return [
                'success' => true,
                'message' => 'Appointment status updated successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to update appointment status'
        ];
    }
    
    /**
     * Get patient appointments
     * 
     * @param int $patientId Patient ID
     * @param string|null $filter Filter by status
     * @return array Appointments list
     */
    public function getPatientAppointments(int $patientId, ?string $filter = null): array {
        return $this->appointmentRepo->getPatientAppointments($patientId, $filter);
    }
    
    /**
     * Get doctor appointments
     * 
     * @param int $doctorId Doctor ID
     * @param string|null $date Filter by date
     * @param string|null $status Filter by status
     * @return array Appointments list
     */
    public function getDoctorAppointments(int $doctorId, ?string $date = null, ?string $status = null): array {
        return $this->appointmentRepo->getDoctorAppointments($doctorId, $date, $status);
    }
    
    /**
     * Generate unique appointment number
     * 
     * @return string Appointment number (e.g., APT-2025-000001)
     */
    private function generateAppointmentNumber(): string {
        $year = date('Y');
        
        // Count appointments this year
        $count = $this->appointmentRepo->countByStatus('all') + 1;
        
        return sprintf('APT-%s-%06d', $year, $count);
    }
}