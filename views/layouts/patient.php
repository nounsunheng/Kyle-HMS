<?php
use App\Config\App;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Patient - Kyle HMS' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/patient.css') ?>">
    <style>
        body { background: #f8f9fa; }
        .navbar-patient { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .navbar-patient .navbar-brand, .navbar-patient .nav-link { color: white !important; }
        .content-wrapper { padding: 30px 15px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-patient">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= url('/patient/dashboard') ?>">
                <i class="fas fa-hospital"></i> <strong>Kyle-HMS</strong>
            </a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="nav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="<?= url('/patient/dashboard') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/patient/doctors') ?>"><i class="fas fa-user-md"></i> Doctors</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/patient/appointments') ?>"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('/patient/profile') ?>"><i class="fas fa-user"></i> Profile</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?= userEmail() ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?= url('/patient/profile') ?>"><i class="fas fa-user"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= url('/auth/logout') ?>"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid content-wrapper">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <?= displayFlash() ?>
        <?php endif; ?>
        <?php require __DIR__ . '/../' . str_replace('.', '/', $contentView) . '.php'; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <?php if (isset($additionalJS)): ?><?= $additionalJS ?><?php endif; ?>
</body>
</html>