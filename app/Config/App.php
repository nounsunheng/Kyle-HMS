<?php
/**
 * Kyle-HMS Application Configuration
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Config;

class App {
    
    // Application Settings
    const NAME = 'Kyle Hospital Management System';
    const VERSION = '2.0.0';
    const ENVIRONMENT = 'development'; // development | production
    
    // URLs (adjust after moving to public folder)
    const BASE_URL = 'http://localhost/Kyle-HMS';
    const ASSETS_URL = self::BASE_URL . '/assets';
    
    // Paths
    const ROOT_PATH = __DIR__ . '/../..';
    const APP_PATH = self::ROOT_PATH . '/app';
    const STORAGE_PATH = self::ROOT_PATH . '/storage';
    const VIEWS_PATH = self::ROOT_PATH . '/views';
    const UPLOADS_PATH = self::ROOT_PATH . '/assets/uploads';
    
    // Timezone
    const TIMEZONE = 'Asia/Phnom_Penh';
    
    // Session Settings
    const SESSION_LIFETIME = 3600; // 1 hour
    const SESSION_NAME = 'KYLE_HMS_SESSION';
    
    // Security
    const PASSWORD_MIN_LENGTH = 8;
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15 minutes
    
    // Pagination
    const ITEMS_PER_PAGE = 10;
    
    // File Upload
    const MAX_UPLOAD_SIZE = 5242880; // 5MB
    const ALLOWED_AVATAR_TYPES = ['image/jpeg', 'image/png', 'image/gif'];
    const ALLOWED_DOCUMENT_TYPES = ['application/pdf'];
    
    // Appointment Settings
    const MIN_BOOKING_HOURS = 2;
    const MAX_BOOKING_DAYS = 90;
    const CANCELLATION_HOURS = 24;
    
    /**
     * Initialize application
     */
    public static function init(): void {
        // Set timezone
        date_default_timezone_set(self::TIMEZONE);
        
        // Error reporting based on environment
        if (self::ENVIRONMENT === 'development') {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 0);
            error_reporting(0);
        }
        
        // Session configuration
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
        ini_set('session.cookie_samesite', 'Strict');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_name(self::SESSION_NAME);
            session_start();
        }
        
        // Check session timeout
        self::checkSessionTimeout();
    }
    
    /**
     * Check and handle session timeout
     */
    private static function checkSessionTimeout(): void {
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            if (time() - $_SESSION['LAST_ACTIVITY'] > self::SESSION_LIFETIME) {
                session_unset();
                session_destroy();
                header('Location: ' . self::BASE_URL . '/auth/login.php?timeout=1');
                exit();
            }
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}