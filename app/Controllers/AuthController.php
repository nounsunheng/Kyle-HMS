<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use App\Services\ValidationService;
use App\Middleware\CsrfMiddleware;

/**
 * Authentication Controller
 * Handles login, registration, and logout
 */
class AuthController extends Controller {
    
    private AuthService $authService;
    private ValidationService $validator;
    
    public function __construct() {
        $this->authService = new AuthService();
        $this->validator = new ValidationService();
    }
    
    /**
     * Show login form
     */
    public function loginForm(): void {
        // Redirect if already logged in
        if ($this->authService->isAuthenticated()) {
            $role = currentUserRole();
            $this->redirect($this->getHomePath($role));
        }
        
        $this->view('auth.login');
    }
    
    /**
     * Process login
     */
    public function login(): void {
        // Validate CSRF token
        $csrf = new CsrfMiddleware();
        $csrf->handle();
        
        // Validate input
        $errors = $this->validator->validateLogin($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/auth/login.php');
        }
        
        // Attempt login
        $result = $this->authService->login(
            $_POST['email'],
            $_POST['password']
        );
        
        if ($result['success']) {
            // Check for intended URL
            $intendedUrl = $_SESSION['intended_url'] ?? $result['redirect'];
            unset($_SESSION['intended_url']);
            
            $this->redirect($intendedUrl);
        } else {
            flash($result['message'], 'error');
            $_SESSION['old_input'] = ['email' => $_POST['email']];
            $this->redirect('/auth/login.php');
        }
    }
    
    /**
     * Show registration form
     */
    public function registerForm(): void {
        // Redirect if already logged in
        if ($this->authService->isAuthenticated()) {
            $role = currentUserRole();
            $this->redirect($this->getHomePath($role));
        }
        
        $this->view('auth.register');
    }
    
    /**
     * Process registration
     */
    public function register(): void {
        // Validate CSRF token
        $csrf = new CsrfMiddleware();
        $csrf->handle();
        
        // Validate input
        $errors = $this->validator->validatePatientRegistration($_POST);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/auth/signup.php');
        }
        
        // Attempt registration
        $result = $this->authService->registerPatient($_POST);
        
        if ($result['success']) {
            flash('Registration successful! You can now login.', 'success');
            $this->redirect('/auth/login.php');
        } else {
            flash($result['message'], 'error');
            $_SESSION['old_input'] = $_POST;
            $this->redirect('/auth/signup.php');
        }
    }
    
    /**
     * Logout user
     */
    public function logout(): void {
        $this->authService->logout();
        flash('You have been logged out successfully.', 'success');
        $this->redirect('/index.php');
    }
    
    /**
     * Get home path based on role
     */
    private function getHomePath(string $role): string {
        return match($role) {
            'p' => '/patient/dashboard.php',
            'd' => '/doctor/dashboard.php',
            'a' => '/admin/dashboard.php',
            default => '/index.php'
        };
    }
}