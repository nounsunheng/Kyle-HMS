<?php
/**
 * api/mark-all-notifications-read.php
 * Mark all notifications as read for current user
 */

require_once '../config/config.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE user_email = ? AND is_read = 0
    ");
    $stmt->execute([getCurrentUserEmail()]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Mark All Read Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>