<?php
$pageTitle = 'Find Doctors';
$currentPage = 'doctors';
$contentView = 'patient.doctors-content';
require __DIR__ . '/../layouts/patient.php';
?>

<h2>Find a Doctor</h2>
<p class="text-muted">Browse our qualified healthcare professionals</p>

<div class="row mt-4">
    <?php if (empty($doctors)): ?>
        <div class="col-12">
            <div class="alert alert-info">No doctors available</div>
        </div>
    <?php else: ?>
        <?php foreach ($doctors as $doctor): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex">
                            <img src="<?= asset('uploads/avatars/' . ($doctor['profile_image'] ?? 'default-doctor.png')) ?>" 
                                 alt="Doctor" class="rounded-circle me-3" style="width: 80px; height: 80px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <h5 class="mb-1">Dr. <?= htmlspecialchars($doctor['docname'] ?? '') ?></h5>
                                <p class="text-muted mb-2">
                                    <i class="fas fa-stethoscope"></i> <?= htmlspecialchars($doctor['specialty_name'] ?? '') ?>
                                </p>
                                <p class="mb-2 small"><?= htmlspecialchars($doctor['docdegree'] ?? '') ?> | <?= ($doctor['docexperience'] ?? 0) ?> years exp.</p>
                                <div class="d-flex gap-2">
                                    <a href="<?= url('/patient/book-appointment?doctor=' . ($doctor['docid'] ?? '')) ?>" class="btn btn-primary btn-sm">
                                        <i class="fas fa-calendar-plus"></i> Book Appointment
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>