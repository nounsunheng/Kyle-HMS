<?php
/**
 * Kyle-HMS Main Configuration File
 * Application-wide settings and constants
 * 
 * @author Noun Sunheng
 * @version 1.0
 */

// Session configuration MUST come BEFORE session_start()
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone configuration
date_default_timezone_set('Asia/Phnom_Penh');

// Error reporting (DISABLE IN PRODUCTION!)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Application constants
define('APP_NAME', 'Kyle Hospital Management System');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/Kyle-HMS');
define('ADMIN_EMAIL', 'admin@kyle-hms.com');

// Path constants
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('ASSETS_PATH', APP_URL . '/assets');
define('UPLOADS_PATH', ROOT_PATH . '/assets/uploads');
define('UPLOADS_URL', ASSETS_PATH . '/uploads');

// File upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']);

// Session timeout
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes in seconds

// Pagination settings
define('RECORDS_PER_PAGE', 10);

// Appointment settings
define('MIN_BOOKING_HOURS', 2); // Minimum hours before appointment
define('MAX_BOOKING_DAYS', 90); // Maximum days in advance
define('CANCELLATION_HOURS', 24); // Hours before appointment to allow cancellation

// Email configuration (for future implementation)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', 'noreply@kyle-hms.com');
define('SMTP_FROM_NAME', 'Kyle HMS');

// Include database connection
require_once ROOT_PATH . '/config/database.php';

// Include helper functions
require_once ROOT_PATH . '/includes/functions.php';

/**
 * Redirect helper function
 * @param string $location
 */
function redirect($location) {
    if (!headers_sent()) {
        header("Location: " . APP_URL . $location);
        exit();
    } else {
        echo '<script>window.location.href="' . APP_URL . $location . '";</script>';
        exit();
    }
}

/**
 * Sanitize input data
 * @param string $data
 * @return string
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_email']) && isset($_SESSION['user_type']);
}

/**
 * Get current user email
 * @return string|null
 */
function getCurrentUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

/**
 * Get current user type
 * @return string|null
 */
function getCurrentUserType() {
    return $_SESSION['user_type'] ?? null;
}

/**
 * Format date for display
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Format time for display
 * @param string $time
 * @param string $format
 * @return string
 */
function formatTime($time, $format = 'g:i A') {
    return date($format, strtotime($time));
}

/**
 * Calculate age from date of birth
 * @param string $dob
 * @return int
 */
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}

/**
 * Display flash message
 */
function displayFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        
        $alertClass = [
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            'info' => 'alert-info'
        ];
        
        echo '<div class="alert ' . $alertClass[$type] . ' alert-dismissible fade show" role="alert">';
        echo htmlspecialchars($message);
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Set flash message
 * @param string $message
 * @param string $type
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Log user activity
 * @param string $action
 * @param string $description
 */
function logActivity($action, $description = null) {
    global $conn;
    
    if (!isLoggedIn()) {
        return;
    }
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO activity_logs (user_email, action, description, ip_address, user_agent) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            getCurrentUserEmail(),
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    } catch (PDOException $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}

/**
 * Generate unique appointment number
 * @return string
 */
function generateAppointmentNumber() {
    global $conn;
    
    $year = date('Y');
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM appointment 
        WHERE YEAR(created_at) = ?
    ");
    $stmt->execute([$year]);
    $result = $stmt->fetch();
    
    $number = $result['count'] + 1;
    
    return 'APT-' . $year . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
}
?>