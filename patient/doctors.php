<?php
/**
 * Kyle-HMS Doctors Directory
 * Browse and search doctors
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('p');

$userEmail = getCurrentUserEmail();

// Filters
$searchTerm = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$specialtyFilter = isset($_GET['specialty']) ? (int)$_GET['specialty'] : 0;

// Build query
$query = "
    SELECT 
        d.*,
        sp.name as specialty_name,
        sp.icon as specialty_icon,
        (SELECT COUNT(*) FROM schedule s 
         WHERE s.docid = d.docid 
         AND s.scheduledate >= CURDATE() 
         AND s.status = 'active' 
         AND (s.nop - s.booked) > 0) as available_slots
    FROM doctor d
    JOIN specialties sp ON d.specialties = sp.id
    WHERE d.status = 'active'
";

$params = [];

if (!empty($searchTerm)) {
    $query .= " AND (d.docname LIKE ? OR sp.name LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

if ($specialtyFilter > 0) {
    $query .= " AND d.specialties = ?";
    $params[] = $specialtyFilter;
}

$query .= " ORDER BY d.docname ASC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $doctors = $stmt->fetchAll();
    
    // Get all specialties for filter
    $stmt = $conn->prepare("SELECT * FROM specialties ORDER BY name ASC");
    $stmt->execute();
    $specialties = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Doctors Page Error: " . $e->getMessage());
    $doctors = [];
    $specialties = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Doctors - <?php echo APP_NAME; ?></title>
    
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
                
                <!-- Search and Filter Section -->
                <div class="content-card mb-4">
                    <form method="GET" action="" class="row g-3">
                        <div class="col-md-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" 
                                       class="form-control" 
                                       name="search" 
                                       placeholder="Search by doctor name or specialty..."
                                       value="<?php echo htmlspecialchars($searchTerm); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" name="specialty">
                                <option value="0">All Specialties</option>
                                <?php foreach ($specialties as $spec): ?>
                                    <option value="<?php echo $spec['id']; ?>" 
                                            <?php echo ($specialtyFilter == $spec['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($spec['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i> Filter
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- Results Count -->
                <div class="mb-3">
                    <p class="text-muted">
                        Found <strong><?php echo count($doctors); ?></strong> doctor(s)
                        <?php if (!empty($searchTerm) || $specialtyFilter > 0): ?>
                            <a href="doctors.php" class="ms-2 text-primary">
                                <i class="fas fa-times"></i> Clear filters
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
                
                <!-- Doctors Grid -->
                <?php if (!empty($doctors)): ?>
                    <div class="row g-4">
                        <?php foreach ($doctors as $doctor): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="doctor-card">
                                    <div class="doctor-header">
                                        <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($doctor['profile_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($doctor['docname']); ?>"
                                             class="doctor-avatar"
                                             onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                                        <h5 class="doctor-name"><?php echo htmlspecialchars($doctor['docname']); ?></h5>
                                        <p class="doctor-specialty">
                                            <i class="<?php echo htmlspecialchars($doctor['specialty_icon']); ?> me-1"></i>
                                            <?php echo htmlspecialchars($doctor['specialty_name']); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="doctor-info">
                                        <div class="info-item">
                                            <i class="fas fa-graduation-cap"></i>
                                            <span><?php echo htmlspecialchars($doctor['docdegree']); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-briefcase"></i>
                                            <span><?php echo $doctor['docexperience']; ?> years experience</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-phone"></i>
                                            <span><?php echo htmlspecialchars(formatPhone($doctor['doctel'])); ?></span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span>$<?php echo number_format($doctor['docconsultation_fee'], 2); ?> / consultation</span>
                                        </div>
                                        
                                        <?php if ($doctor['available_slots'] > 0): ?>
                                            <div class="alert alert-success py-2 px-3 mb-0 mt-2">
                                                <i class="fas fa-check-circle me-1"></i>
                                                <small><strong><?php echo $doctor['available_slots']; ?></strong> slots available</small>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning py-2 px-3 mb-0 mt-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <small>No slots available</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (!empty($doctor['docbio'])): ?>
                                        <div class="mt-3">
                                            <p class="small text-muted mb-0">
                                                <?php echo truncateText(htmlspecialchars($doctor['docbio']), 100); ?>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="doctor-actions">
                                        <button class="btn btn-sm btn-outline-primary flex-fill" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#doctorModal<?php echo $doctor['docid']; ?>">
                                            <i class="fas fa-info-circle me-1"></i> View Details
                                        </button>
                                        <?php if ($doctor['available_slots'] > 0): ?>
                                            <a href="book-appointment.php?doctor=<?php echo $doctor['docid']; ?>" 
                                               class="btn btn-sm btn-primary flex-fill">
                                                <i class="fas fa-calendar-plus me-1"></i> Book Now
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Doctor Details Modal -->
                            <div class="modal fade" id="doctorModal<?php echo $doctor['docid']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Doctor Profile</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="text-center mb-4">
                                                <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($doctor['profile_image']); ?>" 
                                                     alt="<?php echo htmlspecialchars($doctor['docname']); ?>"
                                                     class="rounded-circle"
                                                     style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #4361ee;"
                                                     onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                                                <h4 class="mt-3 mb-1"><?php echo htmlspecialchars($doctor['docname']); ?></h4>
                                                <p class="text-primary fw-bold">
                                                    <i class="<?php echo htmlspecialchars($doctor['specialty_icon']); ?> me-1"></i>
                                                    <?php echo htmlspecialchars($doctor['specialty_name']); ?>
                                                </p>
                                            </div>
                                            
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                                        <i class="fas fa-graduation-cap fa-2x text-primary me-3"></i>
                                                        <div>
                                                            <small class="text-muted">Qualification</small>
                                                            <div class="fw-bold"><?php echo htmlspecialchars($doctor['docdegree']); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                                        <i class="fas fa-briefcase fa-2x text-success me-3"></i>
                                                        <div>
                                                            <small class="text-muted">Experience</small>
                                                            <div class="fw-bold"><?php echo $doctor['docexperience']; ?> years</div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                                        <i class="fas fa-phone fa-2x text-info me-3"></i>
                                                        <div>
                                                            <small class="text-muted">Phone</small>
                                                            <div class="fw-bold"><?php echo htmlspecialchars(formatPhone($doctor['doctel'])); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                                        <i class="fas fa-dollar-sign fa-2x text-warning me-3"></i>
                                                        <div>
                                                            <small class="text-muted">Consultation Fee</small>
                                                            <div class="fw-bold">$<?php echo number_format($doctor['docconsultation_fee'], 2); ?></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php if (!empty($doctor['docbio'])): ?>
                                                <div class="mb-3">
                                                    <h6 class="fw-bold mb-2">About</h6>
                                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($doctor['docbio'])); ?></p>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Availability:</strong> 
                                                <?php if ($doctor['available_slots'] > 0): ?>
                                                    <?php echo $doctor['available_slots']; ?> appointment slots available
                                                <?php else: ?>
                                                    Currently no available slots. Please check back later.
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <?php if ($doctor['available_slots'] > 0): ?>
                                                <a href="book-appointment.php?doctor=<?php echo $doctor['docid']; ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-calendar-plus me-2"></i> Book Appointment
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="content-card">
                        <div class="empty-state">
                            <i class="fas fa-user-md empty-state-icon"></i>
                            <h4>No Doctors Found</h4>
                            <p>We couldn't find any doctors matching your search criteria.</p>
                            <a href="doctors.php" class="btn btn-primary">
                                <i class="fas fa-redo me-2"></i> View All Doctors
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
</body>
</html>