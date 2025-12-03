<?php
/**
 * Kyle-HMS Doctor Patients
 * View all patients and their history
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('d');

$userEmail = getCurrentUserEmail();
$doctorId = getUserId($userEmail, 'd');

// Search filter
$searchTerm = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query
$query = "
    SELECT DISTINCT
        p.*,
        COUNT(DISTINCT a.appoid) as total_visits,
        MAX(a.appodate) as last_visit,
        SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_visits
    FROM patient p
    INNER JOIN appointment a ON p.pid = a.pid
    INNER JOIN schedule s ON a.scheduleid = s.scheduleid
    WHERE s.docid = ?
";

$params = [$doctorId];

if (!empty($searchTerm)) {
    $query .= " AND (p.pname LIKE ? OR p.pemail LIKE ? OR p.ptel LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

$query .= " GROUP BY p.pid ORDER BY last_visit DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $patients = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Patients Fetch Error: " . $e->getMessage());
    $patients = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Patients - <?php echo APP_NAME; ?></title>
    
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
                
                <!-- Search Bar -->
                <div class="content-card mb-4">
                    <form method="GET" action="" class="row g-3 align-items-center">
                        <div class="col-md-10">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search by name, email, or phone..."
                                       value="<?php echo htmlspecialchars($searchTerm); ?>">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Patients List -->
                <div class="content-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-users"></i> My Patients
                        </h5>
                        <span class="badge bg-primary"><?php echo count($patients); ?> Patients</span>
                    </div>
                    
                    <?php if (!empty($patients)): ?>
                        <div class="row g-4 p-3">
                            <?php foreach ($patients as $patient): ?>
                                <div class="col-lg-6">
                                    <div class="patient-info-card">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <div class="d-flex align-items-start">
                                                    <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($patient['profile_image']); ?>" 
                                                         alt="Patient" 
                                                         class="patient-avatar-large me-3"
                                                         onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                                                    <div>
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($patient['pname']); ?></h6>
                                                        <div class="small text-muted mb-2">
                                                            <div>
                                                                <i class="fas fa-id-card me-1"></i>
                                                                PAT-<?php echo str_pad($patient['pid'], 5, '0', STR_PAD_LEFT); ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-envelope me-1"></i>
                                                                <?php echo htmlspecialchars($patient['pemail']); ?>
                                                            </div>
                                                            <div>
                                                                <i class="fas fa-phone me-1"></i>
                                                                <?php echo htmlspecialchars(formatPhone($patient['ptel'])); ?>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="d-flex gap-2 flex-wrap">
                                                            <span class="badge bg-light text-dark">
                                                                <i class="fas fa-birthday-cake me-1"></i>
                                                                <?php echo calculateAge($patient['pdob']); ?> years
                                                            </span>
                                                            <span class="badge bg-light text-dark">
                                                                <i class="fas fa-venus-mars me-1"></i>
                                                                <?php echo ucfirst($patient['pgender']); ?>
                                                            </span>
                                                            <?php if ($patient['pbloodgroup']): ?>
                                                                <span class="badge bg-danger">
                                                                    <i class="fas fa-tint me-1"></i>
                                                                    <?php echo htmlspecialchars($patient['pbloodgroup']); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Total Visits</small>
                                                    <strong class="h5 text-primary"><?php echo $patient['total_visits']; ?></strong>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted d-block">Last Visit</small>
                                                    <strong><?php echo formatDate($patient['last_visit'], 'M d, Y'); ?></strong>
                                                </div>
                                                <button class="btn btn-sm btn-primary mt-2" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#patientModal<?php echo $patient['pid']; ?>">
                                                    <i class="fas fa-folder-open me-1"></i> View Records
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Patient Details Modal -->
                                <div class="modal fade" id="patientModal<?php echo $patient['pid']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Patient Medical Records</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-4">
                                                    
                                                    <!-- Patient Info Sidebar -->
                                                    <div class="col-lg-4">
                                                        <div class="text-center mb-3">
                                                            <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($patient['profile_image']); ?>" 
                                                                 class="patient-avatar-large mb-3"
                                                                 onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                                                            <h5><?php echo htmlspecialchars($patient['pname']); ?></h5>
                                                            <p class="text-muted mb-0">PAT-<?php echo str_pad($patient['pid'], 5, '0', STR_PAD_LEFT); ?></p>
                                                        </div>
                                                        
                                                        <div class="border rounded p-3 mb-2">
                                                            <small class="text-muted">Date of Birth</small>
                                                            <div class="fw-bold"><?php echo formatDate($patient['pdob']); ?></div>
                                                        </div>
                                                        
                                                        <div class="border rounded p-3 mb-2">
                                                            <small class="text-muted">Age</small>
                                                            <div class="fw-bold"><?php echo calculateAge($patient['pdob']); ?> years</div>
                                                        </div>
                                                        
                                                        <div class="border rounded p-3 mb-2">
                                                            <small class="text-muted">Gender</small>
                                                            <div class="fw-bold"><?php echo ucfirst($patient['pgender']); ?></div>
                                                        </div>
                                                        
                                                        <div class="border rounded p-3 mb-2">
                                                            <small class="text-muted">Blood Group</small>
                                                            <div class="fw-bold"><?php echo $patient['pbloodgroup'] ?: 'Not specified'; ?></div>
                                                        </div>
                                                        
                                                        <div class="border rounded p-3 mb-2">
                                                            <small class="text-muted">Phone</small>
                                                            <div class="fw-bold"><?php echo htmlspecialchars(formatPhone($patient['ptel'])); ?></div>
                                                        </div>
                                                        
                                                        <div class="border rounded p-3 mb-2">
                                                            <small class="text-muted">Address</small>
                                                            <div><?php echo htmlspecialchars($patient['paddress']); ?></div>
                                                        </div>
                                                        
                                                        <?php if ($patient['pemergency_name'] && $patient['pemergency_contact']): ?>
                                                            <div class="border rounded p-3 mb-2 bg-light">
                                                                <small class="text-muted"><strong>Emergency Contact</strong></small>
                                                                <div class="fw-bold"><?php echo htmlspecialchars($patient['pemergency_name']); ?></div>
                                                                <div><?php echo htmlspecialchars(formatPhone($patient['pemergency_contact'])); ?></div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <!-- Medical Records -->
                                                    <div class="col-lg-8">
                                                        <h6 class="mb-3">
                                                            <i class="fas fa-file-medical me-2"></i>
                                                            Medical History
                                                        </h6>
                                                        
                                                        <?php
                                                        // Fetch medical records for this patient
                                                        try {
                                                            $stmt = $conn->prepare("
                                                                SELECT * FROM medical_records 
                                                                WHERE pid = ? AND docid = ?
                                                                ORDER BY record_date DESC, created_at DESC
                                                            ");
                                                            $stmt->execute([$patient['pid'], $doctorId]);
                                                            $records = $stmt->fetchAll();
                                                            
                                                            if (!empty($records)):
                                                                foreach ($records as $record):
                                                        ?>
                                                            <div class="card mb-3">
                                                                <div class="card-header bg-light">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <strong><?php echo formatDate($record['record_date']); ?></strong>
                                                                        <span class="badge bg-primary">Visit</span>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="mb-3">
                                                                        <strong class="text-primary">
                                                                            <i class="fas fa-stethoscope me-1"></i> Diagnosis:
                                                                        </strong>
                                                                        <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?></p>
                                                                    </div>
                                                                    
                                                                    <?php if (!empty($record['prescription'])): ?>
                                                                        <div class="mb-3">
                                                                            <strong class="text-success">
                                                                                <i class="fas fa-pills me-1"></i> Prescription:
                                                                            </strong>
                                                                            <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($record['prescription'])); ?></p>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php if (!empty($record['notes'])): ?>
                                                                        <div class="mb-3">
                                                                            <strong class="text-info">
                                                                                <i class="fas fa-notes-medical me-1"></i> Notes:
                                                                            </strong>
                                                                            <p class="mb-0 mt-1"><?php echo nl2br(htmlspecialchars($record['notes'])); ?></p>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                    
                                                                    <?php if (!empty($record['follow_up_date'])): ?>
                                                                        <div class="alert alert-info mb-0">
                                                                            <i class="fas fa-calendar-day me-2"></i>
                                                                            <strong>Follow-up:</strong> <?php echo formatDate($record['follow_up_date']); ?>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                        <?php
                                                                endforeach;
                                                            else:
                                                        ?>
                                                            <div class="text-center py-5">
                                                                <i class="fas fa-file-medical fa-3x text-muted mb-3"></i>
                                                                <p class="text-muted">No medical records yet for this patient</p>
                                                            </div>
                                                        <?php
                                                            endif;
                                                        } catch (PDOException $e) {
                                                            error_log("Medical Records Error: " . $e->getMessage());
                                                            echo '<div class="alert alert-danger">Error loading medical records</div>';
                                                        }
                                                        ?>
                                                        
                                                        <!-- Appointment History -->
                                                        <h6 class="mb-3 mt-4">
                                                            <i class="fas fa-history me-2"></i>
                                                            Appointment History
                                                        </h6>
                                                        
                                                        <?php
                                                        try {
                                                            $stmt = $conn->prepare("
                                                                SELECT a.*, s.scheduletime
                                                                FROM appointment a
                                                                JOIN schedule s ON a.scheduleid = s.scheduleid
                                                                WHERE a.pid = ? AND s.docid = ?
                                                                ORDER BY a.appodate DESC, s.scheduletime DESC
                                                                LIMIT 10
                                                            ");
                                                            $stmt->execute([$patient['pid'], $doctorId]);
                                                            $appointments = $stmt->fetchAll();
                                                            
                                                            if (!empty($appointments)):
                                                        ?>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Date</th>
                                                                            <th>Time</th>
                                                                            <th>Status</th>
                                                                            <th>Symptoms</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($appointments as $appt): ?>
                                                                            <tr>
                                                                                <td><?php echo formatDate($appt['appodate'], 'M d, Y'); ?></td>
                                                                                <td><?php echo formatTime($appt['scheduletime']); ?></td>
                                                                                <td><?php echo getStatusBadge($appt['status']); ?></td>
                                                                                <td><?php echo truncateText(htmlspecialchars($appt['symptoms']), 50); ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        <?php
                                                            else:
                                                                echo '<p class="text-muted text-center">No appointment history</p>';
                                                            endif;
                                                        } catch (PDOException $e) {
                                                            error_log("Appointments History Error: " . $e->getMessage());
                                                        }
                                                        ?>
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-user-injured empty-state-icon"></i>
                            <h4>No Patients Found</h4>
                            <p>
                                <?php if (!empty($searchTerm)): ?>
                                    No patients match your search criteria.
                                <?php else: ?>
                                    You don't have any patients yet. Patients will appear here after their first appointment.
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($searchTerm)): ?>
                                <a href="patients.php" class="btn btn-primary">
                                    <i class="fas fa-redo me-2"></i> View All Patients
                                </a>
                            <?php endif; ?>
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