<?php
/**
 * Kyle-HMS Landing Page
 * Main entry point with modern UI
 */
require_once 'config/config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirectToDashboard(getCurrentUserType());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kyle Hospital Management System - Modern Healthcare Management Platform">
    <title><?php echo APP_NAME; ?> - Home</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/style.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo ASSETS_PATH; ?>/images/favicon.ico">
</head>
<body class="landing-page">
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-hospital-alt me-2"></i>
                <span class="brand-text">Kyle HMS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="btn btn-outline-light btn-sm" href="auth/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary btn-sm" href="auth/signup.php">
                            <i class="fas fa-user-plus me-1"></i> Sign Up
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Modern Healthcare <br>
                            <span class="text-primary">Management System</span>
                        </h1>
                        <p class="hero-subtitle">
                            Streamline your hospital operations with our comprehensive digital platform. 
                            Book appointments, manage schedules, and access medical records - all in one place.
                        </p>
                        <div class="hero-buttons">
                            <a href="auth/signup.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-rocket me-2"></i> Get Started
                            </a>
                            <a href="#features" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-play-circle me-2"></i> Learn More
                            </a>
                        </div>
                        <div class="hero-stats mt-5">
                            <div class="row text-center">
                                <div class="col-4">
                                    <h3 class="stat-number">10+</h3>
                                    <p class="stat-label">Doctors</p>
                                </div>
                                <div class="col-4">
                                    <h3 class="stat-number">500+</h3>
                                    <p class="stat-label">Patients</p>
                                </div>
                                <div class="col-4">
                                    <h3 class="stat-number">24/7</h3>
                                    <p class="stat-label">Support</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="hero-image">
                        <img src="<?php echo ASSETS_PATH; ?>/images/hero-illustration.jpg" 
                             alt="Healthcare Illustration" 
                             class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Powerful Features</h2>
                <p class="section-subtitle">Everything you need to manage your healthcare facility efficiently</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Easy Appointment Booking</h3>
                        <p>Book appointments online 24/7. Choose your preferred doctor, date, and time slot with just a few clicks.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <h3>Expert Doctors</h3>
                        <p>Access to qualified healthcare professionals across 50+ medical specialties for comprehensive care.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h3>Digital Medical Records</h3>
                        <p>Access your complete medical history anytime, anywhere. All your health records in one secure place.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3>Real-time Updates</h3>
                        <p>Get instant notifications about appointment confirmations, schedule changes, and important reminders.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Secure & Private</h3>
                        <p>Your data is protected with enterprise-grade security. HIPAA compliant and encrypted storage.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h3>Mobile Responsive</h3>
                        <p>Access the platform from any device - desktop, tablet, or smartphone. Works perfectly everywhere.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section py-5 bg-light">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Our Services</h2>
                <p class="section-subtitle">Comprehensive healthcare solutions for everyone</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="service-card">
                        <div class="service-icon-wrap">
                            <i class="fas fa-heartbeat service-icon"></i>
                        </div>
                        <h3>For Patients</h3>
                        <ul class="service-list">
                            <li><i class="fas fa-check-circle"></i> Online appointment booking</li>
                            <li><i class="fas fa-check-circle"></i> View doctor profiles</li>
                            <li><i class="fas fa-check-circle"></i> Access medical records</li>
                            <li><i class="fas fa-check-circle"></i> Appointment history</li>
                            <li><i class="fas fa-check-circle"></i> Real-time notifications</li>
                        </ul>
                        <a href="auth/signup.php" class="btn btn-primary mt-3">Register as Patient</a>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="service-card featured">
                        <div class="badge-featured">Most Popular</div>
                        <div class="service-icon-wrap">
                            <i class="fas fa-stethoscope service-icon"></i>
                        </div>
                        <h3>For Doctors</h3>
                        <ul class="service-list">
                            <li><i class="fas fa-check-circle"></i> Manage schedules</li>
                            <li><i class="fas fa-check-circle"></i> View appointments</li>
                            <li><i class="fas fa-check-circle"></i> Access patient records</li>
                            <li><i class="fas fa-check-circle"></i> Update availability</li>
                            <li><i class="fas fa-check-circle"></i> Professional dashboard</li>
                        </ul>
                        <a href="auth/login.php" class="btn btn-primary mt-3">Doctor Login</a>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="service-card">
                        <div class="service-icon-wrap">
                            <i class="fas fa-cogs service-icon"></i>
                        </div>
                        <h3>For Administrators</h3>
                        <ul class="service-list">
                            <li><i class="fas fa-check-circle"></i> Complete system control</li>
                            <li><i class="fas fa-check-circle"></i> Manage doctors & patients</li>
                            <li><i class="fas fa-check-circle"></i> View all appointments</li>
                            <li><i class="fas fa-check-circle"></i> Generate reports</li>
                            <li><i class="fas fa-check-circle"></i> System analytics</li>
                        </ul>
                        <a href="auth/login.php" class="btn btn-primary mt-3">Admin Login</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">How It Works</h2>
                <p class="section-subtitle">Get started in 3 simple steps</p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <div class="step-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3>Create Account</h3>
                        <p>Sign up with your basic information. It takes less than 2 minutes to register.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <div class="step-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Find Doctor</h3>
                        <p>Browse through our list of qualified doctors and select based on specialty.</p>
                    </div>
                </div>
                
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <div class="step-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Book Appointment</h3>
                        <p>Choose your preferred date and time slot, then confirm your appointment instantly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="about-image">
                        <img src="<?php echo ASSETS_PATH; ?>/images/about-img.jpg" 
                             alt="About Kyle HMS" 
                             class="img-fluid rounded shadow">
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="about-content">
                        <h2 class="mb-4">About Kyle HMS</h2>
                        <p class="lead">
                            Kyle Hospital Management System is a comprehensive digital platform designed to 
                            revolutionize healthcare management in Cambodia.
                        </p>
                        <p>
                            Our mission is to bridge the gap between patients and healthcare providers through 
                            technology. We provide a user-friendly platform that simplifies appointment scheduling, 
                            medical record management, and doctor-patient communication.
                        </p>
                        <div class="about-stats mt-4">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="stat-box">
                                        <i class="fas fa-users-medical"></i>
                                        <h4>Professional Team</h4>
                                        <p>Experienced developers and healthcare experts</p>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="stat-box">
                                        <i class="fas fa-award"></i>
                                        <h4>Quality Service</h4>
                                        <p>Committed to excellence and patient care</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section py-5">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">Have questions? We're here to help</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Address</h4>
                        <p>Phnom Penh, Cambodia<br>Street 123, Sangkat Tonle Bassac</p>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h4>Phone</h4>
                        <p>+855 96 999 0399<br>+855 12 345 678</p>
                    </div>
                </div>
                
                <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="contact-info-card">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h4>Email</h4>
                        <p>contact@kyle-hms.com<br>support@kyle-hms.com</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-section py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">
                        &copy; <?php echo date('Y'); ?> Kyle HMS. All rights reserved. 
                        <br>
                        <small>Developed by Noun Sunheng - IT Academy STEP Cambodia</small>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button id="scrollTopBtn" class="scroll-top-btn">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom JS -->
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });
    </script>
</body>
</html>