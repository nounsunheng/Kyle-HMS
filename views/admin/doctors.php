<?php
$pageTitle = 'Manage Doctors';
$currentPage = 'doctors';
$contentView = 'admin.doctors-content';
require __DIR__ . '/../layouts/admin.php';
?>

<!-- Content -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-user-md"></i> Doctors Management</h3>
    <a href="<?= url('/admin/doctors/create') ?>" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Doctor
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialty</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doctors)): ?>
                        <tr><td colspan="7" class="text-center">No doctors found</td></tr>
                    <?php else: ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <tr>
                                <td><?= $doctor['docid'] ?? '' ?></td>
                                <td><?= htmlspecialchars($doctor['docname'] ?? '') ?></td>
                                <td><?= htmlspecialchars($doctor['docemail'] ?? '') ?></td>
                                <td><?= htmlspecialchars($doctor['specialty_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($doctor['doctel'] ?? '') ?></td>
                                <td>
                                    <?php $status = $doctor['status'] ?? 'inactive'; ?>
                                    <span class="badge bg-<?= $status === 'active' ? 'success' : 'secondary' ?>">
                                        <?= ucfirst($status) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="<?= url('/admin/doctors/' . ($doctor['docid'] ?? '')) ?>/edit" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?= url('/admin/doctors/' . ($doctor['docid'] ?? '')) ?>/delete" class="d-inline">
                                        <?= App\Config\Security::csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this doctor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>