<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Middleware;

use App\Config\Security;

/**
 * CSRF Middleware
 * Protects against Cross-Site Request Forgery attacks
 */
class CsrfMiddleware {
    
    /**
     * HTTP methods that require CSRF protection
     * 
     * @var array
     */
    private array $protectedMethods = ['POST', 'PUT', 'PATCH', 'DELETE'];
    
    /**
     * Routes that are exempt from CSRF protection
     * (e.g., API endpoints with other authentication)
     * 
     * @var array
     */
    private array $exemptRoutes = [
        '/api/',
        '/webhook/'
    ];
    
    /**
     * Handle the request
     * Validates CSRF token for protected HTTP methods
     * 
     * @return void
     */
    public function handle(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Skip if method doesn't require protection
        if (!in_array($method, $this->protectedMethods)) {
            return;
        }
        
        // Skip if route is exempt
        if ($this->isExemptRoute()) {
            return;
        }
        
        // Validate CSRF token
        if (!$this->validateToken()) {
            $this->tokenMismatch();
        }
    }
    
    /**
     * Validate CSRF token
     * 
     * @return bool
     */
    private function validateToken(): bool {
        // Get token from request
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        
        if (empty($token)) {
            return false;
        }
        
        // Verify token
        return Security::verifyCSRFToken($token);
    }
    
    /**
     * Check if current route is exempt from CSRF protection
     * 
     * @return bool
     */
    private function isExemptRoute(): bool {
        $currentPath = $_SERVER['REQUEST_URI'] ?? '';
        
        foreach ($this->exemptRoutes as $exemptRoute) {
            if (strpos($currentPath, $exemptRoute) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Handle token mismatch
     * 
     * @return void
     */
    private function tokenMismatch(): void {
        // Log security incident
        error_log("CSRF token mismatch from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        
        logActivity('csrf_token_mismatch', 'CSRF token validation failed');
        
        // Check if AJAX request
        if (isAjax()) {
            http_response_code(419); // 419 Session Expired
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                'code' => 'CSRF_TOKEN_MISMATCH'
            ]);
            exit;
        }
        
        // Show HTML error page
        $this->showTokenMismatchPage();
        exit;
    }
    
    /**
     * Display CSRF token mismatch error page
     * 
     * @return void
     */
    private function showTokenMismatchPage(): void {
        http_response_code(419);
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>419 - Token Mismatch</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    min-height: 100vh;
                    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                }
                .error-container {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
                    max-width: 500px;
                    text-align: center;
                }
                .error-icon {
                    font-size: 80px;
                    color: #f5576c;
                    margin-bottom: 20px;
                }
                .error-code {
                    font-size: 48px;
                    font-weight: bold;
                    color: #333;
                    margin: 0;
                }
                .error-title {
                    font-size: 24px;
                    color: #666;
                    margin: 10px 0 20px 0;
                }
                .error-description {
                    font-size: 16px;
                    color: #888;
                    line-height: 1.6;
                    margin-bottom: 30px;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <div class="error-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="error-code">419</h1>
                <p class="error-title">CSRF Token Mismatch</p>
                <p class="error-description">
                    Your session has expired or the security token is invalid.<br>
                    This usually happens when you leave a page open for too long.<br>
                    Please refresh the page and try again.
                </p>
                <div>
                    <button onclick="location.reload()" class="btn btn-primary me-2">
                        <i class="fas fa-sync-alt"></i> Refresh Page
                    </button>
                    <button onclick="history.back()" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Go Back
                    </button>
                </div>
            </div>
            
            <script src="https://kit.fontawesome.com/a076d05399.js"></script>
        </body>
        </html>
        <?php
    }
    
    /**
     * Generate CSRF token field for forms
     * 
     * @return string HTML input field
     */
    public static function field(): string {
        return Security::csrfField();
    }
    
    /**
     * Get CSRF token value
     * 
     * @return string Token value
     */
    public static function token(): string {
        return Security::generateCSRFToken();
    }
    
    /**
     * Verify CSRF token (for manual checking)
     * 
     * @param string $token Token to verify
     * @return bool
     */
    public static function verify(string $token): bool {
        return Security::verifyCSRFToken($token);
    }
}