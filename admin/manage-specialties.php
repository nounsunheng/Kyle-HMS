<?php
/**
 * Kyle-HMS Manage Specialties
 * Add and view medical specialties
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('a');

$errors = [];

// Handle Add Specialty
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_specialty'])) {
    if (verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $icon = sanitize($_POST['icon'] ?? 'fas fa-stethoscope');
        
        if (empty($name)) {
            $errors[] = 'Specialty name is required';
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    INSERT INTO specialties (name, description, icon) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$name, $description, $icon]);
                
                logActivity('add_specialty', "Added specialty: $name");
                setFlashMessage('Specialty added successfully!', 'success');
                redirect('/admin/manage-specialties.php');
                
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $errors[] = 'Specialty name already exists';
                } else {
                    error_log("Add Specialty Error: " . $e->getMessage());
                    $errors[] = 'Failed to add specialty';
                }
            }
        }
    }
}

// Fetch all specialties
try {
    $stmt = $conn->prepare("
        SELECT s.*,
               COUNT(DISTINCT d.docid) as doctor_count
        FROM specialties s
        LEFT JOIN doctor d ON s.id = d.specialties
        GROUP BY s.id
        ORDER BY s.name ASC
    ");
    $stmt->execute();
    $specialties = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Fetch Specialties Error: " . $e->getMessage());
    $specialties = [];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Specialties - <?php echo APP_NAME; ?></title>
    
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
                
                <div class="row g-4">
                    
                    <!-- Add Specialty Form -->
                    <div class="col-lg-4">
                        <div class="admin-form-card">
                            <div class="admin-form-header">
                                <h5><i class="fas fa-plus me-2"></i> Add New Specialty</h5>
                            </div>
                            
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Specialty Name *</label>
                                    <input type="text" class="form-control" name="name" placeholder="e.g., Cardiology" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3" placeholder="Brief description..."></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Icon Class</label>
                                    <input type="text" class="form-control" name="icon" value="fas fa-stethoscope" placeholder="Font Awesome class">
                                    <small class="form-text text-muted">e.g., fas fa-heart, fas fa-brain</small>
                                </div>
                                
                                <button type="submit" name="add_specialty" class="btn btn-success w-100">
                                    <i class="fas fa-plus me-2"></i> Add Specialty
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Specialties List -->
                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-list"></i> All Specialties
                                </h5>
                                <span class="badge bg-primary"><?php echo count($specialties); ?> Total</span>
                            </div>
                            
                            <div class="row g-3 p-3">
                                <?php if (!empty($specialties)): ?>
                                    <?php foreach ($specialties as $specialty): ?>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3" style="transition: all 0.3s; cursor: pointer;" onmouseover="this.style.borderColor='#6f42c1'" onmouseout="this.style.borderColor='#dee2e6'">
                                                <div class="d-flex align-items-start">
                                                    <div class="me-3">
                                                        <div style="width: 50px; height: 50px; background: linear-gradient(135deg, #6f42c1, #563d7c); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                                            <i class="<?php echo htmlspecialchars($specialty['icon']); ?>"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($specialty['name']); ?></h6>
                                                        <?php if (!empty($specialty['description'])): ?>
                                                            <small class="text-muted"><?php echo truncateText(htmlspecialchars($specialty['description']), 60); ?></small>
                                                        <?php endif; ?>
                                                        <div class="mt-2">
                                                            <span class="badge bg-primary">
                                                                <i class="fas fa-user-md me-1"></i>
                                                                <?php echo $specialty['doctor_count']; ?> Doctor(s)
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-5">
                                        <i class="fas fa-stethoscope fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No specialties yet</p>
                                    </div>
                                <?php endif; ?>
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