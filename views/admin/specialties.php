<?php
$pageTitle = 'Manage Specialties';
$currentPage = 'specialties';
$contentView = 'admin.specialties-content';
require __DIR__ . '/../layouts/admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-stethoscope"></i> Medical Specialties</h3>
    <a href="<?= url('/admin/specialties/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Specialty
    </a>
</div>

<div class="row">
    <?php if (empty($specialties)): ?>
        <div class="col-12">
            <div class="alert alert-info">No specialties found</div>
        </div>
    <?php else: ?>
        <?php foreach ($specialties as $specialty): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <i class="<?= $specialty['icon'] ?? 'fas fa-stethoscope' ?> fa-2x text-primary"></i>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= url('/admin/specialties/' . ($specialty['id'] ?? '')) ?>/edit">Edit</a></li>
                                    <li>
                                        <form method="POST" action="<?= url('/admin/specialties/' . ($specialty['id'] ?? '')) ?>/delete">
                                            <?= App\Config\Security::csrfField() ?>
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Delete?')">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <h5 class="mt-3"><?= htmlspecialchars($specialty['name'] ?? '') ?></h5>
                        <p class="text-muted small"><?= htmlspecialchars($specialty['description'] ?? '') ?></p>
                        <span class="badge bg-info"><?= $specialty['doctor_count'] ?? 0 ?> Doctors</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>