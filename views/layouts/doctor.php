<?php
/**
 * Doctor Layout - Dashboard & Management
 * Professional layout for doctors
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

use App\Config\App;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $pageTitle ?? 'Doctor Dashboard - Kyle HMS' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/doctor.css') ?>">
    
    <style>
        body {
            background: #f8f9fa;
        }
        .navbar-doctor {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-doctor .navbar-brand,
        .navbar-doctor .nav-link {
            color: white !important;
        }
        .navbar-doctor .nav-link:hover {
            color: #f0f0f0 !important;
        }
        .content-wrapper {
            padding: 30px 15px;
        }
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background: white;
            border-bottom: 2px solid #11998e;
            font-weight: 600;
        }
        .badge-success-custom {
            background: #11998e;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-doctor">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('/doctor/dashboard') ?>">
                <i class="fas fa-user-md"></i> <strong>Kyle-HMS Doctor</strong>
            </a>
            
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#doctorNav">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="collapse navbar-collapse" id="doctorNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>" href="<?= url('/doctor/dashboard') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'appointments' ? 'active' : '' ?>" href="<?= url('/doctor/appointments') ?>">
                            <i class="fas fa-calendar-check"></i> Appointments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'schedule' ? 'active' : '' ?>" href="<?= url('/doctor/schedule') ?>">
                            <i class="fas fa-clock"></i> Schedule
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'patients' ? 'active' : '' ?>" href="<?= url('/doctor/patients') ?>">
                            <i class="fas fa-users"></i> Patients
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'profile' ? 'active' : '' ?>" href="<?= url('/doctor/profile') ?>">
                            <i class="fas fa-user"></i> Profile
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg"></i>
                            <span class="ms-2">Dr. <?= userEmail() ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('/doctor/profile') ?>"><i class="fas fa-user"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="<?= url('/doctor/schedule') ?>"><i class="fas fa-clock"></i> My Schedule</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/') ?>"><i class="fas fa-home"></i> Home</a></li>
                            <li><a class="dropdown-item" href="<?= url('/auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid content-wrapper">
        <!-- Flash Messages -->
        <?php if (isset($_SESSION['flash_message'])): ?>
            <?= displayFlash() ?>
        <?php endif; ?>

        <!-- Page Content -->
        <?php require __DIR__ . '/../' . str_replace('.', '/', $contentView) . '.php'; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-white text-center py-3 mt-5 border-top">
        <p class="mb-0 text-muted">&copy; <?= date('Y') ?> Kyle-HMS. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= asset('js/main.js') ?>"></script>
    
    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>
</body>
</html>