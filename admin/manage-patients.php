<?php
/**
 * Kyle-HMS Manage Patients
 * View and manage all patients
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('a');

// Handle Delete Patient
if (isset($_GET['delete']) && verifyCSRFToken($_GET['token'] ?? '')) {
    $patientId = (int)$_GET['delete'];
    
    try {
        $stmt = $conn->prepare("SELECT pemail FROM patient WHERE pid = ?");
        $stmt->execute([$patientId]);
        $patient = $stmt->fetch();
        
        if ($patient) {
            // Delete will cascade
            $stmt = $conn->prepare("DELETE FROM webuser WHERE email = ?");
            $stmt->execute([$patient['pemail']]);
            
            logActivity('delete_patient', "Deleted patient ID: $patientId");
            setFlashMessage('Patient deleted successfully', 'success');
        }
    } catch (PDOException $e) {
        error_log("Delete Patient Error: " . $e->getMessage());
        setFlashMessage('Error deleting patient', 'error');
    }
    
    redirect('/admin/manage-patients.php');
}

// Search
$searchTerm = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "
    SELECT p.*,
           COUNT(DISTINCT a.appoid) as total_appointments,
           MAX(a.appodate) as last_visit
    FROM patient p
    LEFT JOIN appointment a ON p.pid = a.pid
    WHERE 1=1
";

$params = [];

if (!empty($searchTerm)) {
    $query .= " AND (p.pname LIKE ? OR p.pemail LIKE ? OR p.ptel LIKE ?)";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
    $params[] = "%$searchTerm%";
}

$query .= " GROUP BY p.pid ORDER BY p.created_at DESC";

try {
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Fetch Patients Error: " . $e->getMessage());
    $patients = [];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patients - <?php echo APP_NAME; ?></title>
    
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
                
                <!-- Search Bar -->
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="" class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search patients..." value="<?php echo htmlspecialchars($searchTerm); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <div class="admin-stat-card green">
                            <div class="stat-value"><?php echo count($patients); ?></div>
                            <div class="stat-label">Total Patients</div>
                        </div>
                    </div>
                </div>
                
                <!-- Patients Table -->
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Contact</th>
                                <th>Age/Gender</th>
                                <th>Blood Group</th>
                                <th>Appointments</th>
                                <th>Last Visit</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($patients)): ?>
                                <?php foreach ($patients as $patient): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($patient['profile_image']); ?>" 
                                                     class="rounded-circle me-2" 
                                                     style="width: 40px; height: 40px; object-fit: cover;"
                                                     onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($patient['pname']); ?></strong>
                                                    <br>
                                                    <small class="text-muted">PAT-<?php echo str_pad($patient['pid'], 5, '0', STR_PAD_LEFT); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div><i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($patient['pemail']); ?></div>
                                            <div><i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars(formatPhone($patient['ptel'])); ?></div>
                                        </td>
                                        <td>
                                            <?php echo calculateAge($patient['pdob']); ?> years<br>
                                            <small class="text-muted"><?php echo ucfirst($patient['pgender']); ?></small>
                                        </td>
                                        <td>
                                            <?php if ($patient['pbloodgroup']): ?>
                                                <span class="badge bg-danger"><?php echo htmlspecialchars($patient['pbloodgroup']); ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $patient['total_appointments']; ?></td>
                                        <td>
                                            <?php if ($patient['last_visit']): ?>
                                                <?php echo formatDate($patient['last_visit'], 'M d, Y'); ?>
                                            <?php else: ?>
                                                <span class="text-muted">No visits</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo formatDate($patient['created_at'], 'M d, Y'); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="action-btn view" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $patient['pid']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <a href="?delete=<?php echo $patient['pid']; ?>&token=<?php echo $csrfToken; ?>" 
                                                   class="action-btn delete"
                                                   onclick="return confirm('Are you sure? This will delete all patient data and appointments.');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- View Modal -->
                                    <div class="modal fade" id="viewModal<?php echo $patient['pid']; ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Patient Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-12 text-center">
                                                            <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($patient['profile_image']); ?>" 
                                                                 class="rounded-circle mb-3" 
                                                                 style="width: 100px; height: 100px; object-fit: cover; border: 4px solid #198754;">
                                                            <h5><?php echo htmlspecialchars($patient['pname']); ?></h5>
                                                            <p class="text-muted">PAT-<?php echo str_pad($patient['pid'], 5, '0', STR_PAD_LEFT); ?></p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Email:</strong><br>
                                                            <?php echo htmlspecialchars($patient['pemail']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Phone:</strong><br>
                                                            <?php echo htmlspecialchars(formatPhone($patient['ptel'])); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Date of Birth:</strong><br>
                                                            <?php echo formatDate($patient['pdob']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Age:</strong><br>
                                                            <?php echo calculateAge($patient['pdob']); ?> years
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Gender:</strong><br>
                                                            <?php echo ucfirst($patient['pgender']); ?>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <strong>Blood Group:</strong><br>
                                                            <?php echo $patient['pbloodgroup'] ?: 'Not specified'; ?>
                                                        </div>
                                                        <div class="col-12">
                                                            <strong>Address:</strong><br>
                                                            <?php echo htmlspecialchars($patient['paddress']); ?>
                                                        </div>
                                                        <?php if ($patient['pemergency_name'] && $patient['pemergency_contact']): ?>
                                                            <div class="col-12">
                                                                <strong>Emergency Contact:</strong><br>
                                                                <?php echo htmlspecialchars($patient['pemergency_name']); ?> - 
                                                                <?php echo htmlspecialchars(formatPhone($patient['pemergency_contact'])); ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="col-12">
                                                            <hr>
                                                            <div class="row">
                                                                <div class="col-6 text-center">
                                                                    <h4><?php echo $patient['total_appointments']; ?></h4>
                                                                    <small>Total Appointments</small>
                                                                </div>
                                                                <div class="col-6 text-center">
                                                                    <h4><?php echo formatDate($patient['created_at'], 'M Y'); ?></h4>
                                                                    <small>Member Since</small>
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
                                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No patients found</p>
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