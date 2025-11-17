<?php
/**
 * Patient Top Navbar
 * Top navigation with notifications and profile
 */

// Get unread notifications count
$notifCount = getUnreadNotificationCount(getCurrentUserEmail());

// Get user info
$userInfo = $_SESSION['name'] ?? 'Patient';
$userImage = $_SESSION['image'] ?? 'default-avatar.png';

// Page title based on current page
$pageTitle = 'Dashboard';
$breadcrumb = ['Home', 'Dashboard'];

switch (basename($_SERVER['PHP_SELF'])) {
    case 'doctors.php':
        $pageTitle = 'Find Doctors';
        $breadcrumb = ['Home', 'Doctors'];
        break;
    case 'book-appointment.php':
        $pageTitle = 'Book Appointment';
        $breadcrumb = ['Home', 'Book'];
        break;
    case 'appointments.php':
        $pageTitle = 'My Appointments';
        $breadcrumb = ['Home', 'Appointments'];
        break;
    case 'medical-records.php':
        $pageTitle = 'Medical Records';
        $breadcrumb = ['Home', 'Records'];
        break;
    case 'profile.php':
        $pageTitle = 'My Profile';
        $breadcrumb = ['Home', 'Profile'];
        break;
}
?>

<nav class="top-navbar">
    <div class="page-title-section">
        <button class="mobile-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <h1><?php echo $pageTitle; ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <?php foreach ($breadcrumb as $index => $crumb): ?>
                    <li class="breadcrumb-item <?php echo ($index == count($breadcrumb) - 1) ? 'active' : ''; ?>">
                        <?php echo $crumb; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
    
    <div class="navbar-actions">
        <!-- Notifications -->
        <div class="notification-btn dropdown">
            <button class="btn" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <?php if ($notifCount > 0): ?>
                    <span class="badge bg-danger"><?php echo $notifCount; ?></span>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="width: 320px;">
                <li>
                    <div class="dropdown-header d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Notifications</span>
                        <?php if ($notifCount > 0): ?>
                            <span class="badge bg-primary"><?php echo $notifCount; ?> new</span>
                        <?php endif; ?>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <?php
                // Fetch recent notifications
                try {
                    $stmt = $conn->prepare("
                        SELECT * FROM notifications 
                        WHERE user_email = ? 
                        ORDER BY created_at DESC 
                        LIMIT 5
                    ");
                    $stmt->execute([getCurrentUserEmail()]);
                    $notifications = $stmt->fetchAll();
                    
                    if (!empty($notifications)) {
                        foreach ($notifications as $notif) {
                            $iconClass = [
                                'appointment' => 'fa-calendar-check text-primary',
                                'system' => 'fa-info-circle text-info',
                                'reminder' => 'fa-bell text-warning',
                                'cancellation' => 'fa-times-circle text-danger'
                            ];
                            $icon = $iconClass[$notif['type']] ?? 'fa-bell text-secondary';
                            $readClass = $notif['is_read'] ? 'bg-light' : 'bg-white';
                            
                            echo '<li>';
                            echo '<a class="dropdown-item ' . $readClass . ' py-3" href="#">';
                            echo '<div class="d-flex">';
                            echo '<div class="me-3"><i class="fas ' . $icon . '"></i></div>';
                            echo '<div class="flex-grow-1">';
                            echo '<div class="fw-bold">' . htmlspecialchars($notif['title']) . '</div>';
                            echo '<div class="small text-muted">' . truncateText($notif['message'], 50) . '</div>';
                            echo '<div class="small text-muted mt-1">' . formatDate($notif['created_at'], 'M d, g:i A') . '</div>';
                            echo '</div>';
                            echo '</div>';
                            echo '</a>';
                            echo '</li>';
                        }
                    } else {
                        echo '<li><div class="dropdown-item text-center text-muted py-3">No notifications</div></li>';
                    }
                } catch (PDOException $e) {
                    error_log("Notifications Error: " . $e->getMessage());
                    echo '<li><div class="dropdown-item text-center text-muted py-3">Error loading notifications</div></li>';
                }
                ?>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-center text-primary" href="#">
                        <small>View All Notifications</small>
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Profile Dropdown -->
        <div class="profile-dropdown dropdown">
            <button class="profile-toggle dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($userImage); ?>" 
                     alt="Profile" 
                     class="profile-avatar">
                <div class="profile-info">
                    <span class="profile-name"><?php echo htmlspecialchars($userInfo); ?></span>
                    <span class="profile-role">Patient</span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="profile.php">
                        <i class="fas fa-user-circle me-2"></i> My Profile
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="appointments.php">
                        <i class="fas fa-calendar-alt me-2"></i> My Appointments
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="medical-records.php">
                        <i class="fas fa-file-medical me-2"></i> Medical Records
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="../auth/logout.php" onclick="return confirm('Are you sure you want to logout?');">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
// Mobile sidebar toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

// Close sidebar on outside click (mobile)
document.addEventListener('click', function(event) {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebarToggle');
    
    if (window.innerWidth <= 992) {
        if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    }
});
</script>