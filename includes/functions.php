<?php
/**
 * Kyle-HMS Helper Functions
 * Reusable utility functions
 * 
 * @author Noun Sunheng
 * @version 1.0
 */

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * @param string $password
 * @return array ['valid' => bool, 'message' => string]
 */
function validatePassword($password) {
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        return [
            'valid' => false,
            'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters long'
        ];
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        return [
            'valid' => false,
            'message' => 'Password must contain at least one uppercase letter'
        ];
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        return [
            'valid' => false,
            'message' => 'Password must contain at least one lowercase letter'
        ];
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        return [
            'valid' => false,
            'message' => 'Password must contain at least one number'
        ];
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return [
            'valid' => false,
            'message' => 'Password must contain at least one special character'
        ];
    }
    
    return ['valid' => true, 'message' => 'Password is strong'];
}

/**
 * Hash password securely
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Validate Cambodian phone number
 * @param string $phone
 * @return bool
 */
function isValidPhone($phone) {
    // Remove spaces and dashes
    $phone = preg_replace('/[\s\-]/', '', $phone);
    
    // Check if it matches Cambodian phone patterns
    // Accepts: 012345678, 096345678, +855 12 345 678, etc.
    return preg_match('/^(\+855|0)(1[0-9]|6[0-9]|7[0-9]|8[0-9]|9[0-9])[0-9]{6,7}$/', $phone);
}

/**
 * Format phone number for display
 * @param string $phone
 * @return string
 */
function formatPhone($phone) {
    $phone = preg_replace('/[\s\-]/', '', $phone);
    
    if (strlen($phone) === 9 && $phone[0] === '0') {
        return substr($phone, 0, 3) . '-' . substr($phone, 3, 3) . '-' . substr($phone, 6);
    }
    
    return $phone;
}

/**
 * Check if email already exists
 * @param string $email
 * @return bool
 */
function emailExists($email) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("SELECT email FROM webuser WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Email Check Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Upload profile image
 * @param array $file $_FILES array
 * @param string $folder Subfolder in uploads
 * @return array ['success' => bool, 'filename' => string, 'message' => string]
 */
function uploadImage($file, $folder = 'avatars') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return [
            'success' => false,
            'filename' => null,
            'message' => 'No file uploaded or upload error occurred'
        ];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return [
            'success' => false,
            'filename' => null,
            'message' => 'File size exceeds ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB limit'
        ];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, ALLOWED_IMAGE_TYPES)) {
        return [
            'success' => false,
            'filename' => null,
            'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed'
        ];
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    
    // Create upload directory if it doesn't exist
    $uploadDir = UPLOADS_PATH . '/' . $folder;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $destination = $uploadDir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return [
            'success' => true,
            'filename' => $filename,
            'message' => 'File uploaded successfully'
        ];
    } else {
        return [
            'success' => false,
            'filename' => null,
            'message' => 'Failed to move uploaded file'
        ];
    }
}

/**
 * Delete uploaded file
 * @param string $filename
 * @param string $folder
 * @return bool
 */
function deleteUploadedFile($filename, $folder = 'avatars') {
    if (empty($filename) || $filename === 'default-avatar.png' || $filename === 'default-doctor.png') {
        return false;
    }
    
    $filepath = UPLOADS_PATH . '/' . $folder . '/' . $filename;
    
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    
    return false;
}

/**
 * Get unread notification count
 * @param string $userEmail
 * @return int
 */
function getUnreadNotificationCount($userEmail) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM notifications 
            WHERE user_email = ? AND is_read = 0
        ");
        $stmt->execute([$userEmail]);
        $result = $stmt->fetch();
        
        return (int)$result['count'];
    } catch (PDOException $e) {
        error_log("Get Notification Count Error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get recent notifications for user
 * @param string $email
 * @param int $limit
 * @return array
 */
function getRecentNotifications($email, $limit = 5) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            SELECT * FROM notifications 
            WHERE user_email = ? 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$email, $limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Get Notifications Error: " . $e->getMessage());
        return [];
    }
}

