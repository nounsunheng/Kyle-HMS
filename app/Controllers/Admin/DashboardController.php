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
use App\Repositories\PatientRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\AppointmentRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

/**
 * Admin Dashboard Controller
 * Displays system overview with statistics and recent activities
 */
class DashboardController extends Controller {
    
    private AppointmentService $appointmentService;
    private NotificationService $notificationService;
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;
    private AppointmentRepository $appointmentRepo;
    
    public function __construct() {
        $this->appointmentService = new AppointmentService();
        $this->notificationService = new NotificationService();
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->appointmentRepo = new AppointmentRepository();
    }
    
    /**
     * Show admin dashboard
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $userEmail = userEmail();
        
        // Get system statistics
        $stats = [
            'total_patients' => $this->patientRepo->getTotalCount(),
            'active_patients' => $this->patientRepo->getActiveCount(),
            'total_doctors' => $this->doctorRepo->getTotalCount(),
            'active_doctors' => $this->doctorRepo->getActiveCount(),
            'total_appointments' => $this->appointmentRepo->getTotalCount(),
            'today_appointments' => $this->appointmentRepo->getTodayCount(),
            'pending_appointments' => $this->appointmentRepo->getCountByStatus('pending'),
            'completed_appointments' => $this->appointmentRepo->getCountByStatus('completed')
        ];
        
        // Get recent appointments (last 10)
        $recentAppointments = $this->appointmentRepo->getRecentWithDetails(10);
        
        // Get recently registered patients (last 5)
        $recentPatients = $this->patientRepo->getRecent(5);
        
        // Get recently added doctors (last 5)
        $recentDoctors = $this->doctorRepo->getRecent(5);
        
        // Get appointment statistics by status
        $appointmentsByStatus = $this->appointmentRepo->getCountsByStatus();
        
        // Get appointments by specialty (top 5)
        $appointmentsBySpecialty = $this->appointmentRepo->getCountsBySpecialty(5);
        
        // Get monthly appointment trend (last 6 months)
        $monthlyTrend = $this->appointmentRepo->getMonthlyTrend(6);
        
        // Get notifications
        $notifications = $this->notificationService->getUserNotifications($userEmail, 5);
        $unreadCount = $this->notificationService->getUnreadCount($userEmail);
        
        // Prepare dashboard data
        $data = [
            'stats' => $stats,
            'recent_appointments' => $recentAppointments,
            'recent_patients' => $recentPatients,
            'recent_doctors' => $recentDoctors,
            'appointments_by_status' => $appointmentsByStatus,
            'appointments_by_specialty' => $appointmentsBySpecialty,
            'monthly_trend' => $monthlyTrend,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ];
        
        $this->view('admin.dashboard', $data);
    }
    
    /**
     * Get dashboard statistics via AJAX
     */
    public function getStats(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        if (!isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $stats = [
            'total_patients' => $this->patientRepo->getTotalCount(),
            'active_patients' => $this->patientRepo->getActiveCount(),
            'total_doctors' => $this->doctorRepo->getTotalCount(),
            'active_doctors' => $this->doctorRepo->getActiveCount(),
            'total_appointments' => $this->appointmentRepo->getTotalCount(),
            'today_appointments' => $this->appointmentRepo->getTodayCount(),
            'pending_appointments' => $this->appointmentRepo->getCountByStatus('pending'),
            'completed_appointments' => $this->appointmentRepo->getCountByStatus('completed')
        ];
        
        $this->json(['success' => true, 'data' => $stats]);
    }
}