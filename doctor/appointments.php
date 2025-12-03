<?php
/**
 * Kyle-HMS Doctor Appointments
 * View and manage all appointments
 */

require_once '../config/config.php';
require_once '../config/session.php';
require_once '../includes/functions.php';

requireLogin('d');

$userEmail = getCurrentUserEmail();
$doctorId = getUserId($userEmail, 'd');

// FIXED: Handle appointment cancellation
if (isset($_POST['cancel_appointment'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $appointmentId = (int)$_POST['appointment_id'];
        $cancellationReason = trim($_POST['cancellation_reason'] ?? '');
        
        if (empty($cancellationReason)) {
            setFlashMessage('Please provide a reason for cancellation', 'error');
            redirect('/doctor/appointments.php');
        }
        
        try {
            $conn->beginTransaction();
            
            $stmt = $conn->prepare("
                SELECT a.*, p.pname, p.pemail, s.scheduledate, s.scheduletime
                FROM appointment a
                JOIN patient p ON a.pid = p.pid
                JOIN schedule s ON a.scheduleid = s.scheduleid
                WHERE a.appoid = ? AND s.docid = ? AND a.status IN ('pending', 'confirmed')
            ");
            $stmt->execute([$appointmentId, $doctorId]);
            $appt = $stmt->fetch();
            
            if ($appt) {
                $stmt = $conn->prepare("
                    UPDATE appointment 
                    SET status = 'cancelled', 
                        cancellation_reason = ?,
                        cancelled_at = NOW()
                    WHERE appoid = ?
                ");
                $stmt->execute([$cancellationReason, $appointmentId]);
                
                $stmt = $conn->prepare("
                    INSERT INTO notifications (user_email, title, message, type)
                    VALUES (?, ?, ?, 'cancellation')
                ");
                $message = "Your appointment #{$appt['appointment_number']} on " . 
                           formatDate($appt['scheduledate'], 'F j, Y') . " at " . 
                           formatTime($appt['scheduletime']) . " has been cancelled by the doctor. Reason: " . 
                           $cancellationReason;
                $stmt->execute([$appt['pemail'], 'Appointment Cancelled', $message]);
                
                $conn->commit();
                logActivity('cancel_appointment', "Cancelled appointment #{$appt['appointment_number']}");
                setFlashMessage('Appointment cancelled successfully. Patient has been notified.', 'success');
            } else {
                $conn->rollBack();
                setFlashMessage('Appointment not found or cannot be cancelled', 'error');
            }
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Cancel Error: " . $e->getMessage());
            setFlashMessage('Error cancelling appointment: ' . $e->getMessage(), 'error');
        }
        
        redirect('/doctor/appointments.php');
        exit;
    }
}

// Handle status update
if (isset($_POST['update_status'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $appointmentId = (int)$_POST['appointment_id'];
        $newStatus = sanitize($_POST['status']);
        
        try {
            $stmt = $conn->prepare("
                SELECT a.*, p.pemail FROM appointment a
                JOIN patient p ON a.pid = p.pid
                JOIN schedule s ON a.scheduleid = s.scheduleid
                WHERE a.appoid = ? AND s.docid = ?
            ");
            $stmt->execute([$appointmentId, $doctorId]);
            $appt = $stmt->fetch();
            
            if ($appt) {
                $stmt = $conn->prepare("UPDATE appointment SET status = ? WHERE appoid = ?");
                $stmt->execute([$newStatus, $appointmentId]);
                
                $statusText = ucfirst($newStatus);
                $stmt = $conn->prepare("
                    INSERT INTO notifications (user_email, title, message, type)
                    VALUES (?, ?, ?, 'appointment')
                ");
                $stmt->execute([
                    $appt['pemail'],
                    'Appointment Status Updated',
                    "Your appointment #{$appt['appointment_number']} status has been updated to: {$statusText}"
                ]);
                
                logActivity('update_appointment_status', "Updated appointment #{$appt['appointment_number']} to $newStatus");
                setFlashMessage('Appointment status updated successfully', 'success');
            }
        } catch (PDOException $e) {
            error_log("Status Update Error: " . $e->getMessage());
            setFlashMessage('Error updating status', 'error');
        }
        
        redirect('/doctor/appointments.php');
        exit;
    }
}

// FIXED: Handle adding medical record with diagnosis
if (isset($_POST['add_medical_record'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $appointmentId = (int)$_POST['appointment_id'];
        $diagnosis = trim($_POST['diagnosis'] ?? '');
        $prescription = trim($_POST['prescription'] ?? '');
        $testResults = trim($_POST['test_results'] ?? '');
        $notes = trim($_POST['medical_notes'] ?? '');
        $followUpDate = !empty($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null;
        
        if (empty($diagnosis)) {
            setFlashMessage('Diagnosis is required', 'error');
            redirect('/doctor/appointments.php');
            exit;
        }
        
        try {
            $conn->beginTransaction();
            
            $stmt = $conn->prepare("
                SELECT a.pid, p.pemail 
                FROM appointment a
                JOIN patient p ON a.pid = p.pid
                JOIN schedule s ON a.scheduleid = s.scheduleid
                WHERE a.appoid = ? AND s.docid = ?
            ");
            $stmt->execute([$appointmentId, $doctorId]);
            $appt = $stmt->fetch();
            
            if ($appt) {
                $stmt = $conn->prepare("
                    INSERT INTO medical_records (
                        pid, appoid, docid, diagnosis, prescription, 
                        test_results, notes, follow_up_date, record_date
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURDATE())
                ");
                $stmt->execute([
                    $appt['pid'], $appointmentId, $doctorId,
                    $diagnosis, $prescription, $testResults, $notes, $followUpDate
                ]);
                
                $stmt = $conn->prepare("UPDATE appointment SET status = 'completed' WHERE appoid = ?");
                $stmt->execute([$appointmentId]);
                
                $stmt = $conn->prepare("
                    INSERT INTO notifications (user_email, title, message, type)
                    VALUES (?, ?, ?, 'appointment')
                ");
                $stmt->execute([
                    $appt['pemail'],
                    'Medical Record Added',
                    'Your doctor has added medical records for your recent appointment. You can view them in your medical records section.'
                ]);
                
                $conn->commit();
                logActivity('add_medical_record', "Added medical record for appointment ID: $appointmentId");
                setFlashMessage('Medical record added successfully', 'success');
            } else {
                $conn->rollBack();
                setFlashMessage('Appointment not found', 'error');
            }
        } catch (Exception $e) {
            $conn->rollBack();
            error_log("Medical Record Error: " . $e->getMessage());
            setFlashMessage('Error saving medical record: ' . $e->getMessage(), 'error');
        }
        
        redirect('/doctor/appointments.php');
        exit;
    }
}

// Filters
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : 'all';
$dateFilter = isset($_GET['date']) ? sanitize($_GET['date']) : '';

// Build query
$query = "
    SELECT a.*, p.pname, p.ptel, p.pdob, p.pgender, p.paddress, p.pbloodgroup,
           p.profile_image as patient_image, s.title as schedule_title,
           s.scheduledate, s.scheduletime
    FROM appointment a
    JOIN schedule s ON a.scheduleid = s.scheduleid
    JOIN patient p ON a.pid = p.pid
    WHERE s.docid = ?
";

$params = [$doctorId];

if ($statusFilter === 'today') {
    $query .= " AND a.appodate = CURDATE()";
} elseif ($statusFilter === 'cancelled') {
    $query .= " AND a.status = 'cancelled'";
} elseif ($statusFilter !== 'all') {
    $query .= " AND a.status = ?";
    $params[] = $statusFilter;
}

if (!empty($dateFilter)) {
    $query .= " AND a.appodate = ?";
    $params[] = $dateFilter;
}

$query .= " ORDER BY a.appodate DESC, a.appotime DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $appointments = $stmt->fetchAll();
    
    // Auto-expire past appointments
    $stmt = $conn->prepare("
        UPDATE appointment 
        SET status = 'cancelled', cancellation_reason = 'Expired - No show'
        WHERE appodate < CURDATE() 
        AND status IN ('pending', 'confirmed')
    ");
    $stmt->execute();
    
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total,
            SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN a.status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
            SUM(CASE WHEN a.appodate = CURDATE() THEN 1 ELSE 0 END) as today
        FROM appointment a
        JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ?
    ");
    $stmt->execute([$doctorId]);
    $counts = $stmt->fetch();
} catch (PDOException $e) {
    error_log("Appointments Error: " . $e->getMessage());
    $appointments = [];
    $counts = ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0, 'today' => 0];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - <?php echo APP_NAME; ?></title>
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
                
                <!-- Filter Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <a href="?status=all" style="text-decoration:none">
                            <div class="stat-card <?php echo $statusFilter === 'all' ? 'primary' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['total']; ?></div>
                                <div class="stat-label">All</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=today" style="text-decoration:none">
                            <div class="stat-card <?php echo $statusFilter === 'today' ? 'primary' : ''; ?>" style="border-color:#0dcaf0">
                                <div class="stat-value"><?php echo $counts['today']; ?></div>
                                <div class="stat-label">Today</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=pending" style="text-decoration:none">
                            <div class="stat-card <?php echo $statusFilter === 'pending' ? 'warning' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['pending']; ?></div>
                                <div class="stat-label">Pending</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=confirmed" style="text-decoration:none">
                            <div class="stat-card <?php echo $statusFilter === 'confirmed' ? 'success' : ''; ?>" style="border-color:#0dcaf0">
                                <div class="stat-value"><?php echo $counts['confirmed']; ?></div>
                                <div class="stat-label">Confirmed</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=completed" style="text-decoration:none">
                            <div class="stat-card <?php echo $statusFilter === 'completed' ? 'success' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['completed']; ?></div>
                                <div class="stat-label">Completed</div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-2">
                        <a href="?status=cancelled" style="text-decoration:none">
                            <div class="stat-card <?php echo $statusFilter === 'cancelled' ? 'danger' : ''; ?>">
                                <div class="stat-value"><?php echo $counts['cancelled']; ?></div>
                                <div class="stat-label">Cancelled</div>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- Date Filter Row -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="dateFilter" value="<?php echo htmlspecialchars($dateFilter); ?>" onchange="filterByDate(this.value)">
                    </div>
                    <?php if (!empty($dateFilter) || $statusFilter !== 'all'): ?>
                        <div class="col-md-3">
                            <a href="appointments.php" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Clear Filters
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Appointments List -->
                <div class="content-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-calendar-check"></i> Appointments
                            <?php if ($statusFilter !== 'all'): ?>
                                <span class="badge bg-secondary"><?php echo ucfirst($statusFilter); ?></span>
                            <?php endif; ?>
                        </h5>
                    </div>
                    
                    <?php if (!empty($appointments)): ?>
                        <?php foreach ($appointments as $appt): 
                            $isExpired = ($appt['appodate'] < date('Y-m-d') && in_array($appt['status'], ['pending', 'confirmed']));
                        ?>
                            <div class="appointment-card <?php echo ($appt['appodate'] == date('Y-m-d')) ? 'today' : 'upcoming'; ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo UPLOADS_URL.'/avatars/'.htmlspecialchars($appt['patient_image']); ?>" 
                                                 alt="Patient" class="patient-avatar-small me-3"
                                                 onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($appt['pname']); ?></h6>
                                                <small class="text-muted">
                                                    <?php echo calculateAge($appt['pdob']); ?> yrs • <?php echo ucfirst($appt['pgender']); ?>
                                                    <?php if ($appt['pbloodgroup']): ?> • <?php echo htmlspecialchars($appt['pbloodgroup']); ?><?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div>
                                            <small class="text-muted d-block">Appointment #</small>
                                            <strong class="text-primary"><?php echo htmlspecialchars($appt['appointment_number']); ?></strong>
                                        </div>
                                        <div class="mt-1">
                                            <small class="text-muted">
                                                <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars(formatPhone($appt['ptel'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="time-slot" style="display:inline-block">
                                            <i class="fas fa-calendar"></i><?php echo formatDate($appt['appodate'], 'M d'); ?>
                                        </div>
                                        <div class="mt-1">
                                            <small><i class="fas fa-clock me-1"></i><?php echo formatTime($appt['appotime']); ?></small>
                                        </div>
                                        <?php if ($isExpired): ?>
                                            <div class="mt-1">
                                                <span class="badge bg-secondary">Expired</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-2 text-center"><?php echo getStatusBadge($appt['status']); ?></div>
                                    <div class="col-md-2 text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" data-bs-toggle="modal" 
                                                    data-bs-target="#detailsModal<?php echo $appt['appoid']; ?>" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <?php if ($appt['status'] === 'pending' && !$isExpired): ?>
                                                <button class="btn btn-success" 
                                                        onclick="updateStatus(<?php echo $appt['appoid']; ?>,'confirmed')" title="Confirm">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($appt['status'] === 'confirmed' && !$isExpired): ?>
                                                <button class="btn btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#medicalRecordModal<?php echo $appt['appoid']; ?>"
                                                        title="Add Medical Record">
                                                    <i class="fas fa-notes-medical"></i>
                                                </button>
                                                <button class="btn btn-info" 
                                                        onclick="updateStatus(<?php echo $appt['appoid']; ?>,'completed')" title="Mark Complete">
                                                    <i class="fas fa-check-double"></i>
                                                </button>
                                            <?php endif; ?>
                                            <?php if (in_array($appt['status'], ['pending','confirmed']) && !$isExpired): ?>
                                                <button class="btn btn-danger" data-bs-toggle="modal"
                                                        data-bs-target="#cancelModal<?php echo $appt['appoid']; ?>"
                                                        title="Cancel Appointment">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if (!empty($appt['symptoms'])): ?>
                                    <div class="mt-3 p-3 bg-light rounded">
                                        <small class="text-muted"><strong>Chief Complaint:</strong></small>
                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($appt['symptoms'])); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($appt['cancellation_reason']) && $appt['status'] === 'cancelled'): ?>
                                    <div class="mt-3 p-3 bg-danger bg-opacity-10 border border-danger rounded">
                                        <small class="text-danger"><strong>Cancellation Reason:</strong></small>
                                        <p class="mb-0 text-danger"><?php echo nl2br(htmlspecialchars($appt['cancellation_reason'])); ?></p>
                                        <?php if ($appt['cancelled_at']): ?>
                                            <small class="text-muted d-block mt-1">
                                                Cancelled on: <?php echo formatDate($appt['cancelled_at'], 'F j, Y g:i A'); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Details Modal -->
                            <div class="modal fade" id="detailsModal<?php echo $appt['appoid']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Patient Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="text-center mb-3">
                                                        <img src="<?php echo UPLOADS_URL.'/avatars/'.htmlspecialchars($appt['patient_image']); ?>" 
                                                             style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid #007bff"
                                                             onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                                                        <h5 class="mt-2 mb-0"><?php echo htmlspecialchars($appt['pname']); ?></h5>
                                                        <small class="text-muted">Patient ID: PAT-<?php echo str_pad($appt['pid'],5,'0',STR_PAD_LEFT); ?></small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border rounded p-3">
                                                        <small class="text-muted">Age</small>
                                                        <div class="fw-bold"><?php echo calculateAge($appt['pdob']); ?> years</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border rounded p-3">
                                                        <small class="text-muted">Gender</small>
                                                        <div class="fw-bold"><?php echo ucfirst($appt['pgender']); ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border rounded p-3">
                                                        <small class="text-muted">Blood Group</small>
                                                        <div class="fw-bold"><?php echo $appt['pbloodgroup'] ?: 'Not specified'; ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border rounded p-3">
                                                        <small class="text-muted">Phone</small>
                                                        <div class="fw-bold"><?php echo htmlspecialchars(formatPhone($appt['ptel'])); ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="border rounded p-3">
                                                        <small class="text-muted">Address</small>
                                                        <div><?php echo htmlspecialchars($appt['paddress']); ?></div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12"><hr><h6>Appointment Information</h6></div>
                                                <div class="col-md-12">
                                                    <div class="alert alert-info mb-0">
                                                        <div class="row">
                                                            <div class="col-6"><strong>Appointment #:</strong> <?php echo htmlspecialchars($appt['appointment_number']); ?></div>
                                                            <div class="col-6"><strong>Status:</strong> <?php echo getStatusBadge($appt['status']); ?></div>
                                                            <div class="col-6 mt-2"><strong>Date:</strong> <?php echo formatDate($appt['appodate']); ?></div>
                                                            <div class="col-6 mt-2"><strong>Time:</strong> <?php echo formatTime($appt['appotime']); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if (!empty($appt['symptoms'])): ?>
                                                    <div class="col-md-12">
                                                        <div class="border rounded p-3 bg-light">
                                                            <strong>Symptoms:</strong>
                                                            <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($appt['symptoms'])); ?></p>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- FIXED: Cancel Modal -->
                            <?php if (in_array($appt['status'], ['pending','confirmed']) && !$isExpired): ?>
                                <div class="modal fade" id="cancelModal<?php echo $appt['appoid']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['appoid']; ?>">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Cancel Appointment</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Warning:</strong> This will cancel the appointment and notify the patient.
                                                    </div>
                                                    <div class="border rounded p-3 mb-3 bg-light">
                                                        <strong>Appointment:</strong> <?php echo htmlspecialchars($appt['appointment_number']); ?><br>
                                                        <strong>Patient:</strong> <?php echo htmlspecialchars($appt['pname']); ?><br>
                                                        <strong>Date & Time:</strong> <?php echo formatDate($appt['appodate']); ?> at <?php echo formatTime($appt['appotime']); ?>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Reason for Cancellation <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea class="form-control" name="cancellation_reason" rows="4" 
                                                                  placeholder="Please provide a detailed reason. The patient will see this message." required></textarea>
                                                        <small class="text-muted">This reason will be sent to the patient via notification.</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Appointment</button>
                                                    <button type="submit" name="cancel_appointment" class="btn btn-danger">
                                                        <i class="fas fa-times me-2"></i> Cancel Appointment
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- FIXED: Medical Record Modal WITH DIAGNOSIS FIELD -->
                            <?php if ($appt['status'] === 'confirmed' && !$isExpired): ?>
                                <div class="modal fade" id="medicalRecordModal<?php echo $appt['appoid']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <form method="POST" action="">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                                <input type="hidden" name="appointment_id" value="<?php echo $appt['appoid']; ?>">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title"><i class="fas fa-notes-medical me-2"></i>Add Medical Record</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-info">
                                                        <i class="fas fa-info-circle me-2"></i>
                                                        <strong>Patient:</strong> <?php echo htmlspecialchars($appt['pname']); ?> •
                                                        <strong>Date:</strong> <?php echo formatDate($appt['appodate']); ?>
                                                    </div>
                                                    <div class="row g-3">
                                                        <!-- DIAGNOSIS FIELD - THIS WAS MISSING! -->
                                                        <div class="col-md-12">
                                                            <label class="form-label">
                                                                <i class="fas fa-diagnoses me-1 text-danger"></i> 
                                                                <strong>Diagnosis</strong> <span class="text-danger">*</span>
                                                            </label>
                                                            <textarea class="form-control" name="diagnosis" rows="3" 
                                                                      placeholder="Enter primary diagnosis and relevant medical findings..." 
                                                                      required></textarea>
                                                            <small class="text-danger">This field is required</small>
                                                        </div>
                                                        
                                                        <div class="col-md-12">
                                                            <label class="form-label">
                                                                <i class="fas fa-pills me-1"></i> Prescription / Medication
                                                            </label>
                                                            <textarea class="form-control" name="prescription" rows="4" 
                                                                      placeholder="List medications with dosages and instructions...&#10;Example:&#10;1. Amoxicillin 500mg - 3 times daily for 7 days&#10;2. Ibuprofen 400mg - As needed for pain"></textarea>
                                                        </div>
                                                        
                                                        <div class="col-md-12">
                                                            <label class="form-label">
                                                                <i class="fas fa-flask me-1"></i> Test Results / Lab Reports
                                                            </label>
                                                            <textarea class="form-control" name="test_results" rows="3" 
                                                                      placeholder="Enter test results, lab values, imaging findings..."></textarea>
                                                        </div>
                                                        
                                                        <div class="col-md-12">
                                                            <label class="form-label">
                                                                <i class="fas fa-sticky-note me-1"></i> Additional Notes
                                                            </label>
                                                            <textarea class="form-control" name="medical_notes" rows="3" 
                                                                      placeholder="Any additional observations, recommendations, lifestyle advice..."></textarea>
                                                        </div>
                                                        
                                                        <div class="col-md-6">
                                                            <label class="form-label">
                                                                <i class="fas fa-calendar-check me-1"></i> Follow-up Date (Optional)
                                                            </label>
                                                            <input type="date" class="form-control" name="follow_up_date" 
                                                                   min="<?php echo date('Y-m-d'); ?>">
                                                            <small class="text-muted">Recommended date for next visit</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="alert alert-success mt-3 mb-0">
                                                        <i class="fas fa-check-circle me-2"></i>
                                                        <strong>Note:</strong> Saving this record will mark appointment as "Completed" and notify the patient.
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="fas fa-times me-2"></i>Cancel
                                                    </button>
                                                    <button type="submit" name="add_medical_record" class="btn btn-primary">
                                                        <i class="fas fa-save me-2"></i> Save Medical Record
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times empty-state-icon"></i>
                            <h4>No Appointments Found</h4>
                            <p>No appointments match your current filter criteria.</p>
                            <a href="appointments.php" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i> View All
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Update Form (Hidden) -->
    <form id="statusForm" method="POST" action="" style="display:none">
        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
        <input type="hidden" name="appointment_id" id="statusAppointmentId">
        <input type="hidden" name="status" id="statusValue">
        <input type="hidden" name="update_status" value="1">
    </form>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
    <script>
        function updateStatus(appointmentId, status) {
            const statusText = status === 'confirmed' ? 'confirm' : status === 'completed' ? 'mark as completed' : 'update status of';
            if (confirm('Are you sure you want to ' + statusText + ' this appointment?')) {
                document.getElementById('statusAppointmentId').value = appointmentId;
                document.getElementById('statusValue').value = status;
                document.getElementById('statusForm').submit();
            }
        }
        
        function filterByDate(date) {
            const currentUrl = new URL(window.location.href);
            if (date) {
                currentUrl.searchParams.set('date', date);
            } else {
                currentUrl.searchParams.delete('date');
            }
            window.location.href = currentUrl.toString();
        }
        
        // Prevent double form submission
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"][name]');
                if (submitBtn && !submitBtn.disabled) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                    
                    // Re-enable after 5 seconds in case of error
                    setTimeout(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 5000);
                }
            });
        });
        
        // Debug: Log when forms are submitted
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                console.log('Form submitted:', this);
                console.log('Form data:', new FormData(this));
            });
        });
    </script>
</body>
</html>