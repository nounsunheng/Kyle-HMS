<?php
/**
 * Kyle-HMS System Reports
 * Generate system statistics and reports
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('a');

// Date range
$startDate = isset($_GET['start_date']) ? sanitize($_GET['start_date']) : date('Y-m-01');
$endDate = isset($_GET['end_date']) ? sanitize($_GET['end_date']) : date('Y-m-d');

try {
    // Overall Stats
    $stmt = $conn->query("SELECT COUNT(*) as count FROM doctor WHERE status = 'active'");
    $totalDoctors = $stmt->fetch()['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM patient");
    $totalPatients = $stmt->fetch()['count'];
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM specialties");
    $totalSpecialties = $stmt->fetch()['count'];
    
    // Appointments by Status
    $stmt = $conn->prepare("
        SELECT 
            status,
            COUNT(*) as count
        FROM appointment
        WHERE appodate BETWEEN ? AND ?
        GROUP BY status
    ");
    $stmt->execute([$startDate, $endDate]);
    $appointmentsByStatus = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // Top Doctors
    $stmt = $conn->prepare("
        SELECT 
            d.docname,
            sp.name as specialty,
            COUNT(DISTINCT a.appoid) as appointment_count,
            COUNT(DISTINCT a.pid) as patient_count
        FROM doctor d
        LEFT JOIN specialties sp ON d.specialties = sp.id
        LEFT JOIN schedule s ON d.docid = s.docid
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid 
            AND a.appodate BETWEEN ? AND ?
        GROUP BY d.docid
        ORDER BY appointment_count DESC
        LIMIT 5
    ");
    $stmt->execute([$startDate, $endDate]);
    $topDoctors = $stmt->fetchAll();
    
    // Monthly Trends
    $stmt = $conn->prepare("
        SELECT 
            DATE_FORMAT(appodate, '%Y-%m') as month,
            COUNT(*) as count
        FROM appointment
        WHERE appodate >= DATE_SUB(?, INTERVAL 6 MONTH)
        GROUP BY month
        ORDER BY month
    ");
    $stmt->execute([$endDate]);
    $monthlyTrends = $stmt->fetchAll();
    
    // Specialties Distribution
    $stmt = $conn->query("
        SELECT 
            sp.name,
            COUNT(DISTINCT d.docid) as doctor_count
        FROM specialties sp
        LEFT JOIN doctor d ON sp.id = d.specialties
        GROUP BY sp.id
        ORDER BY doctor_count DESC
        LIMIT 10
    ");
    $specialtyDistribution = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Reports Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/admin.css">
</head>
<body class="admin-dashboard">
    
    <div class="dashboard-wrapper">
        <?php include '../includes/admin_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/admin_navbar.php'; ?>
            
            <div class="content-area">
                
                <!-- Date Range Filter -->
                <div class="content-card mb-4">
                    <form method="GET" action="" class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" value="<?php echo $startDate; ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" value="<?php echo $endDate; ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter me-2"></i> Generate Report
                            </button>
                        </div>
                    </form>
                </div>
                
                <!-- System Overview -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="report-card">
                            <div class="report-header">
                                <h6><i class="fas fa-chart-bar me-2"></i> System Overview</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="report-metric">
                                        <div class="report-metric-value"><?php echo $totalDoctors; ?></div>
                                        <div class="report-metric-label">Active Doctors</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="report-metric">
                                        <div class="report-metric-value"><?php echo $totalPatients; ?></div>
                                        <div class="report-metric-label">Registered Patients</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="report-metric">
                                        <div class="report-metric-value"><?php echo $totalSpecialties; ?></div>
                                        <div class="report-metric-label">Medical Specialties</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="report-card">
                            <div class="report-header">
                                <h6><i class="fas fa-calendar-check me-2"></i> Appointments (<?php echo formatDate($startDate); ?> - <?php echo formatDate($endDate); ?>)</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <div class="report-metric">
                                        <div class="report-metric-value"><?php echo array_sum($appointmentsByStatus); ?></div>
                                        <div class="report-metric-label">Total</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="report-metric">
                                        <div class="report-metric-value" style="color: #ffc107;"><?php echo $appointmentsByStatus['pending'] ?? 0; ?></div>
                                        <div class="report-metric-label">Pending</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="report-metric">
                                        <div class="report-metric-value" style="color: #0dcaf0;"><?php echo $appointmentsByStatus['confirmed'] ?? 0; ?></div>
                                        <div class="report-metric-label">Confirmed</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="report-metric">
                                        <div class="report-metric-value" style="color: #198754;"><?php echo $appointmentsByStatus['completed'] ?? 0; ?></div>
                                        <div class="report-metric-label">Completed</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Top Doctors -->
                <div class="row g-4 mb-4">
                    <div class="col-lg-6">
                        <div class="report-card">
                            <div class="report-header">
                                <h6><i class="fas fa-trophy me-2"></i> Top Performing Doctors</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Rank</th>
                                            <th>Doctor</th>
                                            <th>Specialty</th>
                                            <th>Appointments</th>
                                            <th>Patients</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($topDoctors)): ?>
                                            <?php foreach ($topDoctors as $index => $doctor): ?>
                                                <tr>
                                                    <td>
                                                        <?php if ($index === 0): ?>
                                                            <i class="fas fa-medal" style="color: #ffd700;"></i>
                                                        <?php elseif ($index === 1): ?>
                                                            <i class="fas fa-medal" style="color: #c0c0c0;"></i>
                                                        <?php elseif ($index === 2): ?>
                                                            <i class="fas fa-medal" style="color: #cd7f32;"></i>
                                                        <?php else: ?>
                                                            <?php echo $index + 1; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><strong><?php echo htmlspecialchars($doctor['docname']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($doctor['specialty']); ?></td>
                                                    <td><?php echo $doctor['appointment_count']; ?></td>
                                                    <td><?php echo $doctor['patient_count']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No data available</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6">
                        <div class="report-card">
                            <div class="report-header">
                                <h6><i class="fas fa-chart-pie me-2"></i> Specialty Distribution</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Specialty</th>
                                            <th>Doctors</th>
                                            <th>Distribution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($specialtyDistribution)): ?>
                                            <?php 
                                            $totalDoctorsInTop = array_sum(array_column($specialtyDistribution, 'doctor_count'));
                                            foreach ($specialtyDistribution as $spec): 
                                                $percentage = $totalDoctorsInTop > 0 ? ($spec['doctor_count'] / $totalDoctorsInTop) * 100 : 0;
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($spec['name']); ?></td>
                                                    <td><?php echo $spec['doctor_count']; ?></td>
                                                    <td>
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar bg-primary" style="width: <?php echo $percentage; ?>%">
                                                                <?php echo round($percentage, 1); ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">No data available</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Export Actions -->
                <div class="text-center mb-4">
                    <button class="btn btn-success me-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> Print Report
                    </button>
                    <button class="btn btn-info" onclick="alert('PDF export feature coming soon!')">
                        <i class="fas fa-file-pdf me-2"></i> Export to PDF
                    </button>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
</body>
</html>