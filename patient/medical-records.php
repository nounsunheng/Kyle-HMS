<?php
/**
 * Kyle-HMS Medical Records
 * View patient medical history
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('p');

$userEmail = getCurrentUserEmail();
$userId = getUserId($userEmail, 'p');

// Fetch medical records
try {
    $stmt = $conn->prepare("
        SELECT 
            mr.*,
            d.docname,
            d.profile_image as doc_image,
            sp.name as specialty_name,
            sp.icon as specialty_icon
        FROM medical_records mr
        JOIN doctor d ON mr.docid = d.docid
        JOIN specialties sp ON d.specialties = sp.id
        WHERE mr.pid = ?
        ORDER BY mr.record_date DESC, mr.created_at DESC
    ");
    $stmt->execute([$userId]);
    $records = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Medical Records Error: " . $e->getMessage());
    $records = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Records - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
</head>
<body>
    
    <div class="dashboard-wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/patient_navbar.php'; ?>
            
            <div class="content-area">
                
                <?php displayFlashMessage(); ?>
                
                <div class="content-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-file-medical"></i> Medical Records
                        </h5>
                        <p class="text-muted mb-0">Your complete medical history and consultations</p>
                    </div>
                    
                    <?php if (!empty($records)): ?>
                        <div class="timeline">
                            <?php foreach ($records as $index => $record): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($record['doc_image']); ?>" 
                                                             alt="Doctor" 
                                                             class="rounded-circle me-3" 
                                                             style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #4361ee;"
                                                             onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                                                        <div>
                                                            <h6 class="mb-0"><?php echo htmlspecialchars($record['docname']); ?></h6>
                                                            <small class="text-primary">
                                                                <i class="<?php echo htmlspecialchars($record['specialty_icon']); ?> me-1"></i>
                                                                <?php echo htmlspecialchars($record['specialty_name']); ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="badge bg-primary"><?php echo formatDate($record['record_date'], 'M d, Y'); ?></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <div class="bg-light rounded p-3">
                                                            <div class="d-flex align-items-start">
                                                                <i class="fas fa-stethoscope text-primary me-2 mt-1"></i>
                                                                <div class="flex-grow-1">
                                                                    <strong class="d-block mb-1">Diagnosis</strong>
                                                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if (!empty($record['prescription'])): ?>
                                                        <div class="col-md-12">
                                                            <div class="bg-light rounded p-3">
                                                                <div class="d-flex align-items-start">
                                                                    <i class="fas fa-pills text-success me-2 mt-1"></i>
                                                                    <div class="flex-grow-1">
                                                                        <strong class="d-block mb-1">Prescription</strong>
                                                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['prescription'])); ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($record['test_results'])): ?>
                                                        <div class="col-md-12">
                                                            <div class="bg-light rounded p-3">
                                                                <div class="d-flex align-items-start">
                                                                    <i class="fas fa-vial text-info me-2 mt-1"></i>
                                                                    <div class="flex-grow-1">
                                                                        <strong class="d-block mb-1">Test Results</strong>
                                                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['test_results'])); ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($record['notes'])): ?>
                                                        <div class="col-md-12">
                                                            <div class="bg-light rounded p-3">
                                                                <div class="d-flex align-items-start">
                                                                    <i class="fas fa-notes-medical text-warning me-2 mt-1"></i>
                                                                    <div class="flex-grow-1">
                                                                        <strong class="d-block mb-1">Doctor's Notes</strong>
                                                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['notes'])); ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($record['follow_up_date'])): ?>
                                                        <div class="col-md-12">
                                                            <div class="alert alert-info mb-0">
                                                                <i class="fas fa-calendar-day me-2"></i>
                                                                <strong>Follow-up Scheduled:</strong> 
                                                                <?php echo formatDate($record['follow_up_date'], 'F j, Y'); ?>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Recorded on <?php echo formatDate($record['created_at'], 'F j, Y g:i A'); ?>
                                                    </small>
                                                    <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                                        <i class="fas fa-print me-1"></i> Print
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-file-medical empty-state-icon"></i>
                            <h4>No Medical Records Yet</h4>
                            <p>Your medical records will appear here after your consultations with doctors.</p>
                            <a href="book-appointment.php" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i> Book an Appointment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
    
    <style>
        .timeline {
            position: relative;
            padding-left: 3rem;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg, #4361ee 0%, #06d6a0 100%);
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }
        
        .timeline-marker {
            position: absolute;
            left: -2.3rem;
            top: 20px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #4361ee;
            border: 3px solid white;
            box-shadow: 0 0 0 2px #4361ee;
        }
        
        .timeline-content {
            animation: slideInRight 0.5s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @media print {
            .sidebar, .top-navbar, .btn, .timeline-marker, .timeline::before {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            .card {
                break-inside: avoid;
            }
        }
    </style>
</body>
</html>