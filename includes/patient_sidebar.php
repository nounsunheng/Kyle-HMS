<?php
/**
 * Patient Sidebar Navigation
 * Reusable sidebar component
 */

// Get current page for active menu item
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="sidebar-brand">
            <i class="fas fa-hospital-alt"></i>
            <span>Kyle HMS</span>
        </a>
    </div>
    
    <nav class="sidebar-menu">
        <div class="menu-item">
            <a href="dashboard.php" class="menu-link <?php echo ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="doctors.php" class="menu-link <?php echo ($currentPage == 'doctors.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-md"></i>
                <span>Find Doctors</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="book-appointment.php" class="menu-link <?php echo ($currentPage == 'book-appointment.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-plus"></i>
                <span>Book Appointment</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="appointments.php" class="menu-link <?php echo ($currentPage == 'appointments.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar-check"></i>
                <span>My Appointments</span>
                <?php
                // Get pending appointments count
                try {
                    $stmt = $conn->prepare("
                        SELECT COUNT(*) as count 
                        FROM appointment 
                        WHERE pid = ? AND status = 'pending'
                    ");
                    $stmt->execute([getUserId(getCurrentUserEmail(), 'p')]);
                    $pendingCount = $stmt->fetch()['count'];
                    
                    if ($pendingCount > 0) {
                        echo '<span class="badge bg-warning">' . $pendingCount . '</span>';
                    }
                } catch (PDOException $e) {
                    error_log("Sidebar Count Error: " . $e->getMessage());
                }
                ?>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="medical-records.php" class="menu-link <?php echo ($currentPage == 'medical-records.php') ? 'active' : ''; ?>">
                <i class="fas fa-file-medical"></i>
                <span>Medical Records</span>
            </a>
        </div>
        
        <div class="menu-item">
            <a href="profile.php" class="menu-link <?php echo ($currentPage == 'profile.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i>
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