/**
 * Mark notification as read
 * @param int $notifId
 * @return bool
 */
function markNotificationAsRead($notifId) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notif_id = ?");
        return $stmt->execute([$notifId]);
    } catch (PDOException $e) {
        error_log("Mark Notification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Create notification
 * @param string $userEmail
 * @param string $title
 * @param string $message
 * @param string $type
 * @return bool
 */
function createNotification($userEmail, $title, $message, $type = 'system') {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_email, title, message, type) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$userEmail, $title, $message, $type]);
    } catch (PDOException $e) {
        error_log("Create Notification Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Validate date format (YYYY-MM-DD)
 * @param string $date
 * @return bool
 */
function isValidDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Validate time format (HH:MM:SS or HH:MM)
 * @param string $time
 * @return bool
 */
function isValidTime($time) {
    return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time);
}

/**
 * Check if date is in the future
 * @param string $date
 * @return bool
 */
function isFutureDate($date) {
    return strtotime($date) > time();
}

/**
 * Check if date is within allowed booking range
 * @param string $date
 * @return bool
 */
function isWithinBookingRange($date) {
    $timestamp = strtotime($date);
    $minDate = strtotime('+' . MIN_BOOKING_HOURS . ' hours');
    $maxDate = strtotime('+' . MAX_BOOKING_DAYS . ' days');
    
    return $timestamp >= $minDate && $timestamp <= $maxDate;
}

/**
 * Get day name from date
 * @param string $date
 * @return string
 */
function getDayName($date) {
    return date('l', strtotime($date));
}

/**
 * Generate time slots array
 * @param string $startTime
 * @param string $endTime
 * @param int $interval Minutes
 * @return array
 */
function generateTimeSlots($startTime, $endTime, $interval = 30) {
    $slots = [];
    $start = strtotime($startTime);
    $end = strtotime($endTime);
    
    while ($start < $end) {
        $slots[] = date('H:i:s', $start);
        $start = strtotime('+' . $interval . ' minutes', $start);
    }
    
    return $slots;
}

/**
 * Get blood group options
 * @return array
 */
function getBloodGroupOptions() {
    return ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
}

/**
 * Get gender options
 * @return array
 */
function getGenderOptions() {
    return [
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other'
    ];
}

/**
 * Get appointment status badge HTML
 * @param string $status
 * @return string
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
        'confirmed' => '<span class="badge bg-info">Confirmed</span>',
        'completed' => '<span class="badge bg-success">Completed</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        'no_show' => '<span class="badge bg-secondary">No Show</span>',
        'active' => '<span class="badge bg-success">Active</span>',
        'inactive' => '<span class="badge bg-secondary">Inactive</span>',
        'on_leave' => '<span class="badge bg-warning">On Leave</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
}

/**
 * Get appointment status color
 * @param string $status
 * @return string
 */
function getStatusColor($status) {
    $colors = [
        'pending' => '#ffc107',
        'confirmed' => '#0dcaf0',
        'completed' => '#198754',
        'cancelled' => '#dc3545',
        'no_show' => '#6c757d'
    ];
    
    return $colors[$status] ?? '#6c757d';
}

/**
 * Truncate text to specified length
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Convert array to HTML select options
 * @param array $options
 * @param string $selected
 * @return string
 */
function arrayToOptions($options, $selected = '') {
    $html = '';
    foreach ($options as $value => $label) {
        $isSelected = ($value == $selected) ? 'selected' : '';
        $html .= '<option value="' . htmlspecialchars($value) . '" ' . $isSelected . '>';
        $html .= htmlspecialchars($label);
        $html .= '</option>';
    }
    return $html;
}

/**
 * Check if appointment can be cancelled
 * @param string $appointmentDate
 * @param string $appointmentTime
 * @return bool
 */
function canCancelAppointment($appointmentDate, $appointmentTime) {
    $appointmentDateTime = new DateTime($appointmentDate . ' ' . $appointmentTime);
    $now = new DateTime();
    $diff = $now->diff($appointmentDateTime);
    
    // Calculate hours difference
    $hoursDiff = ($diff->days * 24) + $diff->h;
    
    // If appointment is in the past, cannot cancel
    if ($appointmentDateTime < $now) {
        return false;
    }
    
    // Must cancel at least CANCELLATION_HOURS before appointment
    return $hoursDiff >= CANCELLATION_HOURS;
}

/**
 * Get time remaining until appointment
 * @param string $appointmentDate
 * @param string $appointmentTime
 * @return string
 */
function getTimeUntilAppointment($appointmentDate, $appointmentTime) {
    $appointmentDateTime = new DateTime($appointmentDate . ' ' . $appointmentTime);
    $now = new DateTime();
    
    if ($appointmentDateTime < $now) {
        return 'Past';
    }
    
    $diff = $now->diff($appointmentDateTime);
    
    if ($diff->days > 0) {
        return $diff->days . ' day' . ($diff->days > 1 ? 's' : '');
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '');
    } else {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
    }
}

