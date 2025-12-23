<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers\Patient;

use App\Core\Controller;
use App\Services\AuthService;
use App\Repositories\PatientRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

/**
 * Patient Profile Controller
 * Manage patient profile and settings
 */
class ProfileController extends Controller {
    
    private PatientRepository $patientRepo;
    private AuthService $authService;
    
    public function __construct() {
        $this->patientRepo = new PatientRepository();
        $this->authService = new AuthService();
    }
    
    /**
     * Show profile page
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        $patientId = userId();
        
        // Get patient data
        $patient = $this->patientRepo->findById($patientId);
        
        $data = [
            'patient' => $patient
        ];
        
        $this->view('patient.profile', $data);
    }
    
    /**
     * Update profile
     */
    public function update(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        // Validate CSRF
        (new CsrfMiddleware())->handle();
        
        $patientId = userId();
        
        // Update patient data
        $updated = $this->patientRepo->update($patientId, [
            'pname' => $_POST['name'],
            'pdob' => $_POST['dob'],
            'pgender' => $_POST['gender'],
            'ptel' => $_POST['tel'],
            'paddress' => $_POST['address'],
            'pbloodgroup' => $_POST['bloodgroup'] ?? null,
            'pemergency_contact' => $_POST['emergency_contact'] ?? null,
            'pemergency_name' => $_POST['emergency_name'] ?? null,
            'profile_image' => $_POST['profile_image'] ?? 'default-avatar.png'
        ]);
        
        if ($updated) {
            flash('Profile updated successfully', 'success');
            logActivity('update_profile', 'Patient profile updated');
        } else {
            flash('Failed to update profile', 'error');
        }
        
        $this->redirect('/patient/profile.php');
    }
    
    /**
     * Change password
     */
    public function changePassword(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        // Validate CSRF
        (new CsrfMiddleware())->handle();
        
        $userEmail = userEmail();
        
        // Change password
        $result = $this->authService->changePassword(
            $userEmail,
            $_POST['current_password'],
            $_POST['new_password']
        );
        
        if ($result['success']) {
            flash($result['message'], 'success');
        } else {
            flash($result['message'], 'error');
        }
        
        $this->redirect('/patient/profile.php');
    }
}