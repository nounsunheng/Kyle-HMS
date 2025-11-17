<?php
/**
 * Kyle-HMS Login Page
 * Secure authentication with validation
 */
require_once '../config/config.php';
require_once '../config/session.php';

// Enable debug mode (set to false in production)
define('DEBUG_MODE', true);

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToDashboard(getCurrentUserType());
}

$error = '';
$email = '';
$debugInfo = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (DEBUG_MODE) {
            $debugInfo[] = "Email received: " . $email;
            $debugInfo[] = "Password length: " . strlen($password);
        }
        
        // Validation
        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields';
        } elseif (!isValidEmail($email)) {
            $error = 'Invalid email format';
        } else {
            try {
                // Check user exists
                $stmt = $conn->prepare("SELECT email, usertype, password, status FROM webuser WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if (DEBUG_MODE) {
                    $debugInfo[] = "User found: " . ($user ? 'Yes' : 'No');
                    if ($user) {
                        $debugInfo[] = "User status: " . $user['status'];
                        $debugInfo[] = "User type: " . $user['usertype'];
                        $debugInfo[] = "Password hash (first 30 chars): " . substr($user['password'], 0, 30) . "...";
                    }
                }
                
                if ($user) {
                    if (DEBUG_MODE) {
                        $passwordVerified = password_verify($password, $user['password']);
                        $debugInfo[] = "Password verification: " . ($passwordVerified ? 'MATCH' : 'NO MATCH');
                    }
                    
                    if (password_verify($password, $user['password'])) {
                        // Check account status
                        if ($user['status'] !== 'active') {
                            $error = 'Your account has been ' . $user['status'] . '. Please contact administrator.';
                        } else {
                            // Get user details based on type
                            $userDetails = [];
                            $userName = '';
                            $userId = null;
                            
                            switch ($user['usertype']) {
                                case 'p':
                                    $stmt = $conn->prepare("SELECT pid, pname, profile_image FROM patient WHERE pemail = ?");
                                    $stmt->execute([$email]);
                                    $details = $stmt->fetch();
                                    $userName = $details['pname'] ?? 'Patient';
                                    $userId = $details['pid'] ?? null;
                                    $userDetails = ['name' => $userName, 'id' => $userId, 'image' => $details['profile_image'] ?? 'default-avatar.png'];
                                    break;
                                    
                                case 'd':
                                    $stmt = $conn->prepare("SELECT docid, docname, profile_image FROM doctor WHERE docemail = ?");
                                    $stmt->execute([$email]);
                                    $details = $stmt->fetch();
                                    $userName = $details['docname'] ?? 'Doctor';
                                    $userId = $details['docid'] ?? null;
                                    $userDetails = ['name' => $userName, 'id' => $userId, 'image' => $details['profile_image'] ?? 'default-doctor.png'];
                                    break;
                                    
                                case 'a':
                                    $stmt = $conn->prepare("SELECT aid, aname FROM admin WHERE aemail = ?");
                                    $stmt->execute([$email]);
                                    $details = $stmt->fetch();
                                    $userName = $details['aname'] ?? 'Admin';
                                    $userId = $details['aid'] ?? null;
                                    $userDetails = ['name' => $userName, 'id' => $userId, 'image' => 'default-avatar.png'];
                                    break;
                            }
                            
                            // Initialize session
                            initializeSession($email, $user['usertype'], $userDetails);
                            
                            // Create notification
                            createNotification($email, 'Welcome Back!', 'You have successfully logged in to Kyle HMS.', 'system');
                            
                            // Redirect to dashboard
                            redirectToDashboard($user['usertype']);
                        }
                    } else {
                        $error = 'Invalid email or password';
                        if (DEBUG_MODE) {
                            $error .= ' (Check debug info below)';
                        }
                    }
                } else {
                    $error = 'Invalid email or password';
                    if (DEBUG_MODE) {
                        $error .= ' (User not found)';
                    }
                    logActivity('failed_login', 'Failed login attempt for email: ' . $email);
                }
            } catch (PDOException $e) {
                error_log("Login Error: " . $e->getMessage());
                $error = 'An error occurred. Please try again later.';
                if (DEBUG_MODE) {
                    $error .= ' - ' . $e->getMessage();
                }
            }
        }
    }
}

