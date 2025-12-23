<?php
/**
 * Main Layout - Public Pages
 * Used for homepage and public-facing pages
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
    <title><?= $pageTitle ?? 'Kyle Hospital Management System' ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    
    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?= url('/') ?>">
                <i class="fas fa-hospital text-primary"></i>
                <strong>Kyle-HMS</strong>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/#about') ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/#services') ?>">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= url('/#contact') ?>">Contact</a>
                    </li>
                    
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?= userEmail() ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (userRole() === 'p'): ?>
                                    <li><a class="dropdown-item" href="<?= url('/patient/dashboard') ?>">Dashboard</a></li>
                                <?php elseif (userRole() === 'd'): ?>
                                    <li><a class="dropdown-item" href="<?= url('/doctor/dashboard') ?>">Dashboard</a></li>
                                <?php elseif (userRole() === 'a'): ?>
                                    <li><a class="dropdown-item" href="<?= url('/admin/dashboard') ?>">Dashboard</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= url('/auth/logout') ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= url('/auth/login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm ms-2" href="<?= url('/auth/register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="container mt-3">
            <?= displayFlash() ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main>
        <?php require __DIR__ . '/../' . str_replace('.', '/', $contentView) . '.php'; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-hospital"></i> Kyle-HMS</h5>
                    <p>Professional Hospital Management System</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?= url('/') ?>" class="text-white-50">Home</a></li>
                        <li><a href="<?= url('/#about') ?>" class="text-white-50">About</a></li>
                        <li><a href="<?= url('/#services') ?>" class="text-white-50">Services</a></li>
                        <li><a href="<?= url('/#contact') ?>" class="text-white-50">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p class="text-white-50">
                        <i class="fas fa-map-marker-alt"></i> Phnom Penh, Cambodia<br>
                        <i class="fas fa-phone"></i> +855 96 999 0399<br>
                        <i class="fas fa-envelope"></i> info@kyle-hms.com
                    </p>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center">
                <p class="mb-0">&copy; <?= date('Y') ?> Kyle-HMS. All rights reserved. | Developed by Noun Sunheng</p>
            </div>
        </div>
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