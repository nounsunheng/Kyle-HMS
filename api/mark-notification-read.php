<?php
/**
 * api/mark-notification-read.php
 * Mark single notification as read
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

$input = json_decode(file_get_contents('php://input'), true);
$notifId = (int)($input['notif_id'] ?? 0);

if ($notifId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid notification ID']);
    exit;
}

try {
    $stmt = $conn->prepare("
        UPDATE notifications 
        SET is_read = 1 
        WHERE notif_id = ? AND user_email = ?
    ");
    $stmt->execute([$notifId, getCurrentUserEmail()]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    error_log("Mark Read Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>