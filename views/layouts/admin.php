// ============================================================================
// FILE 1: views/layouts/admin.php
// ============================================================================
<?php
use App\Config\App;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin - Kyle HMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <style>
        body { background: #f8f9fa; }
        .sidebar { position: fixed; top: 0; left: 0; height: 100vh; width: 250px; background: #2c3e50; color: white; overflow-y: auto; }
        .sidebar-logo { padding: 20px; text-align: center; background: #1a252f; }
        .sidebar-menu a { display: block; padding: 12px 20px; color: #bdc3c7; text-decoration: none; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: #34495e; color: white; border-left: 4px solid #3498db; }
        .main-content { margin-left: 250px; min-height: 100vh; }
        .topbar { background: white; padding: 15px 30px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); display: flex; justify-content: space-between; }
        .content-wrapper { padding: 30px; }
        .stat-card { background: white; border-radius: 10px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-logo">
            <i class="fas fa-hospital fa-2x text-primary"></i>
            <h4 class="mt-2">Kyle-HMS</h4>
            <small>Admin Panel</small>
        </div>
        <div class="sidebar-menu">
            <a href="<?= url('/admin/dashboard') ?>" class="<?= ($currentPage ?? '') === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="<?= url('/admin/doctors') ?>" class="<?= ($currentPage ?? '') === 'doctors' ? 'active' : '' ?>">
                <i class="fas fa-user-md"></i> Doctors
            </a>
            <a href="<?= url('/admin/patients') ?>" class="<?= ($currentPage ?? '') === 'patients' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Patients
            </a>
            <a href="<?= url('/admin/appointments') ?>" class="<?= ($currentPage ?? '') === 'appointments' ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Appointments
            </a>
            <a href="<?= url('/admin/specialties') ?>" class="<?= ($currentPage ?? '') === 'specialties' ? 'active' : '' ?>">
                <i class="fas fa-stethoscope"></i> Specialties
            </a>
            <hr class="bg-secondary">
            <a href="<?= url('/') ?>"><i class="fas fa-home"></i> Home</a>
            <a href="<?= url('/auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h5 class="mb-0"><?= $pageTitle ?? 'Dashboard' ?></h5>
            <div class="dropdown">
                <a href="#" class="text-decoration-none text-dark dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg"></i> <?= userEmail() ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= url('/auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        <div class="content-wrapper">
            <?php if (isset($_SESSION['flash_message'])): ?>
                <?= displayFlash() ?>
            <?php endif; ?>
            <?php require __DIR__ . '/../' . str_replace('.', '/', $contentView) . '.php'; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <?php if (isset($additionalJS)): ?><?= $additionalJS ?><?php endif; ?>
</body>
</html>