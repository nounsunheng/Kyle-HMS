<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Middleware;

use App\Config\App;

/**
 * Role Middleware
 * Enforces role-based access control (RBAC)
 */
class RoleMiddleware {
    
    /**
     * Allowed roles for the current route
     * 
     * @var array
     */
    private array $allowedRoles = [];
    
    /**
     * Constructor
     * 
     * @param array|string $allowedRoles Roles that can access this route
     */
    public function __construct($allowedRoles = []) {
        $this->allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    }
    
    /**
     * Handle the request
     * Checks if user has required role
     * 
     * @return void
     */
    public function handle(): void {
        // First check if user is logged in
        if (!isLoggedIn()) {
            flash('Please login to continue', 'warning');
            redirect('/auth/login.php');
        }
        
        // Get user's role
        $userRole = currentUserRole();
        
        // Check if user has required role
        if (!$this->hasRole($userRole)) {
            // Log unauthorized access attempt
            logActivity('unauthorized_access', 
                "Attempted to access restricted area. Required: " . implode(', ', $this->allowedRoles));
            
            // Show 403 Forbidden
            $this->forbidden();
        }
    }
    
    /**
     * Check if user has one of the allowed roles
     * 
     * @param string|null $userRole User's role
     * @return bool
     */
    private function hasRole(?string $userRole): bool {
        if (empty($this->allowedRoles)) {
            return true; // No role restriction
        }
        
        return in_array($userRole, $this->allowedRoles);
    }
    
    /**
     * Show 403 Forbidden page
     * 
     * @return void
     */
    private function forbidden(): void {
        http_response_code(403);
        
        // Check if it's an AJAX request
        if (isAjax()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Access denied. You do not have permission to access this resource.'
            ]);
            exit;
        }
        
        // Show HTML error page
        $this->showForbiddenPage();
        exit;
    }
    
    /**
     * Display 403 Forbidden HTML page
     * 
     * @return void
     */
    private function showForbiddenPage(): void {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>403 - Access Denied</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                }
                .error-container {
                    text-align: center;
                    color: white;
                }
                .error-code {
                    font-size: 120px;
                    font-weight: bold;
                    margin: 0;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                }
                .error-message {
                    font-size: 24px;
                    margin: 20px 0;
                }
                .error-description {
                    font-size: 16px;
                    opacity: 0.9;
                    margin-bottom: 30px;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1 class="error-code">403</h1>
                <p class="error-message">Access Denied</p>
                <p class="error-description">
                    You do not have permission to access this page.<br>
                    Please contact the administrator if you believe this is an error.
                </p>
                <div>
                    <a href="javascript:history.back()" class="btn btn-light me-2">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </a>
                    <a href="<?= App::BASE_URL ?>/index.php" class="btn btn-outline-light">
                        <i class="fas fa-home"></i> Home
                    </a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
    
    /**
     * Static method to check role (for use in views/controllers)
     * 
     * @param array|string $roles Roles to check
     * @return bool
     */
    public static function checkRole($roles): bool {
        if (!isLoggedIn()) {
            return false;
        }
        
        $userRole = currentUserRole();
        $allowedRoles = is_array($roles) ? $roles : [$roles];
        
        return in_array($userRole, $allowedRoles);
    }
    
    /**
     * Check if user is patient
     * 
     * @return bool
     */
    public static function isPatient(): bool {
        return currentUserRole() === 'p';
    }
    
    /**
     * Check if user is doctor
     * 
     * @return bool
     */
    public static function isDoctor(): bool {
        return currentUserRole() === 'd';
    }
    
    /**
     * Check if user is admin
     * 
     * @return bool
     */
    public static function isAdmin(): bool {
        return currentUserRole() === 'a';
    }
}