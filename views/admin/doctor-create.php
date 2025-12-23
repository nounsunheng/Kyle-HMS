<?php
$pageTitle = 'Add Doctor';
$currentPage = 'doctors';
$contentView = 'admin.doctor-create-content';
require __DIR__ . '/../layouts/admin.php';
?>

<div class="mb-4">
    <a href="<?= url('/admin/doctors') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Doctors
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-plus"></i> Add New Doctor</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="<?= url('/admin/doctors') ?>">
            <?= App\Config\Security::csrfField() ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="docname" class="form-control" required value="<?= old('docname') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="docemail" class="form-control" required value="<?= old('docemail') ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone *</label>
                    <input type="tel" name="doctel" class="form-control" required value="<?= old('doctel') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Specialty *</label>
                    <select name="specialties" class="form-control" required>
                        <option value="">Select Specialty</option>
                        <?php foreach ($specialties ?? [] as $specialty): ?>
                            <option value="<?= $specialty['id'] ?? '' ?>"><?= htmlspecialchars($specialty['name'] ?? '') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Degree *</label>
                    <input type="text" name="docdegree" class="form-control" required value="<?= old('docdegree') ?>" placeholder="e.g., MBBS, MD">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Experience (years)</label>
                    <input type="number" name="docexperience" class="form-control" value="<?= old('docexperience') ?? 0 ?>" min="0">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Bio</label>
                <textarea name="docbio" class="form-control" rows="3"><?= old('docbio') ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Consultation Fee ($)</label>
                <input type="number" name="docconsultation_fee" class="form-control" step="0.01" value="<?= old('docconsultation_fee') ?? 0 ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Password *</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Add Doctor
            </button>
        </form>
    </div>
</div>