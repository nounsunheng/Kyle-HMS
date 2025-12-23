<?php
/**
 * Admin Dashboard View
 * Main admin dashboard with statistics and overview
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

// This view receives data from AdminDashboardController
// Available variables: $stats, $recent_appointments, $recent_patients, $recent_doctors
?>

<div class="page-header">
    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
    <p class="text-muted">Welcome back, Administrator! Here's what's happening today.</p>
</div>

<!-- Statistics Cards -->
<div class="row">
    <!-- Total Patients -->
    <div class="col-md-3">
        <div class="stat-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Total Patients</p>
                    <h3><?= number_format($stats['total_patients'] ?? 0) ?></h3>
                    <small><?= ($stats['active_patients'] ?? 0) ?> Active</small>
                </div>
                <div>
                    <i class="fas fa-users fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Doctors -->
    <div class="col-md-3">
        <div class="stat-card bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Total Doctors</p>
                    <h3><?= number_format($stats['total_doctors'] ?? 0) ?></h3>
                    <small><?= ($stats['active_doctors'] ?? 0) ?> Active</small>
                </div>
                <div>
                    <i class="fas fa-user-md fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Appointments -->
    <div class="col-md-3">
        <div class="stat-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Total Appointments</p>
                    <h3><?= number_format($stats['total_appointments'] ?? 0) ?></h3>
                    <small><?= ($stats['today_appointments'] ?? 0) ?> Today</small>
                </div>
                <div>
                    <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Appointments -->
    <div class="col-md-3">
        <div class="stat-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-1">Pending</p>
                    <h3><?= number_format($stats['pending_appointments'] ?? 0) ?></h3>
                    <small>Need Review</small>
                </div>
                <div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <!-- Appointments by Status -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Appointments by Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Appointments by Specialty -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Top Specialties</h5>
            </div>
            <div class="card-body">
                <canvas id="specialtyChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row mt-4">
    <!-- Recent Appointments -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Recent Appointments</h5>
                <a href="<?= url('/admin/appointments') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent_appointments)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent appointments</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_appointments as $appointment): ?>
                                    <tr>
                                        <td>#<?= $appointment['appointment_number'] ?? '' ?></td>
                                        <td><?= htmlspecialchars($appointment['patient_name'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($appointment['doctor_name'] ?? '') ?></td>
                                        <td>
                                            <?= formatDate($appointment['appodate'] ?? '') ?><br>
                                            <small class="text-muted"><?= formatTime($appointment['appotime'] ?? '') ?></small>
                                        </td>
                                        <td>
                                            <?php
                                            $status = $appointment['appointment_status'] ?? 'pending';
                                            $badgeClass = [
                                                'pending' => 'warning',
                                                'confirmed' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                'no_show' => 'secondary'
                                            ][$status] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Users -->
    <div class="col-md-4">
        <!-- Quick Actions -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('/admin/doctors/create') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-plus"></i> Add Doctor
                    </a>
                    <a href="<?= url('/admin/patients/create') ?>" class="btn btn-outline-success">
                        <i class="fas fa-plus"></i> Add Patient
                    </a>
                    <a href="<?= url('/admin/specialties/create') ?>" class="btn btn-outline-info">
                        <i class="fas fa-plus"></i> Add Specialty
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Patients -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-plus"></i> Recent Patients</h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recent_patients)): ?>
                    <div class="text-center py-3">
                        <p class="text-muted mb-0">No recent registrations</p>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_patients as $patient): ?>
                            <a href="<?= url('/admin/patients/' . ($patient['pid'] ?? '')) ?>" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                                    <div>
                                        <h6 class="mb-0"><?= htmlspecialchars($patient['pname'] ?? '') ?></h6>
                                        <small class="text-muted"><?= timeAgo($patient['created_at'] ?? '') ?></small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Appointments by Status Chart
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Confirmed', 'Completed', 'Cancelled'],
            datasets: [{
                data: [
                    <?= $stats['pending_appointments'] ?? 0 ?>,
                    <?= $appointments_by_status['confirmed'] ?? 0 ?>,
                    <?= $stats['completed_appointments'] ?? 0 ?>,
                    <?= $appointments_by_status['cancelled'] ?? 0 ?>
                ],
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

// Specialties Chart
const specialtyCtx = document.getElementById('specialtyChart');
if (specialtyCtx && <?= !empty($appointments_by_specialty) ? 'true' : 'false' ?>) {
    new Chart(specialtyCtx, {
        type: 'bar',
        data: {
            labels: [
                <?php 
                if (!empty($appointments_by_specialty)) {
                    foreach ($appointments_by_specialty as $specialty) {
                        echo "'" . addslashes($specialty['specialty_name'] ?? '') . "',";
                    }
                }
                ?>
            ],
            datasets: [{
                label: 'Appointments',
                data: [
                    <?php 
                    if (!empty($appointments_by_specialty)) {
                        foreach ($appointments_by_specialty as $specialty) {
                            echo ($specialty['count'] ?? 0) . ',';
                        }
                    }
                    ?>
                ],
                backgroundColor: '#667eea'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>