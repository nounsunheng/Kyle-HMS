<?php
/**
 * Kyle-HMS Doctor Dashboard
 * Main overview for doctors
 */
require_once '../config/config.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

requireLogin('d');

$userEmail = getCurrentUserEmail();
$doctorId = getUserId($userEmail, 'd');
$doctorName = getUserFullName($userEmail, 'd');

// Handle appointment confirmation from dashboard
if (isset($_POST['confirm_appointment']) && isset($_POST['appointment_id'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $appointmentId = (int)$_POST['appointment_id'];
        
        try {
            // Verify appointment belongs to this doctor
            $stmt = $conn->prepare("
                SELECT a.* FROM appointment a
                JOIN schedule s ON a.scheduleid = s.scheduleid
                WHERE a.appoid = ? AND s.docid = ? AND a.status = 'pending'
            ");
            $stmt->execute([$appointmentId, $doctorId]);
            $appt = $stmt->fetch();
            
            if ($appt) {
                $stmt = $conn->prepare("UPDATE appointment SET status = 'confirmed' WHERE appoid = ?");
                $stmt->execute([$appointmentId]);
                
                logActivity('confirm_appointment', "Confirmed appointment #{$appt['appointment_number']}");
                
                setFlashMessage('Appointment confirmed successfully', 'success');
            }
        } catch (PDOException $e) {
            error_log("Confirm Error: " . $e->getMessage());
            setFlashMessage('Error confirming appointment', 'error');
        }
        
        redirect('/doctor/dashboard.php');
    }
}

// Fetch doctor info
try {
    $stmt = $conn->prepare("
        SELECT d.*, sp.name as specialty_name 
        FROM doctor d 
        JOIN specialties sp ON d.specialties = sp.id 
        WHERE d.docemail = ?
    ");
    $stmt->execute([$userEmail]);
    $doctor = $stmt->fetch();
    
    // Get statistics
    $stmt = $conn->prepare("
        SELECT 
            COUNT(DISTINCT a.appoid) as total_appointments,
            COUNT(DISTINCT CASE WHEN a.appodate = CURDATE() THEN a.appoid END) as today_appointments,
            COUNT(DISTINCT a.pid) as total_patients,
            COUNT(DISTINCT CASE WHEN a.status = 'pending' THEN a.appoid END) as pending_appointments
        FROM appointment a
        JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ?
    ");
    $stmt->execute([$doctorId]);
    $stats = $stmt->fetch();
    
    // Today's appointments - Fixed: Added a.status explicitly
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            p.pname,
            p.ptel,
            p.pdob,
            p.pgender,
            p.profile_image as patient_image,
            s.scheduletime,
            s.title as schedule_title
        FROM appointment a
        JOIN schedule s ON a.scheduleid = s.scheduleid
        JOIN patient p ON a.pid = p.pid
        WHERE s.docid = ?
        AND a.appodate = CURDATE()
        AND a.status IN ('pending', 'confirmed')
        ORDER BY s.scheduletime ASC
    ");
    $stmt->execute([$doctorId]);
    $todayAppointments = $stmt->fetchAll();
    
    // Upcoming schedules
    $stmt = $conn->prepare("
        SELECT 
            s.*,
            (s.nop - s.booked) as available_slots,
            COUNT(a.appoid) as confirmed_count
        FROM schedule s
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid AND a.status = 'confirmed'
        WHERE s.docid = ?
        AND s.scheduledate >= CURDATE()
        AND s.status = 'active'
        GROUP BY s.scheduleid
        ORDER BY s.scheduledate ASC, s.scheduletime ASC
        LIMIT 5
    ");
    $stmt->execute([$doctorId]);
    $upcomingSchedules = $stmt->fetchAll();
    
    // Recent patients
    $stmt = $conn->prepare("
        SELECT DISTINCT
            p.*,
            MAX(a.appodate) as last_visit
        FROM patient p
        JOIN appointment a ON p.pid = a.pid
        JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ?
        AND a.status = 'completed'
        GROUP BY p.pid
        ORDER BY last_visit DESC
        LIMIT 5
    ");
    $stmt->execute([$doctorId]);
    $recentPatients = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Doctor Dashboard Error: " . $e->getMessage());
    $stats = ['total_appointments' => 0, 'today_appointments' => 0, 'total_patients' => 0, 'pending_appointments' => 0];
    $todayAppointments = [];
    $upcomingSchedules = [];
    $recentPatients = [];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/doctor.css">
</head>
<body class="doctor-dashboard">
    
    <div class="dashboard-wrapper">
        <?php include '../includes/doctor_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/doctor_navbar.php'; ?>
            
            <div class="content-area">
                
                <!-- Welcome Section -->
                <div class="welcome-section mb-4">
                    <h2>Welcome back, Dr. <?php echo htmlspecialchars($doctor['docname']); ?>! 👨‍⚕️</h2>
                    <p class="text-muted">
                        <i class="fas fa-stethoscope me-1"></i> <?php echo htmlspecialchars($doctor['specialty_name']); ?> •
                        <span class="status-pill active ms-2">
                            <i class="fas fa-circle"></i> Active
                        </span>
                    </p>
                </div>
                
                <?php displayFlashMessage(); ?>
                
                <!-- Quick Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="stat-card primary">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $stats['today_appointments']; ?></div>
                                    <div class="stat-label">Today's Appointments</div>
                                </div>
                                <div class="stat-icon primary">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $stats['pending_appointments']; ?></div>
                                    <div class="stat-label">Pending Approvals</div>
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
                                    <div class="stat-value"><?php echo $stats['total_patients']; ?></div>
                                    <div class="stat-label">Total Patients</div>
                                </div>
                                <div class="stat-icon success">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="stat-card primary">
                            <div class="stat-card-header">
                                <div>
                                    <div class="stat-value"><?php echo $stats['total_appointments']; ?></div>
                                    <div class="stat-label">All Time</div>
                                </div>
                                <div class="stat-icon primary">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="quick-actions-bar mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Quick Actions</h5>
                            <p class="mb-0 opacity-75">Manage your schedule and appointments efficiently</p>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="schedule.php" class="quick-action-btn">
                                <i class="fas fa-calendar-plus me-2"></i> Create Schedule
                            </a>
                            <a href="appointments.php" class="quick-action-btn">
                                <i class="fas fa-list me-2"></i> View All
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4">
                    
                    <!-- Today's Appointments -->
                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-calendar-day"></i> Today's Schedule
                                </h5>
                                <span class="badge bg-primary"><?php echo date('l, F j, Y'); ?></span>
                            </div>
                            
                            <?php if (!empty($todayAppointments)): ?>
                                <div class="schedule-timeline">
                                    <?php foreach ($todayAppointments as $appt): ?>
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="d-flex align-items-center flex-grow-1">
                                                        <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($appt['patient_image']); ?>" 
                                                             alt="Patient" 
                                                             class="patient-avatar-small me-3"
                                                             onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo htmlspecialchars($appt['pname']); ?></h6>
                                                            <div class="small text-muted">
                                                                <i class="fas fa-phone me-1"></i>
                                                                <?php echo htmlspecialchars(formatPhone($appt['ptel'])); ?> •
                                                                <?php echo calculateAge($appt['pdob']); ?> years •
                                                                <?php echo ucfirst($appt['pgender']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="time-slot">
                                                            <i class="fas fa-clock"></i>
                                                            <?php echo formatTime($appt['scheduletime']); ?>
                                                        </div>
                                                        <div class="mt-2">
                                                            <?php echo getStatusBadge($appt['status']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <?php if (!empty($appt['symptoms'])): ?>
                                                    <div class="mt-2 p-2 bg-light rounded">
                                                        <small class="text-muted"><strong>Symptoms:</strong></small>
                                                        <p class="mb-0 small"><?php echo truncateText(htmlspecialchars($appt['symptoms']), 100); ?></p>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="mt-3 d-flex gap-2">
                                                    <?php if ($appt['status'] === 'pending'): ?>
                                                        <form method="POST" action="" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                            <input type="hidden" name="appointment_id" value="<?php echo $appt['appoid']; ?>">
                                                            <button type="submit" name="confirm_appointment" class="btn btn-sm btn-success">
                                                                <i class="fas fa-check me-1"></i> Confirm
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    <a href="appointments.php" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> Details
                                                    </a>
                                                    <?php if ($appt['status'] === 'confirmed'): ?>
                                                        <button class="btn btn-sm btn-primary">
                                                            <i class="fas fa-notes-medical me-1"></i> Add Record
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times empty-state-icon"></i>
                                    <h4>No Appointments Today</h4>
                                    <p>You have no scheduled appointments for today. Enjoy your day!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        
                        <!-- Upcoming Schedules -->
                        <div class="content-card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-calendar-alt"></i> Upcoming Schedules
                                </h6>
                            </div>
                            
                            <?php if (!empty($upcomingSchedules)): ?>
                                <?php foreach ($upcomingSchedules as $schedule): ?>
                                    <div class="schedule-card <?php echo ($schedule['available_slots'] == 0) ? 'full' : ''; ?>">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($schedule['title']); ?></h6>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    <?php echo formatDate($schedule['scheduledate'], 'M d, Y'); ?>
                                                </small>
                                            </div>
                                            <div class="time-slot" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                                                <?php echo formatTime($schedule['scheduletime']); ?>
                                            </div>
                                        </div>
                                        
                                        <div class="capacity-bar mb-1">
                                            <div class="capacity-fill" style="width: <?php echo ($schedule['booked'] / $schedule['nop']) * 100; ?>%"></div>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="capacity-text">
                                                <?php echo $schedule['booked']; ?> / <?php echo $schedule['nop']; ?> booked
                                            </small>
                                            <?php if ($schedule['available_slots'] > 0): ?>
                                                <small class="text-success fw-bold">
                                                    <?php echo $schedule['available_slots']; ?> available
                                                </small>
                                            <?php else: ?>
                                                <small class="text-danger fw-bold">Fully Booked</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <a href="schedule.php" class="btn btn-outline-primary btn-sm w-100 mt-2">
                                    <i class="fas fa-calendar-plus me-2"></i> Manage Schedule
                                </a>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-3">No upcoming schedules</p>
                                    <a href="schedule.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-2"></i> Create Schedule
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Recent Patients -->
                        <div class="content-card">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-user-injured"></i> Recent Patients
                                </h6>
                            </div>
                            
                            <?php if (!empty($recentPatients)): ?>
                                <?php foreach ($recentPatients as $patient): ?>
                                    <div class="patient-info-card mb-2">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($patient['profile_image']); ?>" 
                                                 alt="Patient" 
                                                 class="patient-avatar-small me-2"
                                                 onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                                            <div class="flex-grow-1">
                                                <div class="fw-bold"><?php echo htmlspecialchars($patient['pname']); ?></div>
                                                <small class="text-muted">
                                                    Last visit: <?php echo formatDate($patient['last_visit'], 'M d'); ?>
                                                </small>
                                            </div>
                                            <a href="patients.php?id=<?php echo $patient['pid']; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                
                                <a href="patients.php" class="btn btn-outline-primary btn-sm w-100 mt-2">
                                    <i class="fas fa-users me-2"></i> View All Patients
                                </a>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-user-injured fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0 small">No patients yet</p>
                                </div>
                            <?php endif; ?>
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