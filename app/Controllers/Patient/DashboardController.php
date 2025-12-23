<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers\Patient;

use App\Core\Controller;
use App\Services\AppointmentService;
use App\Services\NotificationService;
use App\Repositories\PatientRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

/**
 * Patient Dashboard Controller
 * Displays patient dashboard with appointments and notifications
 */
class DashboardController extends Controller {
    
    private AppointmentService $appointmentService;
    private NotificationService $notificationService;
    private PatientRepository $patientRepo;
    
    public function __construct() {
        $this->appointmentService = new AppointmentService();
        $this->notificationService = new NotificationService();
        $this->patientRepo = new PatientRepository();
    }
    
    /**
     * Show patient dashboard
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        $patientId = userId();
        $userEmail = userEmail();
        
        // Get patient data with statistics
        $patient = $this->patientRepo->getWithStatistics($patientId);
        
        // Get upcoming appointments
        $upcomingAppointments = $this->appointmentService->getPatientAppointments(
            $patientId, 
            'confirmed'
        );
        
        // Get recent appointments
        $recentAppointments = $this->appointmentService->getPatientAppointments(
            $patientId
        );
        
        // Limit to 5 most recent
        $recentAppointments = array_slice($recentAppointments, 0, 5);
        
        // Get notifications
        $notifications = $this->notificationService->getUserNotifications($userEmail, 5);
        $unreadCount = $this->notificationService->getUnreadCount($userEmail);
        
        // Prepare dashboard data
        $data = [
            'patient' => $patient,
            'upcoming_appointments' => $upcomingAppointments,
            'recent_appointments' => $recentAppointments,
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'stats' => [
                'total' => $patient['total_appointments'] ?? 0,
                'completed' => $patient['completed_appointments'] ?? 0,
                'pending' => $patient['pending_appointments'] ?? 0,
                'cancelled' => $patient['cancelled_appointments'] ?? 0
            ]
        ];
        
        $this->view('patient.dashboard', $data);
    }
}