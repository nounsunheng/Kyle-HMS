<?php
/**
 * Admin Top Navbar
 */

$userInfo = $_SESSION['name'] ?? 'Administrator';
$userImage = $_SESSION['image'] ?? 'default-avatar.png';

$pageTitle = 'Dashboard';
$breadcrumb = ['Home', 'Dashboard'];

switch (basename($_SERVER['PHP_SELF'])) {
    case 'manage-doctors.php':
        $pageTitle = 'Manage Doctors';
        $breadcrumb = ['Home', 'Doctors'];
        break;
    case 'manage-patients.php':
        $pageTitle = 'Manage Patients';
        $breadcrumb = ['Home', 'Patients'];
        break;
    case 'manage-appointments.php':
        $pageTitle = 'Manage Appointments';
        $breadcrumb = ['Home', 'Appointments'];
        break;
    case 'manage-specialties.php':
        $pageTitle = 'Manage Specialties';
        $breadcrumb = ['Home', 'Specialties'];
        break;
    case 'reports.php':
        $pageTitle = 'Reports';
        $breadcrumb = ['Home', 'Reports'];
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
        <!-- System Status -->
        <div class="me-3 d-none d-md-block">
            <div class="system-status online">
                <span class="status-pulse green"></span>
                <span>System Online</span>
            </div>
        </div>
        
        <!-- Profile Dropdown -->
        <div class="profile-dropdown dropdown">
            <button class="profile-toggle dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($userImage); ?>" 
                     alt="Profile" 
                     class="profile-avatar"
                     onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-avatar.png'">
                <div class="profile-info">
                    <span class="profile-name"><?php echo htmlspecialchars($userInfo); ?></span>
                    <span class="profile-role">Administrator</span>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
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
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

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