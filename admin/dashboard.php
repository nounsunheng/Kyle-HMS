<?php
/**
 * Kyle-HMS Admin Dashboard
 * System overview and statistics
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('a');

$userEmail = getCurrentUserEmail();

// Fetch system statistics
try {
    // Total counts
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM doctor WHERE status = 'active'");
    $stmt->execute();
    $totalDoctors = $stmt->fetch()['count'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM patient");
    $stmt->execute();
    $totalPatients = $stmt->fetch()['count'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM appointment");
    $stmt->execute();
    $totalAppointments = $stmt->fetch()['count'];
    
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM specialties");
    $stmt->execute();
    $totalSpecialties = $stmt->fetch()['count'];
    
    // Today's stats
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as today_appointments,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM appointment 
        WHERE appodate = CURDATE()
    ");
    $stmt->execute();
    $todayStats = $stmt->fetch();
    
    // Recent appointments
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            p.pname,
            d.docname,
            s.scheduledate,
            s.scheduletime,
            sp.name as specialty_name
        FROM appointment a
        JOIN patient p ON a.pid = p.pid
        JOIN schedule s ON a.scheduleid = s.scheduleid
        JOIN doctor d ON s.docid = d.docid
        JOIN specialties sp ON d.specialties = sp.id
        ORDER BY a.created_at DESC
        LIMIT 10
    ");
    $stmt->execute();
    $recentAppointments = $stmt->fetchAll();
    
    // Recent registrations
    $stmt = $conn->prepare("
        SELECT p.*, 'patient' as type FROM patient 
        ORDER BY created_at DESC LIMIT 5
    ");
    $stmt->execute();
    $recentPatients = $stmt->fetchAll();
    
    // Monthly stats for chart
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(appodate, '%Y-%m') as month,
            COUNT(*) as count
        FROM appointment
        WHERE appodate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month
    ");
    $stmt->execute();
    $monthlyStats = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Admin Dashboard Error: " . $e->getMessage());
    $totalDoctors = $totalPatients = $totalAppointments = $totalSpecialties = 0;
    $todayStats = ['today_appointments' => 0, 'pending' => 0, 'confirmed' => 0, 'completed' => 0];
    $recentAppointments = [];
    $recentPatients = [];
    $monthlyStats = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo APP_NAME; ?></title>
    
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
                
                <!-- Welcome Section -->
                <div class="welcome-section mb-4">
                    <h2>Welcome back, Administrator! 👔</h2>
                    <p class="text-muted">System Overview - <?php echo date('l, F j, Y'); ?></p>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Main Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="admin-stat-card purple">
                            <div class="admin-stat-icon purple">
                                <i class="fas fa-user-md"></i>
                            </div>
                            <div class="stat-value"><?php echo $totalDoctors; ?></div>
                            <div class="stat-label">Total Doctors</div>
                            <a href="manage-doctors.php" class="btn btn-sm btn-outline-primary mt-2">
                                Manage <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="admin-stat-card green">
                            <div class="admin-stat-icon green">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-value"><?php echo $totalPatients; ?></div>
                            <div class="stat-label">Total Patients</div>
                            <a href="manage-patients.php" class="btn btn-sm btn-outline-success mt-2">
                                Manage <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="admin-stat-card blue">
                            <div class="admin-stat-icon blue">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-value"><?php echo $totalAppointments; ?></div>
                            <div class="stat-label">Total Appointments</div>
                            <a href="manage-appointments.php" class="btn btn-sm btn-outline-info mt-2">
                                View All <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="admin-stat-card orange">
                            <div class="admin-stat-icon orange">
                                <i class="fas fa-stethoscope"></i>
                            </div>
                            <div class="stat-value"><?php echo $totalSpecialties; ?></div>
                            <div class="stat-label">Specialties</div>
                            <a href="manage-specialties.php" class="btn btn-sm btn-outline-warning mt-2">
                                Manage <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Today's Overview -->
                <div class="content-card mb-4" style="background: linear-gradient(135deg, #6f42c1 0%, #563d7c 100%); color: white;">
                    <h5 class="mb-3">
                        <i class="fas fa-calendar-day me-2"></i> Today's Activity
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-white bg-opacity-10 rounded">
                                <div class="h2 mb-0"><?php echo $todayStats['today_appointments']; ?></div>
                                <small>Total Today</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-white bg-opacity-10 rounded">
                                <div class="h2 mb-0"><?php echo $todayStats['pending']; ?></div>
                                <small>Pending</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-white bg-opacity-10 rounded">
                                <div class="h2 mb-0"><?php echo $todayStats['confirmed']; ?></div>
                                <small>Confirmed</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-white bg-opacity-10 rounded">
                                <div class="h2 mb-0"><?php echo $todayStats['completed']; ?></div>
                                <small>Completed</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    
                    <!-- Recent Appointments -->
                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-calendar-alt"></i> Recent Appointments
                                </h5>
                                <a href="manage-appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <?php if (!empty($recentAppointments)): ?>
                                <div class="table-responsive">
                                    <table class="admin-table table">
                                        <thead>
                                            <tr>
                                                <th>Appt #</th>
                                                <th>Patient</th>
                                                <th>Doctor</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($recentAppointments, 0, 5) as $appt): ?>
                                                <tr>
                                                    <td><strong class="text-primary"><?php echo htmlspecialchars($appt['appointment_number']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($appt['pname']); ?></td>
                                                    <td><?php echo htmlspecialchars($appt['docname']); ?></td>
                                                    <td><?php echo formatDate($appt['scheduledate'], 'M d, Y'); ?></td>
                                                    <td><?php echo getStatusBadge($appt['status']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-muted">No recent appointments</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Patients & Activity -->
                    <div class="col-lg-4">
                        <div class="content-card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-user-plus"></i> Recent Registrations
                                </h6>
                            </div>
                            
                            <?php if (!empty($recentPatients)): ?>
                                <div class="activity-feed">
                                    <?php foreach ($recentPatients as $patient): ?>
                                        <div class="activity-item">
                                            <div class="activity-icon success">
                                                <i class="fas fa-user-plus"></i>
                                            </div>
                                            <div class="activity-content">
                                                <div class="activity-title">
                                                    <?php echo htmlspecialchars($patient['pname']); ?>
                                                </div>
                                                <div class="activity-time">
                                                    Registered <?php echo formatDate($patient['created_at'], 'M d, g:i A'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <p class="text-muted small">No recent registrations</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="content-card mt-4">
                            <h6 class="mb-3"><i class="fas fa-bolt me-2"></i> Quick Actions</h6>
                            <div class="d-grid gap-2">
                                <a href="manage-doctors.php?action=add" class="btn btn-outline-primary">
                                    <i class="fas fa-plus me-2"></i> Add New Doctor
                                </a>
                                <a href="manage-specialties.php?action=add" class="btn btn-outline-success">
                                    <i class="fas fa-plus me-2"></i> Add Specialty
                                </a>
                                <a href="reports.php" class="btn btn-outline-info">
                                    <i class="fas fa-chart-bar me-2"></i> Generate Report
                                </a>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
</body>
</html>