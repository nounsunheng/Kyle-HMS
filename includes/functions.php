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
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'confirmed' => '<span class="badge bg-info">Confirmed</span>',
        'completed' => '<span class="badge bg-success">Completed</span>',
        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
        'no_show' => '<span class="badge bg-secondary">No Show</span>'
    ];
    
    return $badges[$status] ?? '<span class="badge bg-secondary">Unknown</span>';
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
?>