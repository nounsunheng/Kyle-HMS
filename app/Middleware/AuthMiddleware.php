<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Middleware;

/**
 * Authentication Middleware
 * Ensures user is logged in before accessing protected routes
 */
class AuthMiddleware {
    
    /**
     * Handle the request
     * Checks if user is authenticated, redirects to login if not
     * 
     * @return void
     */
    public function handle(): void {
        // Check if user is logged in
        if (!isLoggedIn()) {
            // Store intended URL for redirect after login
            if (!empty($_SERVER['REQUEST_URI'])) {
                $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            }
            
            // Set flash message
            flash('Please login to continue', 'warning');
            
            // Redirect to login page
            redirect('/auth/login.php');
        }
        
        // Check session timeout
        $this->checkSessionTimeout();
    }
    
    /**
     * Check if session has timed out
     * 
     * @return void
     */
    private function checkSessionTimeout(): void {
        $timeout = defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 3600; // 1 hour default
        
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            $elapsed = time() - $_SESSION['LAST_ACTIVITY'];
            
            if ($elapsed > $timeout) {
                // Session expired
                session_unset();
                session_destroy();
                session_start();
                
                flash('Your session has expired. Please login again.', 'warning');
                redirect('/auth/login.php');
            }
        }
        
        // Update last activity time
        $_SESSION['LAST_ACTIVITY'] = time();
    }
    
    /**
     * Check if user is authenticated (without redirect)
     * Useful for AJAX requests
     * 
     * @return bool
     */
    public static function check(): bool {
        return isLoggedIn();
    }
    
    /**
     * Get intended URL after login
     * 
     * @return string|null
     */
    public static function intended(): ?string {
        $intended = $_SESSION['intended_url'] ?? null;
        unset($_SESSION['intended_url']);
        return $intended;
    }
}