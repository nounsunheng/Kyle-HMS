<?php
/**
 * Kyle-HMS Patient Profile
 * View and edit profile information
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('p');

$userEmail = getCurrentUserEmail();
$userId = getUserId($userEmail, 'p');

$errors = [];
$success = false;

// Fetch patient info
try {
    $stmt = $conn->prepare("SELECT * FROM patient WHERE pemail = ?");
    $stmt->execute([$userEmail]);
    $patient = $stmt->fetch();
    
    if (!$patient) {
        setFlashMessage('Patient record not found', 'error');
        redirect('/patient/dashboard.php');
    }
} catch (PDOException $e) {
    error_log("Profile Fetch Error: " . $e->getMessage());
    setFlashMessage('Error loading profile', 'error');
    redirect('/patient/dashboard.php');
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request';
    } else {
        $name = sanitize($_POST['pname'] ?? '');
        $phone = sanitize($_POST['ptel'] ?? '');
        $address = sanitize($_POST['paddress'] ?? '');
        $bloodGroup = sanitize($_POST['pbloodgroup'] ?? '');
        $emergencyName = sanitize($_POST['pemergency_name'] ?? '');
        $emergencyContact = sanitize($_POST['pemergency_contact'] ?? '');
        
        // Validation
        if (empty($name)) {
            $errors[] = 'Name is required';
        }
        
        if (empty($phone)) {
            $errors[] = 'Phone number is required';
        } elseif (!isValidPhone($phone)) {
            $errors[] = 'Invalid phone number format';
        }
        
        if (empty($address)) {
            $errors[] = 'Address is required';
        }
        
        // Handle profile image upload
        $profileImage = $patient['profile_image'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadImage($_FILES['profile_image'], 'avatars');
            if ($upload['success']) {
                // Delete old image if not default
                if ($patient['profile_image'] !== 'default-avatar.png') {
                    deleteUploadedFile($patient['profile_image'], 'avatars');
                }
                $profileImage = $upload['filename'];
            } else {
                $errors[] = $upload['message'];
            }
        }
        
        if (empty($errors)) {
            try {
                $stmt = $conn->prepare("
                    UPDATE patient SET
                        pname = ?,
                        ptel = ?,
                        paddress = ?,
                        pbloodgroup = ?,
                        pemergency_name = ?,
                        pemergency_contact = ?,
                        profile_image = ?
                    WHERE pemail = ?
                ");
                
                $stmt->execute([
                    $name,
                    $phone,
                    $address,
                    $bloodGroup ?: null,
                    $emergencyName ?: null,
                    $emergencyContact ?: null,
                    $profileImage,
                    $userEmail
                ]);
                
                // Update session
                $_SESSION['name'] = $name;
                $_SESSION['image'] = $profileImage;
                
                logActivity('update_profile', 'Updated profile information');
                
                setFlashMessage('Profile updated successfully!', 'success');
                redirect('/patient/profile.php');
                
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
        
        // Get current password hash
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
                redirect('/patient/profile.php');
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
</head>
<body>
    
    <div class="dashboard-wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/patient_navbar.php'; ?>
            
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
                    
                    <!-- Profile Overview Card -->
                    <div class="col-lg-4">
                        <div class="content-card text-center">
                            <div class="position-relative d-inline-block mb-3">
                                <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($patient['profile_image']); ?>" 
                                     alt="Profile" 
                                     class="rounded-circle"
                                     id="profilePreview"
                                     style="width: 150px; height: 150px; object-fit: cover; border: 5px solid #4361ee;"
                                     onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                            </div>
                            <h4 class="mb-1"><?php echo htmlspecialchars($patient['pname']); ?></h4>
                            <p class="text-muted mb-3">
                                <i class="fas fa-user-circle me-1"></i> Patient
                            </p>
                            
                            <div class="row g-2 text-start">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Patient ID</small>
                                        <strong>#PAT-<?php echo str_pad($patient['pid'], 5, '0', STR_PAD_LEFT); ?></strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Age</small>
                                        <strong><?php echo calculateAge($patient['pdob']); ?> years old</strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Blood Group</small>
                                        <strong><?php echo $patient['pbloodgroup'] ? htmlspecialchars($patient['pbloodgroup']) : 'Not specified'; ?></strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <small class="text-muted d-block">Member Since</small>
                                        <strong><?php echo formatDate($patient['created_at'], 'F Y'); ?></strong>
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
                                        <small class="form-text text-muted">
                                            JPG, PNG or GIF. Max size: 5MB
                                        </small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pname" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="pname" 
                                               name="pname" 
                                               value="<?php echo htmlspecialchars($patient['pname']); ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pemail" class="form-label">Email Address</label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="pemail" 
                                               value="<?php echo htmlspecialchars($patient['pemail']); ?>"
                                               disabled>
                                        <small class="form-text text-muted">Email cannot be changed</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="ptel" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="ptel" 
                                               name="ptel" 
                                               value="<?php echo htmlspecialchars($patient['ptel']); ?>"
                                               required>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pdob" class="form-label">Date of Birth</label>
                                        <input type="date" 
                                               class="form-control" 
                                               id="pdob" 
                                               value="<?php echo htmlspecialchars($patient['pdob']); ?>"
                                               disabled>
                                        <small class="form-text text-muted">Date of birth cannot be changed</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pgender" class="form-label">Gender</label>
                                        <select class="form-select" id="pgender" disabled>
                                            <option value="male" <?php echo ($patient['pgender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo ($patient['pgender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                                            <option value="other" <?php echo ($patient['pgender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                        <small class="form-text text-muted">Gender cannot be changed</small>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pbloodgroup" class="form-label">Blood Group</label>
                                        <select class="form-select" id="pbloodgroup" name="pbloodgroup">
                                            <option value="">Select Blood Group</option>
                                            <?php foreach (getBloodGroupOptions() as $bg): ?>
                                                <option value="<?php echo $bg; ?>" <?php echo ($patient['pbloodgroup'] === $bg) ? 'selected' : ''; ?>>
                                                    <?php echo $bg; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <label for="paddress" class="form-label">Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control" 
                                                  id="paddress" 
                                                  name="paddress" 
                                                  rows="3" 
                                                  required><?php echo htmlspecialchars($patient['paddress']); ?></textarea>
                                    </div>
                                    
                                    <div class="col-12">
                                        <hr>
                                        <h6>Emergency Contact</h6>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pemergency_name" class="form-label">Emergency Contact Name</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="pemergency_name" 
                                               name="pemergency_name" 
                                               value="<?php echo htmlspecialchars($patient['pemergency_name'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label for="pemergency_contact" class="form-label">Emergency Contact Phone</label>
                                        <input type="tel" 
                                               class="form-control" 
                                               id="pemergency_contact" 
                                               name="pemergency_contact" 
                                               value="<?php echo htmlspecialchars($patient['pemergency_contact'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <button type="submit" name="update_profile" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i> Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Change Password Card -->
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
        
        // Password validation
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