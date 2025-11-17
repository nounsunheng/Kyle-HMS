<?php
/**
 * Kyle-HMS Patient Dashboard
 * Main dashboard for patient portal
 */
require_once '../config/config.php';
require_once '../config/session.php';

// Require patient login
requireLogin('p');

$userEmail = getCurrentUserEmail();
$userId = getUserId($userEmail, 'p');
$userName = getUserFullName($userEmail, 'p');

// Fetch patient info
try {
    $stmt = $conn->prepare("SELECT * FROM patient WHERE pemail = ?");
    $stmt->execute([$userEmail]);
    $patient = $stmt->fetch();
    
    // Get statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_appointments,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM appointment 
        WHERE pid = ?
    ");
    $stmt->execute([$userId]);
    $stats = $stmt->fetch();
    
    // Get upcoming appointments
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            d.docname,
            d.profile_image as doc_image,
            s.title as schedule_title,
            sp.name as specialty_name
        FROM appointment a
        JOIN schedule s ON a.scheduleid = s.scheduleid
        JOIN doctor d ON s.docid = d.docid
        JOIN specialties sp ON d.specialties = sp.id
        WHERE a.pid = ? 
        AND a.appodate >= CURDATE()
        AND a.status IN ('pending', 'confirmed')
        ORDER BY a.appodate ASC, a.appotime ASC
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $upcomingAppointments = $stmt->fetchAll();
    
    // Get recent medical records
    $stmt = $conn->prepare("
        SELECT 
            mr.*,
            d.docname,
            sp.name as specialty_name
        FROM medical_records mr
        JOIN doctor d ON mr.docid = d.docid
        JOIN specialties sp ON d.specialties = sp.id
        WHERE mr.pid = ?
        ORDER BY mr.record_date DESC
        LIMIT 3
    ");
    $stmt->execute([$userId]);
    $recentRecords = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    setFlashMessage('Error loading dashboard data', 'error');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
</head>
<body>
    
    <div class="dashboard-wrapper">
        
        <!-- Sidebar -->
        <?php include '../includes/patient_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            
            <!-- Top Navbar -->
            <?php include '../includes/patient_navbar.php'; ?>
            
            <!-- Content Area -->
            <div class="content-area">
                
                <!-- Welcome Section -->
                <div class="welcome-section mb-4">
                    <h2>Welcome back, <?php echo htmlspecialchars($patient['pname']); ?>! 👋</h2>
                    <p class="text-muted">Here's what's happening with your health today.</p>
                </div>
                
                <!-- Flash Messages -->
                <?php displayFlashMessage(); ?>
                
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card primary">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $stats['total_appointments'] ?? 0; ?></div>
                                    <div class="stat-label">Total Appointments</div>
                                </div>
                                <div class="stat-icon primary">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $stats['pending'] ?? 0; ?></div>
                                    <div class="stat-label">Pending</div>
                                </div>
                                <div class="stat-icon warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $stats['confirmed'] ?? 0; ?></div>
                                    <div class="stat-label">Confirmed</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $stats['completed'] ?? 0; ?></div>
                                    <div class="stat-label">Completed</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-check-double"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-bolt"></i> Quick Actions
                                </h5>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <a href="doctors.php" class="btn btn-outline-primary w-100 py-3">
                                        <i class="fas fa-user-md d-block fs-3 mb-2"></i>
                                        Find Doctors
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="book-appointment.php" class="btn btn-outline-success w-100 py-3">
                                        <i class="fas fa-calendar-plus d-block fs-3 mb-2"></i>
                                        Book Appointment
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="appointments.php" class="btn btn-outline-info w-100 py-3">
                                        <i class="fas fa-calendar-check d-block fs-3 mb-2"></i>
                                        My Appointments
                                    </a>
                                </div>
                                <div class="col-md-3">
                                    <a href="medical-records.php" class="btn btn-outline-warning w-100 py-3">
                                        <i class="fas fa-file-medical d-block fs-3 mb-2"></i>
                                        Medical Records
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    
                    <!-- Upcoming Appointments -->
                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-calendar-alt"></i> Upcoming Appointments
                                </h5>
                                <a href="appointments.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <?php if (!empty($upcomingAppointments)): ?>
                                <div class="table-responsive">
                                    <table class="custom-table table">
                                        <thead>
                                            <tr>
                                                <th>Appointment #</th>
                                                <th>Doctor</th>
                                                <th>Specialty</th>
                                                <th>Date & Time</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcomingAppointments as $appt): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($appt['appointment_number']); ?></strong>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($appt['doc_image']); ?>" 
                                                                 alt="Doctor" 
                                                                 class="rounded-circle me-2" 
                                                                 style="width: 35px; height: 35px; object-fit: cover;">
                                                            <span><?php echo htmlspecialchars($appt['docname']); ?></span>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($appt['specialty_name']); ?></td>
                                                    <td>
                                                        <div>
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?php echo formatDate($appt['appodate'], 'M d, Y'); ?>
                                                        </div>
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock me-1"></i>
                                                            <?php echo formatTime($appt['appotime']); ?>
                                                        </small>
                                                    </td>
                                                    <td><?php echo getStatusBadge($appt['status']); ?></td>
                                                    <td>
                                                        <a href="appointment-details.php?id=<?php echo $appt['appoid']; ?>" 
                                                           class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times empty-state-icon"></i>
                                    <h4>No Upcoming Appointments</h4>
                                    <p>You don't have any scheduled appointments at the moment.</p>
                                    <a href="book-appointment.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i> Book New Appointment
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Medical Records -->
                    <div class="col-lg-4">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-file-medical"></i> Recent Records
                                </h5>
                                <a href="medical-records.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            
                            <?php if (!empty($recentRecords)): ?>
                                <div class="records-list">
                                    <?php foreach ($recentRecords as $record): ?>
                                        <div class="record-item mb-3 p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($record['docname']); ?></h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($record['specialty_name']); ?></small>
                                                </div>
                                                <span class="badge bg-primary"><?php echo formatDate($record['record_date'], 'M d'); ?></span>
                                            </div>
                                            <p class="mb-2 small">
                                                <strong>Diagnosis:</strong> 
                                                <?php echo truncateText(htmlspecialchars($record['diagnosis']), 60); ?>
                                            </p>
                                            <a href="medical-records.php?id=<?php echo $record['record_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary w-100">
                                                <i class="fas fa-eye me-1"></i> View Details
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state py-4">
                                    <i class="fas fa-file-medical empty-state-icon"></i>
                                    <p class="mb-0 text-muted">No medical records yet</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
    
</body>
</html>