// Generate CSRF token
$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/auth.css">
</head>
<body class="auth-page">
    
    <div class="auth-container">
        <div class="row g-0">
            <!-- Left Side - Image/Info -->
            <div class="col-lg-6 auth-left">
                <div class="auth-left-content">
                    <a href="<?php echo APP_URL; ?>" class="back-home">
                        <i class="fas fa-arrow-left me-2"></i> Back to Home
                    </a>
                    <div class="auth-brand">
                        <i class="fas fa-hospital-alt"></i>
                        <h2>Kyle HMS</h2>
                    </div>
                    <h3 class="auth-welcome">Welcome Back!</h3>
                    <p class="auth-description">
                        Access your account to manage appointments, view medical records, and connect with healthcare professionals.
                    </p>
                    <div class="auth-features">
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Secure & Private</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>24/7 Access</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-check-circle"></i>
                            <span>Easy to Use</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="col-lg-6 auth-right">
                <div class="auth-form-container">
                    <div class="auth-form-header">
                        <h2>Sign In</h2>
                        <p>Enter your credentials to access your account</p>
                    </div>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (DEBUG_MODE && !empty($debugInfo)): ?>
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <strong><i class="fas fa-bug me-2"></i>Debug Information:</strong><br>
                            <?php foreach ($debugInfo as $info): ?>
                                • <?php echo htmlspecialchars($info); ?><br>
                            <?php endforeach; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php displayFlashMessage(); ?>
                    
                    <form method="POST" action="" id="loginForm" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i> Email Address
                            </label>
                            <input type="email" 
                                   class="form-control form-control-lg" 
                                   id="email" 
                                   name="email" 
                                   placeholder="your@email.com"
                                   value="<?php echo htmlspecialchars($email); ?>"
                                   required>
                            <div class="invalid-feedback">
                                Please enter a valid email address
                            </div>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i> Password
                            </label>
                            <div class="input-group">
                                <input type="password" 
                                       class="form-control form-control-lg" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Enter your password"
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Please enter your password
                            </div>
                        </div>
                        
                        <div class="form-group mb-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="rememberMe" 
                                           name="remember">
                                    <label class="form-check-label" for="rememberMe">
                                        Remember me
                                    </label>
                                </div>
                                <a href="forgot-password.php" class="text-primary">
                                    Forgot Password?
                                </a>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i> Sign In
                        </button>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">
                                Don't have an account? 
                                <a href="signup.php" class="text-primary fw-bold">Sign Up</a>
                            </p>
                        </div>
                    </form>
                    
                    <!-- Test Accounts Info -->
                    <div class="mt-5 pt-4 border-top">
                        <p class="text-center text-muted mb-3">
                            <small><i class="fas fa-info-circle me-1"></i> Test Accounts Available</small>
                        </p>
                        <div class="test-accounts">
                            <div class="test-account-item">
                                <strong>Admin:</strong> admin@kyle-hms.com
                            </div>
                            <div class="test-account-item">
                                <strong>Doctor:</strong> dr.soklina@kyle-hms.com
                            </div>
                            <div class="test-account-item">
                                <strong>Patient:</strong> patient1@test.com
                            </div>
                            <div class="test-account-item">
                                <strong>Password:</strong> Test@123
                            </div>
                        </div>
                        
                        <?php if (DEBUG_MODE): ?>
                        <div class="alert alert-warning mt-3 mb-0">
                            <small>
                                <i class="fas fa-tools me-1"></i>
                                <strong>Debug Mode Active</strong><br>
                                Having issues? Try the <a href="../test-password.php" target="_blank">Password Debug Tool</a>
                            </small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Form validation
        const form = document.getElementById('loginForm');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
        
        // Email validation on input
        const emailInput = document.getElementById('email');
        emailInput.addEventListener('input', function() {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (this.value && !emailRegex.test(this.value)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>