/**
 * Check if date is today
 * @param string $date
 * @return bool
 */
function isToday($date) {
    return date('Y-m-d', strtotime($date)) === date('Y-m-d');
}

/**
 * Check if date is tomorrow
 * @param string $date
 * @return bool
 */
function isTomorrow($date) {
    return date('Y-m-d', strtotime($date)) === date('Y-m-d', strtotime('+1 day'));
}

/**
 * Generate secure random password
 * @param int $length
 * @return string
 */
function generatePassword($length = 12) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@$!%*?&#';
    $password = '';
    $charsLength = strlen($chars);
    
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[random_int(0, $charsLength - 1)];
    }
    
    return $password;
}

/**
 * Get file extension
 * @param string $filename
 * @return string
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Format file size
 * @param int $bytes
 * @return string
 */
function formatFileSize($bytes) {
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

/**
 * Get statistics for dashboard
 * @param string $userType
 * @param mixed $userId
 * @return array
 */
function getDashboardStats($userType, $userId) {
    global $conn;
    $stats = [];
    
    try {
        switch ($userType) {
            case 'p': // Patient
                $stmt = $conn->prepare("
                    SELECT 
                        COUNT(*) as total_appointments,
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as upcoming,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                    FROM appointment WHERE pid = ?
                ");
                $stmt->execute([$userId]);
                $stats = $stmt->fetch();
                break;
                
            case 'd': // Doctor
                $stmt = $conn->prepare("
                    SELECT 
                        COUNT(DISTINCT a.appoid) as total_appointments,
                        COUNT(DISTINCT a.pid) as total_patients,
                        SUM(CASE WHEN a.appodate = CURDATE() THEN 1 ELSE 0 END) as today_appointments,
                        SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending_appointments
                    FROM appointment a
                    JOIN schedule s ON a.scheduleid = s.scheduleid
                    WHERE s.docid = ?
                ");
                $stmt->execute([$userId]);
                $stats = $stmt->fetch();
                break;
                
            case 'a': // Admin
                $stmt = $conn->query("
                    SELECT 
                        (SELECT COUNT(*) FROM patient) as total_patients,
                        (SELECT COUNT(*) FROM doctor WHERE status = 'active') as total_doctors,
                        (SELECT COUNT(*) FROM appointment WHERE status = 'pending') as pending_appointments,
                        (SELECT COUNT(*) FROM appointment WHERE appodate = CURDATE()) as today_appointments
                ");
                $stats = $stmt->fetch();
                break;
        }
    } catch (PDOException $e) {
        error_log("Get Dashboard Stats Error: " . $e->getMessage());
    }
    
    return $stats ?: [];
}
?>