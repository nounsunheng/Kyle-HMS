<?php
/**
 * Global Helper Functions
 * Auto-loaded via Composer
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

use App\Config\App;
use App\Config\Security;

// ============================================
// URL HELPERS
// ============================================

// Define constants if not already defined
if (!defined('MIN_BOOKING_HOURS')) {
    define('MIN_BOOKING_HOURS', 2);
}
if (!defined('CANCELLATION_HOURS')) {
    define('CANCELLATION_HOURS', 24);
}
if (!defined('SESSION_TIMEOUT')) {
    define('SESSION_TIMEOUT', 3600);
}

/**
 * Redirect to URL
 */
function redirect(string $path): void {
    $url = App::BASE_URL . $path;
    
    if (!headers_sent()) {
        header("Location: $url");
        exit();
    } else {
        echo "window.location.href='$url';";
        exit();
    }
}

/**
 * Get full URL
 */
function url(string $path = ''): string {
    return App::BASE_URL . $path;
}

/**
 * Get asset URL
 */
function asset(string $path): string {
    return App::ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Redirect back to previous page
 */
function back(): void {
    $referer = $_SERVER['HTTP_REFERER'] ?? App::BASE_URL;
    header("Location: $referer");
    exit();
}

// ============================================
// AUTHENTICATION HELPERS
// ============================================

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

/**
 * Get current user ID
 */
function userId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user email
 */
function userEmail(): ?string {
    return $_SESSION['user_email'] ?? null;
}

/**
 * Get current user role (patient, doctor, admin)
 */
function userRole(): ?string {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if user has specific role
 */
function hasRole($roles): bool {
    if (!isLoggedIn()) {
        return false;
    }
    
    $userRole = userRole();
    
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    
    return $userRole === $roles;
}

/**
 * Require authentication - redirect if not logged in
 */
function requireAuth(): void {
    if (!isLoggedIn()) {
        flash('Please login to continue', 'warning');
        redirect('/auth/login.php');
    }
}

/**
 * Require specific role
 */
function requireRole($roles): void {
    requireAuth();
    
    if (!hasRole($roles)) {
        flash('Access denied', 'error');
        redirect('/index.php');
    }
}

// ============================================
// FLASH MESSAGE HELPERS
// ============================================

/**
 * Set flash message
 */
function flash(string $message, string $type = 'info'): void {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Display flash message HTML
 */
function displayFlash(): string {
    if (!isset($_SESSION['flash_message'])) {
        return '';
    }
    
    $message = $_SESSION['flash_message'];
    $type = $_SESSION['flash_type'] ?? 'info';
    
    $alertClass = [
        'success' => 'alert-success',
        'error' => 'alert-danger',
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $class = $alertClass[$type] ?? 'alert-info';
    $icon = [
        'success' => 'check-circle',
        'error' => 'x-circle',
        'warning' => 'exclamation-triangle',
        'info' => 'info-circle'
    ][$type] ?? 'info-circle';
    
    $html = '';
    $html .= '';
    $html .= Security::escape($message);
    $html .= '';
    $html .= '';
    
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    
    return $html;
}

// ============================================
// DATE & TIME HELPERS
// ============================================

/**
 * Format date for display
 */
function formatDate(string $date, string $format = 'F j, Y'): string {
    if (empty($date) || $date === '0000-00-00') {
        return 'N/A';
    }
    return date($format, strtotime($date));
}

/**
 * Format time for display
 */
function formatTime(string $time, string $format = 'g:i A'): string {
    if (empty($time)) {
        return 'N/A';
    }
    return date($format, strtotime($time));
}

/**
 * Format datetime for display
 */
function formatDateTime(string $datetime, string $format = 'F j, Y g:i A'): string {
    if (empty($datetime)) {
        return 'N/A';
    }
    return date($format, strtotime($datetime));
}

/**
 * Get human-readable time difference
 */
function timeAgo(string $datetime): string {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return formatDate($datetime);
    }
}

/**
 * Calculate age from date of birth
 */
function calculateAge(string $dob): int {
    if (empty($dob)) {
        return 0;
    }
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}

/**
 * Check if date is in the past
 */
function isPastDate(string $date): bool {
    return strtotime($date) < strtotime('today');
}

/**
 * Check if date is today
 */
function isToday(string $date): bool {
    return date('Y-m-d', strtotime($date)) === date('Y-m-d');
}

// ============================================
// VALIDATION HELPERS
// ============================================

/**
 * Get old input value (from failed validation)
 */
function old(string $key, $default = '') {
    return $_SESSION['old_input'][$key] ?? $default;
}

/**
 * Get validation error for field
 */
function error(string $key): string {
    return $_SESSION['errors'][$key] ?? '';
}

/**
 * Check if field has validation error
 */
function hasError(string $key): bool {
    return isset($_SESSION['errors'][$key]);
}

/**
 * Clear old input and errors
 */
function clearValidation(): void {
    unset($_SESSION['old_input'], $_SESSION['errors']);
}

// ============================================
// FILE UPLOAD HELPERS
// ============================================

/**
 * Generate unique filename
 */
function generateUniqueFilename(string $originalName): string {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    return uniqid('file_', true) . '.' . $extension;
}

/**
 * Get file extension
 */
function getFileExtension(string $filename): string {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is image
 */
function isImage(string $mimeType): bool {
    return in_array($mimeType, App::ALLOWED_AVATAR_TYPES);
}

/**
 * Format file size
 */
function formatFileSize(int $bytes): string {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// ============================================
// STRING HELPERS
// ============================================

/**
 * Truncate string
 */
function truncate(string $text, int $length = 100, string $suffix = '...'): string {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Convert string to slug
 */
function slug(string $text): string {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Generate random password
 */
function generatePassword(int $length = 12): string {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    return substr(str_shuffle(str_repeat($chars, ceil($length / strlen($chars)))), 0, $length);
}

// ============================================
// DEBUG HELPERS
// ============================================

/**
 * Dump and die
 */
function dd($data): void {
    echo '';
    var_dump($data);
    echo '';
    die();
}

/**
 * Dump variable
 */
function dump($data): void {
    echo '';
    var_dump($data);
    echo '';
}

// ============================================
// UTILITY HELPERS
// ============================================

/**
 * Check if request is POST
 */
function isPost(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if request is GET
 */
function isGet(): bool {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Check if request is AJAX
 */
function isAjax(): bool {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get request input
 */
function input(string $key, $default = null) {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Return JSON response
 */
function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Log user activity
 * 
 * @param string $action Action performed
 * @param string|null $description Optional description
 * @return void
 */
function logActivity(string $action, ?string $description = null): void {
    try {
        if (!isLoggedIn()) {
            return;
        }
        
        $db = \App\Config\Database::getInstance()->getConnection();
        
        $sql = "
            INSERT INTO activity_logs (user_email, action, description, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?)
        ";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([
            currentUserEmail(),
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
    } catch (\PDOException $e) {
        error_log("Activity Log Error: " . $e->getMessage());
    }
}