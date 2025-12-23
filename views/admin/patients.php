<?php
$pageTitle = 'Manage Patients';
$currentPage = 'patients';
$contentView = 'admin.patients-content';
require __DIR__ . '/../layouts/admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3><i class="fas fa-users"></i> Patients Management</h3>
    <div>
        <form method="GET" class="d-inline">
            <input type="text" name="search" class="form-control d-inline-block" style="width: 300px;" placeholder="Search patients..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </form>
        <a href="<?= url('/admin/patients/create') ?>" class="btn btn-success ms-2">
            <i class="fas fa-plus"></i> Add Patient
        </a>
    </div>
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
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Age</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($patients)): ?>
                        <tr><td colspan="7" class="text-center">No patients found</td></tr>
                    <?php else: ?>
                        <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?= $patient['pid'] ?? '' ?></td>
                                <td><?= htmlspecialchars($patient['pname'] ?? '') ?></td>
                                <td><?= htmlspecialchars($patient['pemail'] ?? '') ?></td>
                                <td><?= htmlspecialchars($patient['ptel'] ?? '') ?></td>
                                <td><?= ucfirst($patient['pgender'] ?? '') ?></td>
                                <td><?= calculateAge($patient['pdob'] ?? '') ?> years</td>
                                <td>
                                    <a href="<?= url('/admin/patients/' . ($patient['pid'] ?? '')) ?>/edit" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?= url('/admin/patients/' . ($patient['pid'] ?? '')) ?>/delete" class="d-inline">
                                        <?= App\Config\Security::csrfField() ?>
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this patient?')">
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