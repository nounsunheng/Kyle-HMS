<?php
/**
 * Kyle-HMS Patient Registration
 * Complete signup form with validation
 */
require_once '../config/config.php';
require_once '../config/session.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToDashboard(getCurrentUserType());
}

$errors = [];
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        // Sanitize inputs
        $formData = [
            'name' => sanitize($_POST['name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'dob' => sanitize($_POST['dob'] ?? ''),
            'gender' => sanitize($_POST['gender'] ?? ''),
            'address' => sanitize($_POST['address'] ?? ''),
            'blood_group' => sanitize($_POST['blood_group'] ?? ''),
            'emergency_name' => sanitize($_POST['emergency_name'] ?? ''),
            'emergency_contact' => sanitize($_POST['emergency_contact'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];
        
        // Validation
        if (empty($formData['name'])) {
            $errors[] = 'Full name is required';
        } elseif (strlen($formData['name']) < 3) {
            $errors[] = 'Name must be at least 3 characters';
        }
        
        if (empty($formData['email'])) {
            $errors[] = 'Email is required';
        } elseif (!isValidEmail($formData['email'])) {
            $errors[] = 'Invalid email format';
        } elseif (emailExists($formData['email'])) {
            $errors[] = 'Email already registered';
        }
        
        if (empty($formData['phone'])) {
            $errors[] = 'Phone number is required';
        } elseif (!isValidPhone($formData['phone'])) {
            $errors[] = 'Invalid Cambodian phone number';
        }
        
        if (empty($formData['dob'])) {
            $errors[] = 'Date of birth is required';
        } elseif (!isValidDate($formData['dob'])) {
            $errors[] = 'Invalid date format';
        } else {
            $age = calculateAge($formData['dob']);
            if ($age < 1) {
                $errors[] = 'Invalid date of birth';
            } elseif ($age > 120) {
                $errors[] = 'Please enter a valid date of birth';
            }
        }
        
        if (empty($formData['gender'])) {
            $errors[] = 'Gender is required';
        } elseif (!in_array($formData['gender'], ['male', 'female', 'other'])) {
            $errors[] = 'Invalid gender selection';
        }
        
        if (empty($formData['address'])) {
            $errors[] = 'Address is required';
        }
        
        if (empty($formData['password'])) {
            $errors[] = 'Password is required';
        } else {
            $passwordValidation = validatePassword($formData['password']);
            if (!$passwordValidation['valid']) {
                $errors[] = $passwordValidation['message'];
            }
        }
        
        if ($formData['password'] !== $formData['confirm_password']) {
            $errors[] = 'Passwords do not match';
        }
        
        // If no errors, proceed with registration
        if (empty($errors)) {
            try {
                $conn->beginTransaction();
                
                // Insert into webuser table
                $hashedPassword = hashPassword($formData['password']);
                $stmt = $conn->prepare("INSERT INTO webuser (email, usertype, password) VALUES (?, 'p', ?)");
                $stmt->execute([$formData['email'], $hashedPassword]);
                
                // Insert into patient table
                $stmt = $conn->prepare("
                    INSERT INTO patient (
                        pemail, pname, pdob, pgender, ptel, paddress, 
                        pbloodgroup, pemergency_name, pemergency_contact
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $formData['email'],
                    $formData['name'],
                    $formData['dob'],
                    $formData['gender'],
                    $formData['phone'],
                    $formData['address'],
                    $formData['blood_group'] ?: null,
                    $formData['emergency_name'] ?: null,
                    $formData['emergency_contact'] ?: null
                ]);
                
                $conn->commit();
                
                // Create welcome notification
                createNotification(
                    $formData['email'],
                    'Welcome to Kyle HMS!',
                    'Your account has been created successfully. You can now book appointments with our doctors.',
                    'system'
                );
                
                setFlashMessage('Registration successful! Please login to continue.', 'success');
                redirect('/auth/login.php');
                
            } catch (PDOException $e) {
                $conn->rollBack();
                error_log("Registration Error: " . $e->getMessage());
                $errors[] = 'Registration failed. Please try again.';
            }
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
    <title>Sign Up - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/auth.css">
</head>
<body class="auth-page signup-page">
    
    <div class="auth-container">
        <div class="row g-0">
            <!-- Left Side - Info -->
            <div class="col-lg-5 auth-left">
                <div class="auth-left-content">
                    <a href="<?php echo APP_URL; ?>" class="back-home">
                        <i class="fas fa-arrow-left me-2"></i> Back to Home
                    </a>
                    <div class="auth-brand">
                        <i class="fas fa-hospital-alt"></i>
                        <h2>Kyle HMS</h2>
                    </div>
                    <h3 class="auth-welcome">Join Us Today!</h3>
                    <p class="auth-description">
                        Create your account to access world-class healthcare services. 
                        Book appointments, manage your health records, and stay connected with your doctors.
                    </p>
                    <div class="signup-benefits">
                        <h5 class="mb-3">Why Register?</h5>
                        <div class="benefit-item">
                            <i class="fas fa-calendar-check"></i>
                            <div>
                                <strong>Easy Booking</strong>
                                <p>Schedule appointments instantly</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-file-medical"></i>
                            <div>
                                <strong>Digital Records</strong>
                                <p>Access your medical history anytime</p>
                            </div>
                        </div>
                        <div class="benefit-item">
                            <i class="fas fa-bell"></i>
                            <div>
                                <strong>Smart Reminders</strong>
                                <p>Never miss an appointment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Signup Form -->
            <div class="col-lg-7 auth-right">
                <div class="auth-form-container">
                    <div class="auth-form-header">
                        <h2>Create Account</h2>
                        <p>Fill in your details to get started</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="signupForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        
                        <!-- Personal Information -->
                        <h5 class="form-section-title">
                            <i class="fas fa-user me-2"></i> Personal Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       placeholder="John Doe"
                                       value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">Please enter your full name</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       placeholder="your@email.com"
                                       value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">Please enter a valid email</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="096-123-4567"
                                       value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">Please enter a valid phone number</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control" 
                                       id="dob" 
                                       name="dob" 
                                       max="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo htmlspecialchars($formData['dob'] ?? ''); ?>"
                                       required>
                                <div class="invalid-feedback">Please select your date of birth</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <?php foreach (getGenderOptions() as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" <?php echo (($formData['gender'] ?? '') === $value) ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select your gender</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="blood_group" class="form-label">Blood Group</label>
                                <select class="form-select" id="blood_group" name="blood_group">
                                    <option value="">Select Blood Group</option>
                                    <?php foreach (getBloodGroupOptions() as $bg): ?>
                                        <option value="<?php echo $bg; ?>" <?php echo (($formData['blood_group'] ?? '') === $bg) ? 'selected' : ''; ?>>
                                            <?php echo $bg; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="address" 
                                      name="address" 
                                      rows="2" 
                                      placeholder="Street, City, Province"
                                      required><?php echo htmlspecialchars($formData['address'] ?? ''); ?></textarea>
                            <div class="invalid-feedback">Please enter your address</div>
                        </div>
                        
                        <!-- Emergency Contact -->
                        <h5 class="form-section-title mt-4">
                            <i class="fas fa-phone-alt me-2"></i> Emergency Contact
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="emergency_name" class="form-label">Contact Name</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="emergency_name" 
                                       name="emergency_name" 
                                       placeholder="Emergency contact name"
                                       value="<?php echo htmlspecialchars($formData['emergency_name'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="emergency_contact" class="form-label">Contact Phone</label>
                                <input type="tel" 
                                       class="form-control" 
                                       id="emergency_contact" 
                                       name="emergency_contact" 
                                       placeholder="Emergency contact phone"
                                       value="<?php echo htmlspecialchars($formData['emergency_contact'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <h5 class="form-section-title mt-4">
                            <i class="fas fa-lock me-2"></i> Security
                        </h5>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Create strong password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    Min 8 characters, include uppercase, lowercase, number & special character
                                </small>
                                <div class="invalid-feedback">Please enter a strong password</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Re-enter password"
                                           required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">Passwords must match</div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="terms" 
                                       required>
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and 
                                    <a href="#" class="text-primary">Privacy Policy</a>
                                </label>
                                <div class="invalid-feedback">You must agree to continue</div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-user-plus me-2"></i> Create Account
                        </button>
                        
                        <div class="text-center">
                            <p class="mb-0">
                                Already have an account? 
                                <a href="login.php" class="text-primary fw-bold">Sign In</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo ASSETS_PATH; ?>/js/validation.js"></script>
    
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            togglePasswordVisibility('password', this);
        });
        
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            togglePasswordVisibility('confirm_password', this);
        });
        
        function togglePasswordVisibility(fieldId, button) {
            const field = document.getElementById(fieldId);
            const icon = button.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Form validation
        const form = document.getElementById('signupForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // Check password match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                event.preventDefault();
                document.getElementById('confirm_password').setCustomValidity('Passwords do not match');
            }
            
            form.classList.add('was-validated');
        });
        
        // Reset password validation on input
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            if (this.value === password) {
                this.setCustomValidity('');
            } else {
                this.setCustomValidity('Passwords do not match');
            }
        });
    </script>
</body>
</html>