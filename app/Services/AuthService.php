<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\AdminRepository;
use App\Config\Security;

/**
 * Authentication Service
 * Handles login, registration, and session management
 */
class AuthService {
    
    private UserRepository $userRepo;
    private PatientRepository $patientRepo;
    private DoctorRepository $doctorRepo;
    private NotificationService $notificationService;
    
    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->patientRepo = new PatientRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Authenticate user login
     * 
     * @param string $email User email
     * @param string $password Plain text password
     * @return array Result with success status and data
     */
    public function login(string $email, string $password): array {
        // Verify credentials
        $user = $this->userRepo->verifyCredentials($email, $password);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Invalid email or password'
            ];
        }
        
        // Check if account is active
        if ($user['status'] !== 'active') {
            return [
                'success' => false,
                'message' => 'Your account has been suspended. Please contact administrator.'
            ];
        }
        
        // Update last login
        $this->userRepo->updateLastLogin($email);
        
        // Get user profile based on type
        $profile = $this->getUserProfile($email, $user['usertype']);
        
        if (!$profile) {
            return [
                'success' => false,
                'message' => 'Profile not found. Please contact administrator.'
            ];
        }
        
        // Create session
        $this->createSession($user, $profile);
        
        // Log activity
        logActivity('login', 'User logged in successfully');
        
        // Send welcome notification
        $this->notificationService->create([
            'user_email' => $email,
            'title' => 'Welcome Back!',
            'message' => 'You have successfully logged in to Kyle HMS.',
            'type' => 'system'
        ]);
        
        return [
            'success' => true,
            'message' => 'Login successful',
            'redirect' => $this->getRedirectPath($user['usertype'])
        ];
    }
    
    /**
     * Register new patient
     * 
     * @param array $data Registration data
     * @return array Result with success status
     */
    public function registerPatient(array $data): array {
        // Validate email doesn't exist
        if ($this->userRepo->emailExists($data['email'])) {
            return [
                'success' => false,
                'message' => 'Email already exists. Please use a different email.'
            ];
        }
        
        // Start transaction
        try {
            $this->userRepo->db->beginTransaction();
            
            // Create webuser account
            $userCreated = $this->userRepo->create([
                'email' => $data['email'],
                'password' => $data['password'],
                'usertype' => 'p',
                'status' => 'active'
            ]);
            
            if (!$userCreated) {
                throw new \Exception('Failed to create user account');
            }
            
            // Create patient profile
            $patientId = $this->patientRepo->create([
                'pemail' => $data['email'],
                'pname' => $data['name'],
                'pdob' => $data['dob'],
                'pgender' => $data['gender'],
                'ptel' => $data['tel'],
                'paddress' => $data['address'],
                'pbloodgroup' => $data['bloodgroup'] ?? null
            ]);
            
            if (!$patientId) {
                throw new \Exception('Failed to create patient profile');
            }
            
            $this->userRepo->db->commit();
            
            // Send welcome notification
            $this->notificationService->create([
                'user_email' => $data['email'],
                'title' => 'Welcome to Kyle HMS!',
                'message' => 'Your account has been created successfully. You can now book appointments with our doctors.',
                'type' => 'system'
            ]);
            
            // Log activity
            logActivity('register', 'New patient registered: ' . $data['email']);
            
            return [
                'success' => true,
                'message' => 'Registration successful! You can now login.',
                'patient_id' => $patientId
            ];
            
        } catch (\Exception $e) {
            $this->userRepo->db->rollBack();
            error_log("Registration Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Registration failed. Please try again.'
            ];
        }
    }
    
    /**
     * Logout user
     * 
     * @return void
     */
    public function logout(): void {
        // Log activity before destroying session
        if (isLoggedIn()) {
            logActivity('logout', 'User logged out');
        }
        
        // Destroy session
        session_unset();
        session_destroy();
        
        // Start new session
        session_start();
    }
    
    /**
     * Check if user is authenticated
     * 
     * @return bool
     */
    public function isAuthenticated(): bool {
        return isLoggedIn();
    }
    
    /**
     * Get current user profile
     * 
     * @return array|null User profile data
     */
    public function getCurrentUser(): ?array {
        if (!isLoggedIn()) {
            return null;
        }
        
        $email = currentUserEmail();
        $role = currentUserRole();
        
        return $this->getUserProfile($email, $role);
    }
    
    /**
     * Change user password
     * 
     * @param string $email User email
     * @param string $currentPassword Current password
     * @param string $newPassword New password
     * @return array Result
     */
    public function changePassword(string $email, string $currentPassword, string $newPassword): array {
        // Verify current password
        $user = $this->userRepo->verifyCredentials($email, $currentPassword);
        
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Current password is incorrect'
            ];
        }
        
        // Update password
        $updated = $this->userRepo->updatePassword($email, $newPassword);
        
        if ($updated) {
            logActivity('password_change', 'Password changed successfully');
            
            return [
                'success' => true,
                'message' => 'Password changed successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to change password'
        ];
    }
    
    /**
     * Get user profile based on type
     * 
     * @param string $email User email
     * @param string $usertype User type (p, d, a)
     * @return array|null Profile data
     */
    private function getUserProfile(string $email, string $usertype): ?array {
        switch ($usertype) {
            case 'p':
                return $this->patientRepo->findByEmail($email);
            case 'd':
                return $this->doctorRepo->findByEmail($email);
            case 'a':
                // Admin profile (to be implemented)
                return ['aemail' => $email, 'aname' => 'Administrator'];
            default:
                return null;
        }
    }
    
    /**
     * Create user session
     * 
     * @param array $user User data
     * @param array $profile Profile data
     * @return void
     */
    private function createSession(array $user, array $profile): void {
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['usertype'];
        
        // Set role-specific data
        switch ($user['usertype']) {
            case 'p':
                $_SESSION['user_id'] = $profile['pid'];
                $_SESSION['user_name'] = $profile['pname'];
                break;
            case 'd':
                $_SESSION['user_id'] = $profile['docid'];
                $_SESSION['user_name'] = $profile['docname'];
                break;
            case 'a':
                $_SESSION['user_id'] = $profile['aid'] ?? 1;
                $_SESSION['user_name'] = $profile['aname'] ?? 'Admin';
                break;
        }
        
        $_SESSION['login_time'] = time();
    }
    
    /**
     * Get redirect path based on user type
     * 
     * @param string $usertype User type
     * @return string Redirect path
     */
    private function getRedirectPath(string $usertype): string {
        switch ($usertype) {
            case 'p':
                return '/patient/dashboard.php';
            case 'd':
                return '/doctor/dashboard.php';
            case 'a':
                return '/admin/dashboard.php';
            default:
                return '/index.php';
        }
    }
}