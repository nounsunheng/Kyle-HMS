<?php
/**
 * Kyle-HMS Session Management
 * Handles user sessions and authentication checks
 * 
 * @author Noun Sunheng
 * @version 1.0
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Check if session is valid
 * @return bool
 */
function isSessionValid() {
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }
    
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Require login - redirect if not authenticated
 * @param string|array $allowedTypes User types allowed (p, d, a)
 */
function requireLogin($allowedTypes = null) {
    if (!isLoggedIn() || !isSessionValid()) {
        destroySession();
        setFlashMessage('Please login to continue', 'warning');
        redirect('/auth/login.php');
    }
    
    if ($allowedTypes !== null) {
        $userType = getCurrentUserType();
        
        if (is_array($allowedTypes)) {
            if (!in_array($userType, $allowedTypes)) {
                setFlashMessage('Access denied', 'error');
                redirectToDashboard($userType);
            }
        } else {
            if ($userType !== $allowedTypes) {
                setFlashMessage('Access denied', 'error');
                redirectToDashboard($userType);
            }
        }
    }
    
    // Regenerate session ID periodically for security
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } else if (time() - $_SESSION['last_regeneration'] > 300) { // Every 5 minutes
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

/**
 * Initialize user session after successful login
 * @param string $email
 * @param string $userType
 * @param array $userData Additional user data
 */
function initializeSession($email, $userType, $userData = []) {
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Set session variables
    $_SESSION['user_email'] = $email;
    $_SESSION['user_type'] = $userType;
    $_SESSION['last_activity'] = time();
    $_SESSION['last_regeneration'] = time();
    $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Store additional user data
    foreach ($userData as $key => $value) {
        $_SESSION[$key] = $value;
    }
    
    // Update last login time
    updateLastLogin($email);
    
    // Log login activity
    logActivity('login', 'User logged in successfully');
}

/**
 * Update last login timestamp
 * @param string $email
 */
function updateLastLogin($email) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("UPDATE webuser SET last_login = NOW() WHERE email = ?");
        $stmt->execute([$email]);
    } catch (PDOException $e) {
        error_log("Update Last Login Error: " . $e->getMessage());
    }
}

/**
 * Destroy session and logout user
 */
function destroySession() {
    if (isLoggedIn()) {
        logActivity('logout', 'User logged out');
    }
    
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_destroy();
}

/**
 * Redirect to appropriate dashboard based on user type
 * @param string $userType
 */
function redirectToDashboard($userType) {
    switch ($userType) {
        case 'p':
            redirect('/patient/dashboard.php');
            break;
        case 'd':
            redirect('/doctor/dashboard.php');
            break;
        case 'a':
            redirect('/admin/dashboard.php');
            break;
        default:
            redirect('/auth/login.php');
    }
}

/**
 * Get user full name from database
 * @param string $email
 * @param string $userType
 * @return string
 */
function getUserFullName($email, $userType) {
    global $conn;
    
    try {
        switch ($userType) {
            case 'p':
                $stmt = $conn->prepare("SELECT pname as name FROM patient WHERE pemail = ?");
                break;
            case 'd':
                $stmt = $conn->prepare("SELECT docname as name FROM doctor WHERE docemail = ?");
                break;
            case 'a':
                $stmt = $conn->prepare("SELECT aname as name FROM admin WHERE aemail = ?");
                break;
            default:
                return 'Unknown User';
        }
        
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        return $result['name'] ?? 'Unknown User';
    } catch (PDOException $e) {
        error_log("Get User Name Error: " . $e->getMessage());
        return 'Unknown User';
    }
}

/**
 * Get user ID from database
 * @param string $email
 * @param string $userType
 * @return int|null
 */
function getUserId($email, $userType) {
    global $conn;
    
    try {
        switch ($userType) {
            case 'p':
                $stmt = $conn->prepare("SELECT pid as id FROM patient WHERE pemail = ?");
                break;
            case 'd':
                $stmt = $conn->prepare("SELECT docid as id FROM doctor WHERE docemail = ?");
                break;
            case 'a':
                $stmt = $conn->prepare("SELECT aid as id FROM admin WHERE aemail = ?");
                break;
            default:
                return null;
        }
        
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        return $result['id'] ?? null;
    } catch (PDOException $e) {
        error_log("Get User ID Error: " . $e->getMessage());
        return null;
    }
}

/**
 * Check if user account is active
 * @param string $email
 * @return bool
 */
function isAccountActive($email) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT status FROM webuser WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        
        return $result && $result['status'] === 'active';
    } catch (PDOException $e) {
        error_log("Check Account Status Error: " . $e->getMessage());
        return false;
    }
}
?>