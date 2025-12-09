<?php
/**
 * Kyle-HMS Manage Doctors
 * Add, edit, view, delete doctors
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('a');

$errors = [];

// Handle Add Doctor
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $email = sanitize($_POST['docemail'] ?? '');
        $name = sanitize($_POST['docname'] ?? '');
        $phone = sanitize($_POST['doctel'] ?? '');
        $specialty = (int)($_POST['specialties'] ?? 0);
        $degree = sanitize($_POST['docdegree'] ?? '');
        $experience = (int)($_POST['docexperience'] ?? 0);
        $fee = (float)($_POST['docconsultation_fee'] ?? 0);
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($email) || !isValidEmail($email)) {
            $errors[] = 'Valid email is required';
        } elseif (emailExists($email)) {
            $errors[] = 'Email already exists';
        }
        
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($phone)) $errors[] = 'Phone is required';
        if ($specialty <= 0) $errors[] = 'Specialty is required';
        if (empty($degree)) $errors[] = 'Degree is required';
        
        $passwordValidation = validatePassword($password);
        if (!$passwordValidation['valid']) {
            $errors[] = $passwordValidation['message'];
        }
        
        if (empty($errors)) {
            try {
                $conn->beginTransaction();
                
                // Insert webuser
                $stmt = $conn->prepare("INSERT INTO webuser (email, usertype, password) VALUES (?, 'd', ?)");
                $stmt->execute([$email, hashPassword($password)]);
                
                // Insert doctor
                $stmt = $conn->prepare("
                    INSERT INTO doctor (docemail, docname, doctel, specialties, docdegree, docexperience, docconsultation_fee)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$email, $name, $phone, $specialty, $degree, $experience, $fee]);
                
                $conn->commit();
                
                logActivity('add_doctor', "Added new doctor: $name");
                setFlashMessage('Doctor added successfully!', 'success');
                redirect('/admin/manage-doctors.php');
                
            } catch (PDOException $e) {
                $conn->rollBack();
                error_log("Add Doctor Error: " . $e->getMessage());
                $errors[] = 'Failed to add doctor';
            }
        }
    }
}

// Handle Delete Doctor
if (isset($_GET['delete']) && verifyCSRFToken($_GET['token'] ?? '')) {
    $doctorId = (int)$_GET['delete'];
    
    try {
        // Check for existing appointments
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM appointment a
            JOIN schedule s ON a.scheduleid = s.scheduleid
            WHERE s.docid = ?
        ");
        $stmt->execute([$doctorId]);
        $apptCount = $stmt->fetch()['count'];
        
        if ($apptCount > 0) {
            setFlashMessage("Cannot delete doctor with existing appointments ($apptCount)", 'error');
        } else {
            // Get email first
            $stmt = $conn->prepare("SELECT docemail FROM doctor WHERE docid = ?");
            $stmt->execute([$doctorId]);
            $doctor = $stmt->fetch();
            
            if ($doctor) {
                // Delete will cascade due to foreign keys
                $stmt = $conn->prepare("DELETE FROM webuser WHERE email = ?");
                $stmt->execute([$doctor['docemail']]);
                
                logActivity('delete_doctor', "Deleted doctor ID: $doctorId");
                setFlashMessage('Doctor deleted successfully', 'success');
            }
        }
    } catch (PDOException $e) {
        error_log("Delete Doctor Error: " . $e->getMessage());
        setFlashMessage('Error deleting doctor', 'error');
    }
    
    redirect('/admin/manage-doctors.php');
}

// Fetch all doctors
$searchTerm = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "
    SELECT d.*, sp.name as specialty_name,
           (SELECT COUNT(*) FROM schedule s WHERE s.docid = d.docid) as total_schedules,
           (SELECT COUNT(DISTINCT a.pid) FROM appointment a 
            JOIN schedule s ON a.scheduleid = s.scheduleid 
            WHERE s.docid = d.docid) as total_patients
    FROM doctor d
    JOIN specialties sp ON d.specialties = sp.id
    WHERE 1=1
";

$params = [];

if (!empty($searchTerm)) {
    $query .= " AND (d.docname LIKE ? OR d.docemail LIKE ? OR sp.name LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

$query .= " ORDER BY d.created_at DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $doctors = $stmt->fetchAll();
    
    // Get specialties for dropdown
    $stmt = $conn->prepare("SELECT * FROM specialties ORDER BY name ASC");
    $stmt->execute();
    $specialties = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Fetch Doctors Error: " . $e->getMessage());
    $doctors = [];
    $specialties = [];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - <?php echo APP_NAME; ?></title>
    
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
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- Action Bar -->
                <div class="row g-3 mb-4">
                    <div class="col-md-8">
                        <form method="GET" action="" class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search doctors..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4 text-end">
                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                            <i class="fas fa-plus me-2"></i> Add New Doctor
                        </button>
                    </div>
                </div>
                
                <!-- Doctors Table -->
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialty</th>
                                <th>Contact</th>
                                <th>Experience</th>
                                <th>Fee</th>
                                <th>Stats</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($doctors)): ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($doctor['profile_image']); ?>" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;"
                                                     onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($doctor['docname']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($doctor['docdegree']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($doctor['specialty_name']); ?></td>
                                        <td>
                                            <div><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($doctor['docemail']); ?></div>
                                            <div><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars(formatPhone($doctor['doctel'])); ?></div>
                                        </td>
                                        <td><?php echo $doctor['docexperience']; ?> years</td>
                                        <td>$<?php echo number_format($doctor['docconsultation_fee'], 2); ?></td>
                                        <td>
                                            <small class="d-block"><?php echo $doctor['total_schedules']; ?> schedules</small>
                                            <small class="d-block"><?php echo $doctor['total_patients']; ?> patients</small>
                                        </td>
                                        <td>
                                            <?php if ($doctor['status'] === 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo ucfirst($doctor['status']); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn view" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $doctor['docid']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="?delete=<?php echo $doctor['docid']; ?>&token=<?php echo $csrfToken; ?>" 
                                                   class="action-btn delete"
                                                   onclick="return confirm('Are you sure? This will delete the doctor and all related schedules.');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?php echo $doctor['docid']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Doctor Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-12 text-center">
                                                            <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($doctor['profile_image']); ?>" 
                                                                 class="rounded-circle mb-3" 
                                                                 style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #6f42c1;"
                                                                 onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                                                            <h5><?php echo htmlspecialchars($doctor['docname']); ?></h5>
                                                            <p class="text-muted"><?php echo htmlspecialchars($doctor['specialty_name']); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Email:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['docemail']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Phone:</strong><br>
                                                            <?php echo htmlspecialchars(formatPhone($doctor['doctel'])); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Qualification:</strong><br>
                                                            <?php echo htmlspecialchars($doctor['docdegree']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Experience:</strong><br>
                                                            <?php echo $doctor['docexperience']; ?> years
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Consultation Fee:</strong><br>
                                                            $<?php echo number_format($doctor['docconsultation_fee'], 2); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Status:</strong><br>
                                                            <?php echo ucfirst($doctor['status']); ?>
                                                        </div>
                                                        <?php if (!empty($doctor['docbio'])): ?>
                                                            <div class="col-12">
                                                                <strong>Bio:</strong><br>
                                                                <?php echo nl2br(htmlspecialchars($doctor['docbio'])); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="col-12">
                                                            <hr>
                                                            <strong>Statistics:</strong>
                                                            <div class="row mt-2">
                                                                <div class="col-4 text-center">
                                                                    <h4><?php echo $doctor['total_schedules']; ?></h4>
                                                                    <small>Schedules</small>
                                                                </div>
                                                                <div class="col-4 text-center">
                                                                    <h4><?php echo $doctor['total_patients']; ?></h4>
                                                                    <small>Patients</small>
                                                                </div>
                                                                <div class="col-4 text-center">
                                                                    <h4><?php echo formatDate($doctor['created_at'], 'M Y'); ?></h4>
                                                                    <small>Joined</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="fas fa-user-md fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No doctors found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                    <div class="modal-header">
                        <h5 class="modal-title">Add New Doctor</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" class="form-control" name="docname" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="docemail" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="tel" class="form-control" name="doctel" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Specialty *</label>
                                <select class="form-select" name="specialties" required>
                                    <option value="">Select Specialty</option>
                                    <?php foreach ($specialties as $spec): ?>
                                        <option value="<?php echo $spec['id']; ?>"><?php echo htmlspecialchars($spec['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Qualification *</label>
                                <input type="text" class="form-control" name="docdegree" placeholder="e.g., MBBS, MD" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Experience (years)</label>
                                <input type="number" class="form-control" name="docexperience" min="0" value="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Consultation Fee</label>
                                <input type="number" class="form-control" name="docconsultation_fee" min="0" step="0.01" value="0">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Password *</label>
                                <input type="password" class="form-control" name="password" required>
                                <small class="form-text text-muted">Min 8 chars with uppercase, lowercase, number & special character</small>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_doctor" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i> Add Doctor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
</body>
</html>