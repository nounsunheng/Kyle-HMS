<?php
$pageTitle = 'Add Specialty';
$currentPage = 'specialties';
$contentView = 'admin.specialty-create-content';
require __DIR__ . '/../layouts/admin.php';
?>

<div class="mb-4">
    <a href="<?= url('/admin/specialties') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5>Add New Specialty</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('/admin/specialties') ?>">
            <?= App\Config\Security::csrfField() ?>
            
            <div class="mb-3">
                <label class="form-label">Specialty Name *</label>
                <input type="text" name="name" class="form-control" required value="<?= old('name') ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= old('description') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Icon (Font Awesome class)</label>
                <input type="text" name="icon" class="form-control" value="<?= old('icon') ?? 'fas fa-stethoscope' ?>" placeholder="fas fa-stethoscope">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add Specialty
            </button>
        </form>
    </div>
</div>