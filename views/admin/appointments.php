<?php
$pageTitle = 'Manage Appointments';
$currentPage = 'appointments';
$contentView = 'admin.appointments-content';
require __DIR__ . '/../layouts/admin.php';
?>

<h3><i class="fas fa-calendar-check"></i> Appointments Management</h3>

<div class="card mt-4">
    <div class="card-body">
        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="7" class="text-center">No appointments found</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $apt): ?>
                            <tr>
                                <td>#<?= $apt['appointment_number'] ?? '' ?></td>
                                <td><?= htmlspecialchars($apt['patient_name'] ?? '') ?></td>
                                <td><?= htmlspecialchars($apt['doctor_name'] ?? '') ?></td>
                                <td><?= formatDate($apt['appodate'] ?? '') ?></td>
                                <td><?= formatTime($apt['appotime'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $status = $apt['appointment_status'] ?? 'pending';
                                    $badgeClass = ['pending' => 'warning', 'confirmed' => 'info', 'completed' => 'success', 'cancelled' => 'danger'][$status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                                </td>
                                <td>
                                    <a href="<?= url('/admin/appointments/' . ($apt['appoid'] ?? '')) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>