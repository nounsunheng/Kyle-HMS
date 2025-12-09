<?php
/**
 * Admin Sidebar Navigation
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <i class="fas fa-hospital-alt"></i>
            <span>Kyle HMS</span>
        </a>
        <div class="text-center mt-2">
            <small class="text-white-50">Admin Panel</small>
        </div>
    </div>
    
    <nav class="sidebar-menu">
        <div class="menu-item">
            <a href="dashboard.php" class="menu-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="manage-doctors.php" class="menu-link <?php echo ($currentPage == 'manage-doctors.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i>
                <span>Manage Doctors</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="manage-patients.php" class="menu-link <?php echo ($currentPage == 'manage-patients.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Manage Patients</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="manage-appointments.php" class="menu-link <?php echo ($currentPage == 'manage-appointments.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>Appointments</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="manage-specialties.php" class="menu-link <?php echo ($currentPage == 'manage-specialties.php') ? 'active' : ''; ?>">
                <i class="fas fa-stethoscope"></i>
                <span>Specialties</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="reports.php" class="menu-link <?php echo ($currentPage == 'reports.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
        </div>
        
        <div class="menu-item mt-auto">
            <a href="../auth/logout.php" class="menu-link text-danger" onclick="return confirm('Are you sure you want to logout?');">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </nav>
</aside>