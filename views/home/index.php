<?php
/**
 * Homepage View
 * Public landing page
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

$pageTitle = 'Home - Kyle Hospital Management System';
$contentView = 'home.index-content';

// Include main layout
require __DIR__ . '/../layouts/main.php';
?>

<!-- Hero Section -->
<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row align-items-center text-white">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold">Welcome to Kyle-HMS</h1>
                <p class="lead">Modern Hospital Management System</p>
                <?php if (!isLoggedIn()): ?>
                    <a href="<?= url('/auth/register') ?>" class="btn btn-light btn-lg">Get Started</a>
                    <a href="<?= url('/auth/login') ?>" class="btn btn-outline-light btn-lg ms-2">Login</a>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <img src="<?= asset('images/hero-illustration.jpg') ?>" alt="Healthcare" class="img-fluid rounded">
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features-section py-5" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Our Services</h2>
            <p class="text-muted">Comprehensive healthcare management solutions</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Online Appointments</h5>
                        <p class="text-muted">Book appointments with your preferred doctors anytime, anywhere</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-md fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Expert Doctors</h5>
                        <p class="text-muted">Access to qualified healthcare professionals across specialties</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-file-medical fa-2x"></i>
                        </div>
                        <h5 class="fw-bold">Medical Records</h5>
                        <p class="text-muted">Secure digital storage of your medical history and reports</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section py-5 bg-light" id="about">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="<?= asset('images/about-img.jpg') ?>" alt="About Us" class="img-fluid rounded shadow">
            </div>
            <div class="col-md-6">
                <h2 class="fw-bold mb-4">About Kyle-HMS</h2>
                <p class="text-muted">Kyle Hospital Management System is a comprehensive digital solution designed to streamline healthcare operations and improve patient care delivery.</p>
                <p class="text-muted">Our platform enables efficient appointment scheduling, doctor-patient communication, and medical record management, all in one secure system.</p>
                <ul class="list-unstyled mt-4">
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> 24/7 Online Appointment Booking</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Real-time Schedule Management</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Secure Medical Records</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Multi-specialty Support</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section py-5" id="contact">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Contact Us</h2>
            <p class="text-muted">Get in touch with us for any inquiries</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4 text-center">
                <div class="contact-item p-4">
                    <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                    <h5>Address</h5>
                    <p class="text-muted">Phnom Penh, Cambodia</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="contact-item p-4">
                    <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                    <h5>Phone</h5>
                    <p class="text-muted">+855 96 999 0399</p>
                </div>
            </div>
            
            <div class="col-md-4 text-center">
                <div class="contact-item p-4">
                    <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                    <h5>Email</h5>
                    <p class="text-muted">info@kyle-hms.com</p>
                </div>
            </div>
        </div>
    </div>
</section>