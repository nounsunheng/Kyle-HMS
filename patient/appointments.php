<?php
/**
 * Kyle-HMS My Appointments
 * View all patient appointments
 */

require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('p');

$userEmail = getCurrentUserEmail();
$userId = getUserId($userEmail, 'p');

// Handle cancellation with doctor notification
if (isset($_POST['cancel_appointment']) && isset($_POST['appointment_id'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $appointmentId = (int)$_POST['appointment_id'];
        $cancellationReason = sanitize($_POST['cancellation_reason']);
        
        if (empty($cancellationReason)) {
            setFlashMessage('Please provide a reason for cancellation', 'error');
            redirect('/patient/appointments.php');
        }
        
        try {
            $conn->beginTransaction();
            
            // Check if appointment belongs to user and can be cancelled
            $stmt = $conn->prepare("
                SELECT a.*, s.scheduledate, s.scheduletime, d.docname, d.docemail
                FROM appointment a
                JOIN schedule s ON a.scheduleid = s.scheduleid
                JOIN doctor d ON s.docid = d.docid
                WHERE a.appoid = ? AND a.pid = ?
                AND a.status IN ('pending', 'confirmed')
            ");
            $stmt->execute([$appointmentId, $userId]);
            $appointment = $stmt->fetch();
            
            if ($appointment) {
                // Check cancellation time limit
                $appointmentDateTime = strtotime($appointment['scheduledate'] . ' ' . $appointment['scheduletime']);
                $hoursUntil = ($appointmentDateTime - time()) / 3600;
                
                if ($hoursUntil < CANCELLATION_HOURS) {
                    throw new Exception('Appointments can only be cancelled at least ' . CANCELLATION_HOURS . ' hours in advance');
                }
                
                // Update appointment status
                $stmt = $conn->prepare("
                    UPDATE appointment 
                    SET status = 'cancelled',
                        cancellation_reason = ?,
                        cancelled_at = NOW()
                    WHERE appoid = ?
                ");
                $stmt->execute([$cancellationReason, $appointmentId]);
                
                // Send notification to doctor
                $stmt = $conn->prepare("
                    INSERT INTO notifications (user_email, title, message, type)
                    VALUES (?, ?, ?, 'cancellation')
                ");
                $patientName = getUserFullName($userEmail, 'p');
                $message = "Patient {$patientName} has cancelled appointment #{$appointment['appointment_number']} scheduled for " . 
                           formatDate($appointment['scheduledate'], 'F j, Y') . " at " . 
                           formatTime($appointment['scheduletime']) . ". Reason: " . 
                           $cancellationReason;
                $stmt->execute([
                    $appointment['docemail'],
                    'Appointment Cancelled by Patient',
                    $message
                ]);
                
                $conn->commit();
                
                logActivity('cancel_appointment', 'Cancelled appointment #' . $appointment['appointment_number']);
                
                setFlashMessage('Appointment cancelled successfully. The doctor has been notified.', 'success');
            } else {
                throw new Exception('Appointment not found or cannot be cancelled');
            }
        } catch (Exception $e) {
            $conn->rollBack();
            setFlashMessage($e->getMessage(), 'error');
        }
        
        redirect('/patient/appointments.php');
    }
}

// Filters
$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : 'all';

// Build query
$query = "
    SELECT 
        a.*,
        d.docname,
        d.doctel,
        d.profile_image as doc_image,
        sp.name as specialty_name,
        sp.icon as specialty_icon,
        s.title as schedule_title
    FROM appointment a
    JOIN schedule s ON a.scheduleid = s.scheduleid
    JOIN doctor d ON s.docid = d.docid
    JOIN specialties sp ON d.specialties = sp.id
    WHERE a.pid = ?
";

$params = [$userId];

if ($statusFilter !== 'all') {
    $query .= " AND a.status = ?";
    $params[] = $statusFilter;
}

$query .= " ORDER BY a.appodate DESC, a.appotime DESC";

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
        WHERE pid = ?
    ");
    $stmt->execute([$userId]);
    $counts = $stmt->fetch();
    
} catch (PDOException $e) {
    error_log("Appointments Error: " . $e->getMessage());
    $appointments = [];
    $counts = ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
</head>
<body>
    
    <div class="dashboard-wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/patient_navbar.php'; ?>
            
            <div class="content-area">
                
                <?php displayFlashMessage(); ?>
                
                <!-- Statistics Cards -->
                <div class="row g-3 mb-4">
                    <div class="col-md-2">
                        <div class="stat-card <?php echo $statusFilter === 'all' ? 'primary' : ''; ?>" style="border-left-width: 4px;">
                            <a href="?status=all" style="text-decoration: none; color: inherit;">
                                <div class="stat-value"><?php echo $counts['total']; ?></div>
                                <div class="stat-label">Total</div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card <?php echo $statusFilter === 'pending' ? 'warning' : ''; ?>" style="border-left-width: 4px; border-color: #ffc107;">
                            <a href="?status=pending" style="text-decoration: none; color: inherit;">
                                <div class="stat-value"><?php echo $counts['pending']; ?></div>
                                <div class="stat-label">Pending</div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card <?php echo $statusFilter === 'confirmed' ? 'info' : ''; ?>" style="border-left-width: 4px; border-color: #0dcaf0;">
                            <a href="?status=confirmed" style="text-decoration: none; color: inherit;">
                                <div class="stat-value"><?php echo $counts['confirmed']; ?></div>
                                <div class="stat-label">Confirmed</div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card <?php echo $statusFilter === 'completed' ? 'success' : ''; ?>" style="border-left-width: 4px; border-color: #198754;">
                            <a href="?status=completed" style="text-decoration: none; color: inherit;">
                                <div class="stat-value"><?php echo $counts['completed']; ?></div>
                                <div class="stat-label">Completed</div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stat-card <?php echo $statusFilter === 'cancelled' ? 'danger' : ''; ?>" style="border-left-width: 4px; border-color: #dc3545;">
                            <a href="?status=cancelled" style="text-decoration: none; color: inherit;">
                                <div class="stat-value"><?php echo $counts['cancelled']; ?></div>
                                <div class="stat-label">Cancelled</div>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex align-items-center justify-content-center h-100">
                            <a href="book-appointment.php" class="btn btn-primary w-100">
                                <i class="fas fa-plus me-2"></i> New
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Appointments List -->
                <div class="content-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-calendar-check"></i> My Appointments
                            <?php if ($statusFilter !== 'all'): ?>
                                <span class="badge bg-secondary"><?php echo ucfirst($statusFilter); ?></span>
                            <?php endif; ?>
                        </h5>
                        <?php if ($statusFilter !== 'all'): ?>
                            <a href="appointments.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Clear Filter
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($appointments)): ?>
                        <div class="table-responsive">
                            <table class="custom-table table">
                                <thead>
                                    <tr>
                                        <th>Appt #</th>
                                        <th>Doctor</th>
                                        <th>Specialty</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appt): ?>
                                        <tr>
                                            <td>
                                                <strong class="text-primary"><?php echo htmlspecialchars($appt['appointment_number']); ?></strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($appt['doc_image']); ?>" 
                                                         alt="Doctor" 
                                                         class="rounded-circle me-2" 
                                                         style="width: 40px; height: 40px; object-fit: cover;"
                                                         onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                                                    <div>
                                                        <div class="fw-bold"><?php echo htmlspecialchars($appt['docname']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars(formatPhone($appt['doctel'])); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <i class="<?php echo htmlspecialchars($appt['specialty_icon']); ?> me-1"></i>
                                                <?php echo htmlspecialchars($appt['specialty_name']); ?>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-calendar me-1 text-primary"></i>
                                                    <?php echo formatDate($appt['appodate'], 'M d, Y'); ?>
                                                </div>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo formatTime($appt['appotime']); ?>
                                                </small>
                                            </td>
                                            <td><?php echo getStatusBadge($appt['status']); ?></td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#detailsModal<?php echo $appt['appoid']; ?>">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if (in_array($appt['status'], ['pending', 'confirmed'])): ?>
                                                        <?php
                                                        $appointmentDateTime = strtotime($appt['appodate'] . ' ' . $appt['appotime']);
                                                        $hoursUntil = ($appointmentDateTime - time()) / 3600;
                                                        if ($hoursUntil >= CANCELLATION_HOURS):
                                                        ?>
                                                            <button class="btn btn-outline-danger" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#cancelModal<?php echo $appt['appoid']; ?>">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        
                                        <!-- Details Modal -->
                                        <div class="modal fade" id="detailsModal<?php echo $appt['appoid']; ?>" tabindex="-1">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Appointment Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <div class="border rounded p-3">
                                                                    <small class="text-muted">Appointment Number</small>
                                                                    <h5 class="text-primary mb-0"><?php echo htmlspecialchars($appt['appointment_number']); ?></h5>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="border rounded p-3">
                                                                    <small class="text-muted">Status</small>
                                                                    <div><?php echo getStatusBadge($appt['status']); ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="border rounded p-3">
                                                                    <small class="text-muted">Date</small>
                                                                    <div class="fw-bold"><?php echo formatDate($appt['appodate'], 'F j, Y'); ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="border rounded p-3">
                                                                    <small class="text-muted">Time</small>
                                                                    <div class="fw-bold"><?php echo formatTime($appt['appotime']); ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="border rounded p-3">
                                                                    <small class="text-muted">Doctor</small>
                                                                    <div class="d-flex align-items-center mt-2">
                                                                        <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($appt['doc_image']); ?>" 
                                                                             alt="Doctor" 
                                                                             class="rounded-circle me-3" 
                                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                                        <div>
                                                                            <h6 class="mb-0"><?php echo htmlspecialchars($appt['docname']); ?></h6>
                                                                            <p class="mb-0 text-primary">
                                                                                <i class="<?php echo htmlspecialchars($appt['specialty_icon']); ?> me-1"></i>
                                                                                <?php echo htmlspecialchars($appt['specialty_name']); ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php if (!empty($appt['symptoms'])): ?>
                                                                <div class="col-md-12">
                                                                    <div class="border rounded p-3">
                                                                        <small class="text-muted">Symptoms / Reason</small>
                                                                        <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($appt['symptoms'])); ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($appt['notes'])): ?>
                                                                <div class="col-md-12">
                                                                    <div class="border rounded p-3">
                                                                        <small class="text-muted">Additional Notes</small>
                                                                        <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($appt['notes'])); ?></p>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <?php if (!empty($appt['cancellation_reason']) && $appt['status'] === 'cancelled'): ?>
                                                                <div class="col-md-12">
                                                                    <div class="border border-danger rounded p-3 bg-danger bg-opacity-10">
                                                                        <small class="text-danger"><strong>Cancellation Reason:</strong></small>
                                                                        <p class="mb-0 mt-2 text-danger"><?php echo nl2br(htmlspecialchars($appt['cancellation_reason'])); ?></p>
                                                                        <?php if ($appt['cancelled_at']): ?>
                                                                            <small class="text-muted d-block mt-2">
                                                                                Cancelled on: <?php echo formatDate($appt['cancelled_at'], 'F j, Y g:i A'); ?>
                                                                            </small>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div class="col-md-12">
                                                                <div class="border rounded p-3 bg-light">
                                                                    <small class="text-muted">Booked On</small>
                                                                    <div><?php echo formatDate($appt['created_at'], 'F j, Y g:i A'); ?></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Cancel Modal with Required Reason -->
                                        <?php if (in_array($appt['status'], ['pending', 'confirmed'])): ?>
                                            <?php
                                            $appointmentDateTime = strtotime($appt['appodate'] . ' ' . $appt['appotime']);
                                            $hoursUntil = ($appointmentDateTime - time()) / 3600;
                                            if ($hoursUntil >= CANCELLATION_HOURS):
                                            ?>
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
                                                                    <p>Are you sure you want to cancel this appointment?</p>
                                                                    <div class="alert alert-warning">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                        <strong>Appointment:</strong> <?php echo htmlspecialchars($appt['appointment_number']); ?><br>
                                                                        <strong>Doctor:</strong> <?php echo htmlspecialchars($appt['docname']); ?><br>
                                                                        <strong>Date:</strong> <?php echo formatDate($appt['appodate']); ?> at <?php echo formatTime($appt['appotime']); ?>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="cancellation_reason<?php echo $appt['appoid']; ?>" class="form-label">
                                                                            Reason for Cancellation <span class="text-danger">*</span>
                                                                        </label>
                                                                        <textarea class="form-control" 
                                                                                  id="cancellation_reason<?php echo $appt['appoid']; ?>" 
                                                                                  name="cancellation_reason" 
                                                                                  rows="4" 
                                                                                  placeholder="Please provide a reason for cancellation. The doctor will see this message."
                                                                                  required></textarea>
                                                                        <small class="text-muted">This reason will be sent to your doctor via notification.</small>
                                                                    </div>
                                                                    <div class="alert alert-info mb-0">
                                                                        <i class="fas fa-info-circle me-2"></i>
                                                                        <strong>Note:</strong> Appointments can only be cancelled at least <?php echo CANCELLATION_HOURS; ?> hours before the scheduled time.
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
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times empty-state-icon"></i>
                            <h4>No Appointments Found</h4>
                            <p>
                                <?php if ($statusFilter !== 'all'): ?>
                                    No <?php echo $statusFilter; ?> appointments found.
                                <?php else: ?>
                                    You haven't booked any appointments yet.
                                <?php endif; ?>
                            </p>
                            <a href="book-appointment.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i> Book Your First Appointment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
</body>
</html>