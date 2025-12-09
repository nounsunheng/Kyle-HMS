<?php
/**
 * Kyle-HMS Manage All Appointments
 * View system-wide appointments
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('a');

// Filters
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : 'all';
$dateFilter = isset($_GET['date']) ? sanitize($_GET['date']) : '';

$query = "
    SELECT 
        a.*,
        p.pname,
        p.pemail as patient_email,
        p.ptel as patient_phone,
        d.docname,
        d.docemail as doctor_email,
        sp.name as specialty_name,
        s.scheduledate,
        s.scheduletime
    FROM appointment a
    JOIN patient p ON a.pid = p.pid
    JOIN schedule s ON a.scheduleid = s.scheduleid
    JOIN doctor d ON s.docid = d.docid
    JOIN specialties sp ON d.specialties = sp.id
    WHERE 1=1
";

$params = [];

if ($statusFilter !== 'all') {
    $query .= " AND a.status = ?";
    $params[] = $statusFilter;
}

if (!empty($dateFilter)) {
    $query .= " AND a.appodate = ?";
    $params[] = $dateFilter;
}

$query .= " ORDER BY a.appodate DESC, a.appotime DESC LIMIT 100";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();
    
    // Get counts
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
        FROM appointment
    ");
    $stmt->execute();
    $counts = $stmt->fetch();
    
} catch (PDOException $e) {
    error_log("Appointments Error: " . $e->getMessage());
    $appointments = [];
    $counts = ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/admin.css">
</head>
<body class="admin-dashboard">
    
    <div class="dashboard-wrapper">
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/admin_navbar.php'; ?>
            
            <div class="content-area">
                
                <?php displayFlashMessage(); ?>
                
                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <a href="?status=all" style="text-decoration: none;">
                            <div class="admin-stat-card <?php echo $statusFilter === 'all' ? 'blue' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['total']; ?></div>
                                <div class="stat-label">All</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=pending" style="text-decoration: none;">
                            <div class="admin-stat-card <?php echo $statusFilter === 'pending' ? 'orange' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['pending']; ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=confirmed" style="text-decoration: none;">
                            <div class="admin-stat-card <?php echo $statusFilter === 'confirmed' ? 'blue' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['confirmed']; ?></div>
                                <div class="stat-label">Confirmed</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=completed" style="text-decoration: none;">
                            <div class="admin-stat-card <?php echo $statusFilter === 'completed' ? 'green' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['completed']; ?></div>
                                <div class="stat-label">Completed</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=cancelled" style="text-decoration: none;">
                            <div class="admin-stat-card">
                                <div class="stat-value"><?php echo $counts['cancelled']; ?></div>
                                <div class="stat-label">Cancelled</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" id="dateFilter" value="<?php echo $dateFilter; ?>" onchange="window.location.href='?date=' + this.value">
                    </div>
                </div>
                
                <!-- Appointments Table -->
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Appt #</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Specialty</th>
                                <th>Date & Time</th>
                                <th>Status</th>
                                <th>Booked On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php foreach ($appointments as $appt): ?>
                                    <tr>
                                        <td><strong class="text-primary"><?php echo htmlspecialchars($appt['appointment_number']); ?></strong></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($appt['pname']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($appt['patient_phone']); ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($appt['docname']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($appt['doctor_email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($appt['specialty_name']); ?></td>
                                        <td>
                                            <?php echo formatDate($appt['scheduledate'], 'M d, Y'); ?><br>
                                            <small><?php echo formatTime($appt['scheduletime']); ?></small>
                                        </td>
                                        <td><?php echo getStatusBadge($appt['status']); ?></td>
                                        <td><?php echo formatDate($appt['created_at'], 'M d, g:i A'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No appointments found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
</body>
</html>