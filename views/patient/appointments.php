<?php
$pageTitle = 'My Appointments';
$currentPage = 'appointments';
$contentView = 'patient.appointments-content';
require __DIR__ . '/../layouts/patient.php';
?>

<h2>My Appointments</h2>

<div class="card mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="6" class="text-center">No appointments found</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $apt): ?>
                            <tr>
                                <td>#<?= $apt['appointment_number'] ?? '' ?></td>
                                <td><?= htmlspecialchars($apt['doctor_name'] ?? '') ?></td>
                                <td><?= formatDate($apt['appodate'] ?? '') ?></td>
                                <td><?= formatTime($apt['appotime'] ?? '') ?></td>
                                <td><span class="badge bg-info"><?= ucfirst($apt['appointment_status'] ?? '') ?></span></td>
                                <td>
                                    <?php if (($apt['appointment_status'] ?? '') === 'pending'): ?>
                                        <form method="POST" action="<?= url('/patient/appointments/' . ($apt['appoid'] ?? '')) ?>/cancel" class="d-inline">
                                            <?= App\Config\Security::csrfField() ?>
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel?')">Cancel</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>