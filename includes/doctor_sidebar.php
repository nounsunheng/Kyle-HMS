<?php
/**
 * Doctor Sidebar Navigation
 * Reusable sidebar for doctor portal
 */

$currentPage = basename($_SERVER['PHP_SELF']);
$doctorId = getUserId(getCurrentUserEmail(), 'd');

// Get today's appointments count
$todayCount = 0;
try {
    $stmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM appointment a
        JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ? 
        AND a.appodate = CURDATE()
        AND a.status IN ('pending', 'confirmed')
    ");
    $stmt->execute([$doctorId]);
    $todayCount = $stmt->fetch()['count'];
} catch (PDOException $e) {
    error_log("Sidebar Count Error: " . $e->getMessage());
}
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <i class="fas fa-hospital-alt"></i>
            <span>Kyle HMS</span>
        </a>
        <div class="text-center mt-2">
            <small class="text-white-50">Doctor Portal</small>
        </div>
    </div>
    
    <nav class="sidebar-menu">
        <div class="menu-item">
            <a href="dashboard.php" class="menu-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="appointments.php" class="menu-link <?php echo ($currentPage == 'appointments.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                <span>Appointments</span>
                <?php if ($todayCount > 0): ?>
                    <span class="badge bg-danger"><?php echo $todayCount; ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="schedule.php" class="menu-link <?php echo ($currentPage == 'schedule.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i>
                <span>My Schedule</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="patients.php" class="menu-link <?php echo ($currentPage == 'patients.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>My Patients</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="profile.php" class="menu-link <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i>
                <span>My Profile</span>
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