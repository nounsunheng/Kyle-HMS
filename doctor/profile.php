<?php
/**
 * Kyle-HMS Doctor Profile
 * View and edit doctor profile
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('d');

$userEmail = getCurrentUserEmail();
$doctorId = getUserId($userEmail, 'd');

$errors = [];

// Fetch doctor info
try {
    $stmt = $conn->prepare("
        SELECT d.*, sp.name as specialty_name, sp.id as specialty_id
        FROM doctor d 
        JOIN specialties sp ON d.specialties = sp.id 
        WHERE d.docemail = ?
    ");
    $stmt->execute([$userEmail]);
    $doctor = $stmt->fetch();
    
    if (!$doctor) {
        setFlashMessage('Doctor record not found', 'error');
        redirect('/doctor/dashboard.php');
    }
    
    // Fetch all specialties for dropdown
    $stmt = $conn->prepare("SELECT * FROM specialties ORDER BY name ASC");
    $stmt->execute();
    $specialties = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Profile Fetch Error: " . $e->getMessage());
    setFlashMessage('Error loading profile', 'error');
    redirect('/doctor/dashboard.php');
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request';
    } else {
        $name = sanitize($_POST['docname'] ?? '');
        $phone = sanitize($_POST['doctel'] ?? '');
        $specialty = (int)($_POST['specialties'] ?? 0);
        $degree = sanitize($_POST['docdegree'] ?? '');
        $experience = (int)($_POST['docexperience'] ?? 0);
        $bio = sanitize($_POST['docbio'] ?? '');
        $fee = (float)($_POST['docconsultation_fee'] ?? 0);
        
        // Validation
        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        
        if (empty($phone) || !isValidPhone($phone)) {
            $errors[] = 'Valid phone number is required';
        }
        
        if ($specialty <= 0) {
            $errors[] = 'Please select a specialty';
        }
        
        if (empty($degree)) {
            $errors[] = 'Qualification/degree is required';
        }
        
        if ($experience < 0 || $experience > 60) {
            $errors[] = 'Experience must be between 0 and 60 years';
        }
        
        if ($fee < 0) {
            $errors[] = 'Consultation fee must be positive';
        }
        
        // Handle profile image upload
        $profileImage = $doctor['profile_image'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadImage($_FILES['profile_image'], 'avatars');
            if ($upload['success']) {
                if ($doctor['profile_image'] !== 'default-doctor.png') {
                    deleteUploadedFile($doctor['profile_image'], 'avatars');
                }
                $profileImage = $upload['filename'];
            } else {
                $errors[] = $upload['message'];
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    UPDATE doctor SET
                        docname = ?,
                        doctel = ?,
                        specialties = ?,
                        docdegree = ?,
                        docexperience = ?,
                        docbio = ?,
                        docconsultation_fee = ?,
                        profile_image = ?
                    WHERE docemail = ?
                ");
                
                $stmt->execute([
                    $name,
                    $phone,
                    $specialty,
                    $degree,
                    $experience,
                    $bio,
                    $fee,
                    $profileImage,
                    $userEmail
                ]);
                
                // Update session
                $_SESSION['name'] = $name;
                $_SESSION['image'] = $profileImage;
                
                logActivity('update_profile', 'Updated doctor profile');
                
                setFlashMessage('Profile updated successfully!', 'success');
                redirect('/doctor/profile.php');
                
            } catch (PDOException $e) {
                error_log("Profile Update Error: " . $e->getMessage());
                $errors[] = 'Failed to update profile';
            }
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request';
    } else {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        try {
            $stmt = $conn->prepare("SELECT password FROM webuser WHERE email = ?");
            $stmt->execute([$userEmail]);
            $user = $stmt->fetch();
            
            if (!verifyPassword($currentPassword, $user['password'])) {
                $errors[] = 'Current password is incorrect';
            }
            
            if (empty($newPassword)) {
                $errors[] = 'New password is required';
            } else {
                $passwordValidation = validatePassword($newPassword);
                if (!$passwordValidation['valid']) {
                    $errors[] = $passwordValidation['message'];
                }
            }
            
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'Passwords do not match';
            }
            
            if (empty($errors)) {
                $stmt = $conn->prepare("UPDATE webuser SET password = ? WHERE email = ?");
                $stmt->execute([hashPassword($newPassword), $userEmail]);
                
                logActivity('change_password', 'Changed account password');
                
                setFlashMessage('Password changed successfully!', 'success');
                redirect('/doctor/profile.php');
            }
        } catch (PDOException $e) {
            error_log("Password Change Error: " . $e->getMessage());
            $errors[] = 'Failed to change password';
        }
    }
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - <?php echo APP_NAME; ?></title>
    
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
                    
                    <!-- Profile Overview -->
                    <div class="col-lg-4">
                        <div class="content-card text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($doctor['profile_image']); ?>" 
                                     alt="Profile" 
                                     class="rounded-circle"
                                     id="profilePreview"
                                     style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #0d6efd;"
                                     onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                            </div>
                            <h4 class="mb-1"><?php echo htmlspecialchars($doctor['docname']); ?></h4>
                            <p class="text-muted mb-3">
                                <i class="fas fa-stethoscope me-1"></i> <?php echo htmlspecialchars($doctor['specialty_name']); ?>
                            </p>
                            
                            <div class="status-pill active mb-3">
                                <i class="fas fa-circle"></i> Active
                            </div>
                            
                            <div class="row g-2 text-start">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Doctor ID</small>
                                        <strong>#DOC-<?php echo str_pad($doctor['docid'], 5, '0', STR_PAD_LEFT); ?></strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Qualification</small>
                                        <strong><?php echo htmlspecialchars($doctor['docdegree']); ?></strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Experience</small>
                                        <strong><?php echo $doctor['docexperience']; ?> years</strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Consultation Fee</small>
                                        <strong>$<?php echo number_format($doctor['docconsultation_fee'], 2); ?></strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Member Since</small>
                                        <strong><?php echo formatDate($doctor['created_at'], 'F Y'); ?></strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Profile Form -->
                    <div class="col-lg-8">
                        <div class="content-card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-user-edit"></i> Edit Profile
                                </h5>
                            </div>
                            
                            <form method="POST" action="" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="profile_image" class="form-label">Profile Picture</label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="profile_image" 
                                               name="profile_image" 
                                               accept="image/*"
                                               onchange="previewImage(this)">
                                        <small class="form-text text-muted">JPG, PNG or GIF. Max size: 5MB</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="docname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="docname" 
                                               name="docname" 
                                               value="<?php echo htmlspecialchars($doctor['docname']); ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="docemail" class="form-label">Email Address</label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="docemail" 
                                               value="<?php echo htmlspecialchars($doctor['docemail']); ?>"
                                               disabled>
                                        <small class="form-text text-muted">Email cannot be changed</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="doctel" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="doctel" 
                                               name="doctel" 
                                               value="<?php echo htmlspecialchars($doctor['doctel']); ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="specialties" class="form-label">Specialty <span class="text-danger">*</span></label>
                                        <select class="form-select" id="specialties" name="specialties" required>
                                            <?php foreach ($specialties as $spec): ?>
                                                <option value="<?php echo $spec['id']; ?>" <?php echo ($doctor['specialty_id'] == $spec['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($spec['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="docdegree" class="form-label">Qualification/Degree <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="docdegree" 
                                               name="docdegree" 
                                               placeholder="e.g., MBBS, MD"
                                               value="<?php echo htmlspecialchars($doctor['docdegree']); ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="docexperience" class="form-label">Years of Experience</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="docexperience" 
                                               name="docexperience" 
                                               min="0" 
                                               max="60"
                                               value="<?php echo $doctor['docexperience']; ?>">
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <label for="docconsultation_fee" class="form-label">Consultation Fee ($)</label>
                                        <input type="number" 
                                               class="form-control" 
                                               id="docconsultation_fee" 
                                               name="docconsultation_fee" 
                                               min="0" 
                                               step="0.01"
                                               value="<?php echo $doctor['docconsultation_fee']; ?>">
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <label for="docbio" class="form-label">Professional Bio</label>
                                        <textarea class="form-control" 
                                                  id="docbio" 
                                                  name="docbio" 
                                                  rows="4" 
                                                  placeholder="Brief description about your practice, expertise, and approach to patient care..."><?php echo htmlspecialchars($doctor['docbio'] ?? ''); ?></textarea>
                                        <small class="form-text text-muted">This will be visible to patients</small>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Change Password -->
                        <div class="content-card mt-4">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fas fa-lock"></i> Change Password
                                </h5>
                            </div>
                            
                            <form method="POST" action="" id="passwordForm">
                                <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                                
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label for="current_password" class="form-label">Current Password</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="current_password" 
                                               name="current_password" 
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="new_password" class="form-label">New Password</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="new_password" 
                                               name="new_password" 
                                               required>
                                        <small class="form-text text-muted">
                                            Min 8 characters with uppercase, lowercase, number & special character
                                        </small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="confirm_password" 
                                               name="confirm_password" 
                                               required>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" name="change_password" class="btn btn-warning">
                                        <i class="fas fa-key me-2"></i> Change Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        document.getElementById('passwordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
                return false;
            }
        });
    </script>
</body>
</html>