<div class="row">
    <div class="col-md-12">
        <h2>Welcome, <?= htmlspecialchars($patient['pname'] ?? 'Patient') ?>!</h2>
        <p class="text-muted">Manage your appointments and health records</p>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5>Total Appointments</h5>
                <h2><?= $stats['total'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5>Completed</h5>
                <h2><?= $stats['completed'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5>Pending</h5>
                <h2><?= $stats['pending'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <h5>Cancelled</h5>
                <h2><?= $stats['cancelled'] ?? 0 ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Upcoming Appointments</h5>
            </div>
            <div class="card-body">
                <?php if (empty($upcoming_appointments)): ?>
                    <p class="text-muted">No upcoming appointments</p>
                    <a href="<?= url('/patient/doctors') ?>" class="btn btn-primary">Book Appointment</a>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcoming_appointments as $apt): ?>
                                    <tr>
                                        <td><?= formatDate($apt['appodate'] ?? '') ?></td>
                                        <td><?= formatTime($apt['appotime'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($apt['doctor_name'] ?? '') ?></td>
                                        <td><span class="badge bg-info"><?= ucfirst($apt['appointment_status'] ?? '') ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= url('/patient/doctors') ?>" class="btn btn-primary">
                        <i class="fas fa-user-md"></i> Find Doctor
                    </a>
                    <a href="<?= url('/patient/book-appointment') ?>" class="btn btn-success">
                        <i class="fas fa-calendar-plus"></i> Book Appointment
                    </a>
                    <a href="<?= url('/patient/appointments') ?>" class="btn btn-info">
                        <i class="fas fa-calendar-check"></i> My Appointments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>