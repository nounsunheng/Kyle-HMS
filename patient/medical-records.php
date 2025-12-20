<?php
/**
 * Kyle-HMS Medical Records
 * View patient medical history with file downloads
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('p');

$userEmail = getCurrentUserEmail();
$userId = getUserId($userEmail, 'p');

// Fetch medical records with file counts
try {
    $stmt = $conn->prepare("
        SELECT 
            mr.*,
            d.docname,
            d.profile_image as doc_image,
            sp.name as specialty_name,
            sp.icon as specialty_icon,
            (SELECT COUNT(*) FROM medical_record_files WHERE record_id = mr.record_id) as file_count
        FROM medical_records mr
        JOIN doctor d ON mr.docid = d.docid
        JOIN specialties sp ON d.specialties = sp.id
        WHERE mr.pid = ?
        ORDER BY mr.record_date DESC, mr.created_at DESC
    ");
    $stmt->execute([$userId]);
    $records = $stmt->fetchAll();
    
    // Fetch files for each record
    $recordFiles = [];
    if (!empty($records)) {
        $recordIds = array_column($records, 'record_id');
        $placeholders = str_repeat('?,', count($recordIds) - 1) . '?';
        
        $stmt = $conn->prepare("
            SELECT * FROM medical_record_files 
            WHERE record_id IN ($placeholders)
            ORDER BY uploaded_at ASC
        ");
        $stmt->execute($recordIds);
        $files = $stmt->fetchAll();
        
        foreach ($files as $file) {
            $recordFiles[$file['record_id']][] = $file;
        }
    }
    
} catch (PDOException $e) {
    error_log("Medical Records Error: " . $e->getMessage());
    $records = [];
    $recordFiles = [];
}

// Helper function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Helper function to get file icon
function getFileIcon($fileType) {
    if (strpos($fileType, 'pdf') !== false) {
        return 'fas fa-file-pdf text-danger';
    } elseif (strpos($fileType, 'image') !== false) {
        return 'fas fa-file-image text-primary';
    } elseif (strpos($fileType, 'word') !== false || strpos($fileType, 'document') !== false) {
        return 'fas fa-file-word text-info';
    } else {
        return 'fas fa-file text-secondary';
    }
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
    
    <style>
        .file-attachment {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .file-attachment:hover {
            background: #e9ecef;
            transform: translateX(5px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .file-icon-wrapper {
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border-radius: 8px;
            margin-right: 12px;
        }
        
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
            .sidebar, .top-navbar, .btn, .timeline-marker, .timeline::before, .file-attachment {
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
                                                        <div class="badge bg-primary mb-1"><?php echo formatDate($record['record_date'], 'M d, Y'); ?></div>
                                                        <?php if ($record['file_count'] > 0): ?>
                                                            <div class="badge bg-info">
                                                                <i class="fas fa-paperclip me-1"></i><?php echo $record['file_count']; ?> file(s)
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="row g-3">
                                                    <div class="col-md-12">
                                                        <div class="bg-light rounded p-3">
                                                            <div class="d-flex align-items-start">
                                                                <i class="fas fa-stethoscope text-primary me-2 mt-1 fs-5"></i>
                                                                <div class="flex-grow-1">
                                                                    <strong class="d-block mb-2">Diagnosis</strong>
                                                                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <?php if (!empty($record['prescription'])): ?>
                                                        <div class="col-md-12">
                                                            <div class="bg-light rounded p-3">
                                                                <div class="d-flex align-items-start">
                                                                    <i class="fas fa-pills text-success me-2 mt-1 fs-5"></i>
                                                                    <div class="flex-grow-1">
                                                                        <strong class="d-block mb-2">Prescription</strong>
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
                                                                    <i class="fas fa-vial text-info me-2 mt-1 fs-5"></i>
                                                                    <div class="flex-grow-1">
                                                                        <strong class="d-block mb-2">Test Results</strong>
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
                                                                    <i class="fas fa-notes-medical text-warning me-2 mt-1 fs-5"></i>
                                                                    <div class="flex-grow-1">
                                                                        <strong class="d-block mb-2">Doctor's Notes</strong>
                                                                        <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['notes'])); ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <!-- ATTACHED FILES SECTION -->
                                                    <?php if (!empty($recordFiles[$record['record_id']])): ?>
                                                        <div class="col-md-12">
                                                            <div class="border rounded p-3 bg-white">
                                                                <h6 class="mb-3">
                                                                    <i class="fas fa-paperclip text-primary me-2"></i>
                                                                    Attached Documents (<?php echo count($recordFiles[$record['record_id']]); ?>)
                                                                </h6>
                                                                <?php foreach ($recordFiles[$record['record_id']] as $file): ?>
                                                                    <div class="file-attachment d-flex align-items-center">
                                                                        <div class="file-icon-wrapper">
                                                                            <i class="<?php echo getFileIcon($file['file_type']); ?> fs-4"></i>
                                                                        </div>
                                                                        <div class="flex-grow-1">
                                                                            <div class="fw-bold"><?php echo htmlspecialchars($file['original_filename']); ?></div>
                                                                            <small class="text-muted">
                                                                                <?php echo formatFileSize($file['file_size']); ?> • 
                                                                                Uploaded <?php echo formatDate($file['uploaded_at'], 'M d, Y g:i A'); ?>
                                                                            </small>
                                                                        </div>
                                                                        <a href="download-file.php?file_id=<?php echo $file['file_id']; ?>" 
                                                                           class="btn btn-sm btn-primary" 
                                                                           target="_blank">
                                                                            <i class="fas fa-download me-1"></i> Download
                                                                        </a>
                                                                    </div>
                                                                <?php endforeach; ?>
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
</body>
</html>