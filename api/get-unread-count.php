<?php
/**
 * api/get-unread-count.php
 * Get unread notification count
 */
require_once '../config/config.php';
require_once '../config/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'count' => 0]);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM notifications 
        WHERE user_email = ? AND is_read = 0
    ");
    $stmt->execute([getCurrentUserEmail()]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'success' => true, 
        'count' => (int)$result['count']
    ]);
} catch (PDOException $e) {
    error_log("Get Unread Count Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'count' => 0]);
}
?>