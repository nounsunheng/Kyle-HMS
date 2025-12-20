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
            <button class="btn" data-bs-toggle="dropdown" aria-expanded="false" id="notificationToggle">
                <i class="fas fa-bell"></i>
                <?php if ($notifCount > 0): ?>
                    <span class="badge bg-danger" id="notifBadge"><?php echo $notifCount; ?></span>
                <?php endif; ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end notification-dropdown" style="width: 380px; max-width: 95vw; max-height: 500px; overflow-y: auto; overflow-x: hidden;">
                <li>
                    <div class="dropdown-header d-flex justify-content-between align-items-center" style="flex-wrap: wrap;">
                        <span class="fw-bold">Notifications</span>
                        <div>
                            <?php if ($notifCount > 0): ?>
                                <span class="badge bg-primary me-2"><?php echo $notifCount; ?> new</span>
                                <button class="btn btn-sm btn-link text-primary p-0" onclick="markAllAsRead()" style="font-size: 0.8rem;">
                                    Mark all read
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <div id="notificationsList">
                    <?php
                    // Fetch recent notifications
                    try {
                        $stmt = $conn->prepare("
                            SELECT * FROM notifications 
                            WHERE user_email = ? 
                            ORDER BY created_at DESC 
                            LIMIT 10
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
                                $readClass = $notif['is_read'] ? 'bg-light' : 'bg-white border-start border-primary border-3';
                                
                                echo '<li>';
                                echo '<a class="dropdown-item ' . $readClass . ' py-3 notification-item" href="#" data-notif-id="' . $notif['notif_id'] . '" onclick="markAsRead(' . $notif['notif_id'] . ', event)" style="white-space: normal; overflow-wrap: break-word;">';
                                echo '<div class="d-flex" style="max-width: 100%;">';
                                echo '<div class="me-2 mt-1 flex-shrink-0"><i class="fas ' . $icon . ' fs-5"></i></div>';
                                echo '<div class="flex-grow-1" style="min-width: 0; overflow: hidden;">';
                                echo '<div class="fw-bold mb-1" style="font-size: 0.9rem; word-wrap: break-word; overflow-wrap: break-word;">' . htmlspecialchars($notif['title']) . '</div>';
                                echo '<div class="text-muted mb-1" style="font-size: 0.85rem; word-wrap: break-word; overflow-wrap: break-word; white-space: normal; line-height: 1.4;">' . htmlspecialchars($notif['message']) . '</div>';
                                echo '<div class="text-muted" style="font-size: 0.75rem;">';
                                echo '<i class="fas fa-clock me-1"></i>' . formatDate($notif['created_at'], 'M d, g:i A');
                                echo '</div>';
                                echo '</div>';
                                echo '</div>';
                                echo '</a>';
                                echo '</li>';
                            }
                        } else {
                            echo '<li><div class="dropdown-item text-center text-muted py-5">';
                            echo '<i class="fas fa-bell-slash fa-3x mb-3 opacity-25"></i>';
                            echo '<p class="mb-0">No notifications</p>';
                            echo '</div></li>';
                        }
                    } catch (PDOException $e) {
                        error_log("Notifications Error: " . $e->getMessage());
                        echo '<li><div class="dropdown-item text-center text-danger py-3">Error loading notifications</div></li>';
                    }
                    ?>
                </div>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-center text-primary py-2" href="#">
                        <small><strong>View All Notifications</strong></small>
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

<style>
.notification-dropdown {
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.notification-item {
    transition: all 0.2s ease;
}

.notification-item:hover {
    background-color: #f8f9fa !important;
}

.notification-dropdown::-webkit-scrollbar {
    width: 6px;
}

.notification-dropdown::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.notification-dropdown::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.notification-dropdown::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

<script>
// Mobile sidebar toggle
document.getElementById('sidebarToggle')?.addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

// Mark notification as read
async function markAsRead(notifId, event) {
    event.preventDefault();
    
    try {
        const response = await fetch('../api/mark-notification-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ notif_id: notifId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update UI
            const notifItem = document.querySelector(`[data-notif-id="${notifId}"]`);
            if (notifItem) {
                notifItem.classList.remove('bg-white', 'border-start', 'border-primary', 'border-3');
                notifItem.classList.add('bg-light');
            }
            
            // Update badge
            updateNotificationBadge();
        }
    } catch (error) {
        console.error('Error marking notification as read:', error);
    }
}

// Mark all notifications as read
async function markAllAsRead() {
    try {
        const response = await fetch('../api/mark-all-notifications-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Update UI
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('bg-white', 'border-start', 'border-primary', 'border-3');
                item.classList.add('bg-light');
            });
            
            // Remove badge
            const badge = document.getElementById('notifBadge');
            if (badge) {
                badge.remove();
            }
            
            // Refresh to show updated header
            location.reload();
        }
    } catch (error) {
        console.error('Error marking all notifications as read:', error);
    }
}

// Update notification badge count
async function updateNotificationBadge() {
    try {
        const response = await fetch('../api/get-unread-count.php');
        const data = await response.json();
        
        const badge = document.getElementById('notifBadge');
        
        if (data.count > 0) {
            if (badge) {
                badge.textContent = data.count;
            } else {
                const btn = document.getElementById('notificationToggle');
                btn.innerHTML += `<span class="badge bg-danger" id="notifBadge">${data.count}</span>`;
            }
        } else {
            if (badge) {
                badge.remove();
            }
        }
    } catch (error) {
        console.error('Error updating notification badge:', error);
    }
}

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