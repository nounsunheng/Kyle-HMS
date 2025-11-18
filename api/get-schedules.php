<?php
/**
 * Kyle-HMS API: Get Doctor Schedules
 * Returns available schedules for a doctor
 */
header('Content-Type: application/json');

require_once '../config/config.php';
require_once '../config/session.php';

// Require login
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$doctorId = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;

if ($doctorId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid doctor ID']);
    exit;
}

try {
    $stmt = $conn->prepare("
        SELECT 
            scheduleid,
            title,
            scheduledate,
            scheduletime,
            duration,
            nop,
            booked
        FROM schedule
        WHERE docid = ?
        AND status = 'active'
        AND scheduledate >= CURDATE()
        AND (nop - booked) > 0
        ORDER BY scheduledate ASC, scheduletime ASC
    ");
    
    $stmt->execute([$doctorId]);
    $schedules = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'schedules' => $schedules
    ]);
    
} catch (PDOException $e) {
    error_log("Get Schedules API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred'
    ]);
}
?>