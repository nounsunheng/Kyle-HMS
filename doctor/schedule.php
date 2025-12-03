<?php
/**
 * Kyle-HMS Doctor Schedule Management
 * Create and manage consultation schedules
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('d');

$userEmail = getCurrentUserEmail();
$doctorId = getUserId($userEmail, 'd');

$errors = [];

// Handle schedule creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_schedule'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request';
    } else {
        $title = sanitize($_POST['title'] ?? '');
        $scheduleDate = sanitize($_POST['schedule_date'] ?? '');
        $scheduleTime = sanitize($_POST['schedule_time'] ?? '');
        $duration = (int)($_POST['duration'] ?? 30);
        $capacity = (int)($_POST['capacity'] ?? 10);
        
        // Validation
        if (empty($title)) {
            $errors[] = 'Session title is required';
        }
        
        if (empty($scheduleDate) || !isValidDate($scheduleDate)) {
            $errors[] = 'Valid date is required';
        } elseif (empty($scheduleTime) || !isValidTime($scheduleTime)) {
            $errors[] = 'Valid time is required';
        } else {
            $selectedDateTime = strtotime("$scheduleDate $scheduleTime");
            $now = time();

            if ($selectedDateTime <= $now) {
                $errors[] = 'Schedule must be in the future';
            }
        }
        
        if (empty($scheduleTime) || !isValidTime($scheduleTime)) {
            $errors[] = 'Valid time is required';
        }
        
        if ($duration < 15 || $duration > 120) {
            $errors[] = 'Duration must be between 15 and 120 minutes';
        }
        
        if ($capacity < 1 || $capacity > 50) {
            $errors[] = 'Capacity must be between 1 and 50 patients';
        }
        
        // Check for conflicts
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    SELECT scheduleid FROM schedule 
                    WHERE docid = ? 
                    AND scheduledate = ? 
                    AND scheduletime = ?
                    AND status = 'active'
                ");
                $stmt->execute([$doctorId, $scheduleDate, $scheduleTime]);
                
                if ($stmt->rowCount() > 0) {
                    $errors[] = 'You already have a schedule at this date and time';
                }
            } catch (PDOException $e) {
                error_log("Schedule Check Error: " . $e->getMessage());
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO schedule (docid, title, scheduledate, scheduletime, duration, nop, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'active')
                ");
                
                $stmt->execute([$doctorId, $title, $scheduleDate, $scheduleTime, $duration, $capacity]);
                
                logActivity('create_schedule', "Created schedule: $title on $scheduleDate");
                
                setFlashMessage('Schedule created successfully!', 'success');
                redirect('/doctor/schedule.php');
                
            } catch (PDOException $e) {
                error_log("Schedule Creation Error: " . $e->getMessage());
                $errors[] = 'Failed to create schedule';
            }
        }
    }
}

// Handle schedule deletion
if (isset($_GET['delete']) && isset($_GET['token'])) {
    if (verifyCSRFToken($_GET['token'])) {
        $scheduleId = (int)$_GET['delete'];
        
        try {
            // Check if schedule belongs to doctor and has no confirmed appointments
            $stmt = $conn->prepare("
                SELECT s.*, COUNT(a.appoid) as appointment_count
                FROM schedule s
                LEFT JOIN appointment a ON s.scheduleid = a.scheduleid AND a.status IN ('confirmed', 'completed')
                WHERE s.scheduleid = ? AND s.docid = ?
                GROUP BY s.scheduleid
            ");
            $stmt->execute([$scheduleId, $doctorId]);
            $schedule = $stmt->fetch();
            
            if ($schedule) {
                if ($schedule['appointment_count'] > 0) {
                    setFlashMessage('Cannot delete schedule with confirmed appointments', 'error');
                } else {
                    // Cancel pending appointments first
                    $stmt = $conn->prepare("
                        UPDATE appointment 
                        SET status = 'cancelled', 
                            cancellation_reason = 'Schedule cancelled by doctor'
                        WHERE scheduleid = ? AND status = 'pending'
                    ");
                    $stmt->execute([$scheduleId]);
                    
                    // Delete schedule
                    $stmt = $conn->prepare("DELETE FROM schedule WHERE scheduleid = ?");
                    $stmt->execute([$scheduleId]);
                    
                    logActivity('delete_schedule', "Deleted schedule ID: $scheduleId");
                    
                    setFlashMessage('Schedule deleted successfully', 'success');
                }
            }
        } catch (PDOException $e) {
            error_log("Schedule Delete Error: " . $e->getMessage());
            setFlashMessage('Error deleting schedule', 'error');
        }
        
        redirect('/doctor/schedule.php');
    }
}

// Fetch all schedules
try {
    $stmt = $conn->prepare("
        SELECT 
            s.*,
            (s.nop - s.booked) as available_slots,
            COUNT(DISTINCT CASE WHEN a.status = 'confirmed' THEN a.appoid END) as confirmed_count,
            COUNT(DISTINCT CASE WHEN a.status = 'pending' THEN a.appoid END) as pending_count
        FROM schedule s
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
        WHERE s.docid = ?
        GROUP BY s.scheduleid
        ORDER BY s.scheduledate DESC, s.scheduletime DESC
    ");
    $stmt->execute([$doctorId]);
    $schedules = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Schedules Fetch Error: " . $e->getMessage());
    $schedules = [];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Schedule - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/doctor.css">
</head>
<body>
    
    <div class="dashboard-wrapper">
        <?php include '../includes/doctor_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/doctor_navbar.php'; ?>
            
            <div class="content-area">
                
                <?php displayFlashMessage(); ?>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row g-4">
                    
                    <!-- Create Schedule Form -->
                    <div class="col-lg-4">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-calendar-plus"></i> Create New Schedule
                                </h5>
                            </div>
                            
                            <form method="POST" action="" id="scheduleForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                
                                <div class="mb-3">
                                    <label for="title" class="form-label">Session Title <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="title" 
                                           name="title" 
                                           placeholder="e.g., Morning Consultation"
                                           required>
                                    <small class="form-text text-muted">Brief description of the session</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="schedule_date" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="schedule_date" 
                                           name="schedule_date" 
                                           min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="schedule_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                    <input type="time" 
                                           class="form-control" 
                                           id="schedule_time" 
                                           name="schedule_time" 
                                           required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration per Patient (minutes)</label>
                                    <select class="form-select" id="duration" name="duration">
                                        <option value="15">15 minutes</option>
                                        <option value="20">20 minutes</option>
                                        <option value="30" selected>30 minutes</option>
                                        <option value="45">45 minutes</option>
                                        <option value="60">60 minutes</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="capacity" class="form-label">Number of Patients <span class="text-danger">*</span></label>
                                    <input type="number" 
                                           class="form-control" 
                                           id="capacity" 
                                           name="capacity" 
                                           min="1" 
                                           max="50" 
                                           value="10"
                                           required>
                                    <small class="form-text text-muted">Maximum patients for this session</small>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Schedule will be visible to patients for booking after creation.</small>
                                </div>
                                
                                <button type="submit" name="create_schedule" class="btn btn-primary w-100">
                                    <i class="fas fa-plus me-2"></i> Create Schedule
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Schedules List -->
                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-calendar-alt"></i> My Schedules
                                </h5>
                                <span class="badge bg-primary"><?php echo count($schedules); ?> Total</span>
                            </div>
                            
                            <?php if (!empty($schedules)): ?>
                                <?php 
                                $upcomingSchedules = array_filter($schedules, function($s) {
                                    return $s['scheduledate'] >= date('Y-m-d') && $s['status'] === 'active';
                                });
                                $pastSchedules = array_filter($schedules, function($s) {
                                    return $s['scheduledate'] < date('Y-m-d') || $s['status'] !== 'active';
                                });
                                ?>
                                
                                <?php if (!empty($upcomingSchedules)): ?>
                                    <h6 class="mb-3 mt-3">
                                        <i class="fas fa-calendar-check text-success me-2"></i>
                                        Upcoming Schedules
                                    </h6>
                                    
                                    <?php foreach ($upcomingSchedules as $schedule): ?>
                                        <div class="schedule-card <?php echo ($schedule['available_slots'] == 0) ? 'full' : ''; ?>">
                                            <div class="row align-items-center">
                                                <div class="col-md-4">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($schedule['title']); ?></h6>
                                                    <div class="d-flex gap-3 small text-muted">
                                                        <span>
                                                            <i class="fas fa-calendar me-1"></i>
                                                            <?php echo formatDate($schedule['scheduledate'], 'M d, Y'); ?>
                                                        </span>
                                                        <span>
                                                            <i class="fas fa-clock me-1"></i>
                                                            <?php echo formatTime($schedule['scheduletime']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="capacity-bar mb-1">
                                                        <div class="capacity-fill" style="width: <?php echo ($schedule['booked'] / $schedule['nop']) * 100; ?>%"></div>
                                                    </div>
                                                    <small class="capacity-text">
                                                        <?php echo $schedule['booked']; ?> / <?php echo $schedule['nop']; ?> booked
                                                    </small>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <?php if ($schedule['pending_count'] > 0): ?>
                                                        <span class="badge bg-warning">
                                                            <?php echo $schedule['pending_count']; ?> Pending
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($schedule['confirmed_count'] > 0): ?>
                                                        <span class="badge bg-success ms-1">
                                                            <?php echo $schedule['confirmed_count']; ?> Confirmed
                                                        </span>
                                                    <?php endif; ?>
                                                    <?php if ($schedule['available_slots'] > 0): ?>
                                                        <span class="badge bg-info ms-1">
                                                            <?php echo $schedule['available_slots']; ?> Available
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger ms-1">Full</span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="col-md-2 text-end">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#detailsModal<?php echo $schedule['scheduleid']; ?>">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <?php if ($schedule['booked'] == 0): ?>
                                                            <a href="?delete=<?php echo $schedule['scheduleid']; ?>&token=<?php echo $csrfToken; ?>" 
                                                               class="btn btn-outline-danger"
                                                               onclick="return confirm('Are you sure you want to delete this schedule?');">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Schedule Details Modal -->
                                        <div class="modal fade" id="detailsModal<?php echo $schedule['scheduleid']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Schedule Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-12">
                                                                <div class="p-3 bg-light rounded">
                                                                    <h6 class="mb-2"><?php echo htmlspecialchars($schedule['title']); ?></h6>
                                                                    <div class="row g-2">
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Date</small>
                                                                            <strong><?php echo formatDate($schedule['scheduledate']); ?></strong>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Time</small>
                                                                            <strong><?php echo formatTime($schedule['scheduletime']); ?></strong>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Duration per Patient</small>
                                                                            <strong><?php echo $schedule['duration']; ?> minutes</strong>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <small class="text-muted d-block">Total Capacity</small>
                                                                            <strong><?php echo $schedule['nop']; ?> patients</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-12">
                                                                <h6>Booking Status</h6>
                                                                <div class="row g-2">
                                                                    <div class="col-4">
                                                                        <div class="text-center p-2 bg-light rounded">
                                                                            <div class="h4 mb-0 text-primary"><?php echo $schedule['booked']; ?></div>
                                                                            <small class="text-muted">Booked</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <div class="text-center p-2 bg-light rounded">
                                                                            <div class="h4 mb-0 text-success"><?php echo $schedule['confirmed_count']; ?></div>
                                                                            <small class="text-muted">Confirmed</small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-4">
                                                                        <div class="text-center p-2 bg-light rounded">
                                                                            <div class="h4 mb-0 text-warning"><?php echo $schedule['pending_count']; ?></div>
                                                                            <small class="text-muted">Pending</small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="col-12">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-info-circle me-1"></i>
                                                                    Created on <?php echo formatDate($schedule['created_at'], 'F j, Y g:i A'); ?>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <a href="appointments.php" class="btn btn-primary">
                                                            <i class="fas fa-calendar-check me-2"></i> View Appointments
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                                <?php if (!empty($pastSchedules)): ?>
                                    <h6 class="mb-3 mt-4">
                                        <i class="fas fa-history text-muted me-2"></i>
                                        Past Schedules
                                    </h6>
                                    
                                    <?php foreach ($pastSchedules as $schedule): ?>
                                        <div class="schedule-card" style="opacity: 0.7;">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($schedule['title']); ?></h6>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo formatDate($schedule['scheduledate'], 'M d, Y'); ?> •
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php echo formatTime($schedule['scheduletime']); ?>
                                                    </small>
                                                </div>
                                                <div class="col-md-4">
                                                    <small>
                                                        <?php echo $schedule['booked']; ?> / <?php echo $schedule['nop']; ?> attended
                                                    </small>
                                                </div>
                                                <div class="col-md-2 text-end">
                                                    <span class="badge bg-secondary">
                                                        <?php echo ucfirst($schedule['status']); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-calendar-times empty-state-icon"></i>
                                    <h4>No Schedules Yet</h4>
                                    <p>Create your first consultation schedule to start accepting appointments.</p>
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
    
    <script>
        // Set minimum date to tomorrow
        document.getElementById('schedule_date').min = new Date(Date.now() + 86400000).toISOString().split('T')[0];
    </script>
</body>
</html>