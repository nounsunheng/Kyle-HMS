<?php
/**
 * Kyle-HMS Book Appointment
 * Step-by-step appointment booking
 */
require_once '../config/config.php';
require_once '../config/session.php';

requireLogin('p');

$userEmail = getCurrentUserEmail();
$userId = getUserId($userEmail, 'p');

$errors = [];
$success = false;

// Pre-selected doctor from URL
$selectedDoctorId = isset($_GET['doctor']) ? (int)$_GET['doctor'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request. Please try again.';
    } else {
        $doctorId = (int)$_POST['doctor_id'];
        $scheduleId = (int)$_POST['schedule_id'];
        $symptoms = sanitize($_POST['symptoms'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
        
        // Validation
        if ($doctorId <= 0) {
            $errors[] = 'Please select a doctor';
        }
        
        if ($scheduleId <= 0) {
            $errors[] = 'Please select an available schedule';
        }
        
        if (empty($symptoms)) {
            $errors[] = 'Please describe your symptoms';
        }
        
        // If no errors, proceed with booking
        if (empty($errors)) {
            try {
                $conn->beginTransaction();
                
                // Check schedule availability
                $stmt = $conn->prepare("
                    SELECT scheduleid, scheduledate, scheduletime, nop, booked, title
                    FROM schedule 
                    WHERE scheduleid = ? 
                    AND status = 'active' 
                    AND (nop - booked) > 0
                    AND scheduledate >= CURDATE()
                    FOR UPDATE
                ");
                $stmt->execute([$scheduleId]);
                $schedule = $stmt->fetch();
                
                if (!$schedule) {
                    throw new Exception('Selected time slot is no longer available');
                }
                
                // Check if patient already has appointment at this time
                $stmt = $conn->prepare("
                    SELECT COUNT(*) as count 
                    FROM appointment 
                    WHERE pid = ? 
                    AND scheduleid = ?
                    AND status IN ('pending', 'confirmed')
                ");
                $stmt->execute([$userId, $scheduleId]);
                
                if ($stmt->fetch()['count'] > 0) {
                    throw new Exception('You already have an appointment at this time');
                }
                
                // Generate appointment number
                $appointmentNumber = generateAppointmentNumber();
                
                // Get next appointment slot number for this schedule
                $appoNum = $schedule['booked'] + 1;
                
                // Insert appointment
                $stmt = $conn->prepare("
                    INSERT INTO appointment (
                        pid, apponum, scheduleid, appodate, appotime, 
                        appointment_number, status, symptoms, notes
                    ) VALUES (?, ?, ?, ?, ?, ?, 'pending', ?, ?)
                ");
                
                $stmt->execute([
                    $userId,
                    $appoNum,
                    $scheduleId,
                    $schedule['scheduledate'],
                    $schedule['scheduletime'],
                    $appointmentNumber,
                    $symptoms,
                    $notes
                ]);
                
                $conn->commit();
                
                // Log activity
                logActivity('book_appointment', 'Booked appointment: ' . $appointmentNumber);
                
                setFlashMessage('Appointment booked successfully! Appointment #: ' . $appointmentNumber, 'success');
                redirect('/patient/appointments.php');
                
            } catch (Exception $e) {
                $conn->rollBack();
                error_log("Booking Error: " . $e->getMessage());
                $errors[] = $e->getMessage();
            }
        }
    }
}

// Fetch all doctors with available schedules
try {
    $stmt = $conn->prepare("
        SELECT DISTINCT
            d.docid,
            d.docname,
            d.profile_image,
            sp.name as specialty_name,
            sp.icon as specialty_icon
        FROM doctor d
        JOIN specialties sp ON d.specialties = sp.id
        WHERE d.status = 'active'
        AND EXISTS (
            SELECT 1 FROM schedule s 
            WHERE s.docid = d.docid 
            AND s.status = 'active' 
            AND s.scheduledate >= CURDATE()
            AND (s.nop - s.booked) > 0
        )
        ORDER BY d.docname
    ");
    $stmt->execute();
    $doctors = $stmt->fetchAll();
    
} catch (PDOException $e) {
    error_log("Fetch Doctors Error: " . $e->getMessage());
    $doctors = [];
}

$csrfToken = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - <?php echo APP_NAME; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo ASSETS_PATH; ?>/css/patient.css">
    
    <style>
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            padding: 1rem;
        }
        
        .step::before {
            content: '';
            position: absolute;
            top: 30px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: -1;
        }
        
        .step:first-child::before {
            left: 50%;
        }
        
        .step:last-child::before {
            right: 50%;
        }
        
        .step-number {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .step.active .step-number {
            background: linear-gradient(135deg, #4361ee 0%, #3f37c9 100%);
            color: white;
        }
        
        .step.completed .step-number {
            background: #06d6a0;
            color: white;
        }
        
        .step-title {
            font-weight: 600;
            color: #6c757d;
        }
        
        .step.active .step-title {
            color: #4361ee;
        }
        
        .step.completed .step-title {
            color: #06d6a0;
        }
        
        .schedule-card {
            border: 2px solid #e9ecef;
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 1rem;
        }
        
        .schedule-card:hover {
            border-color: #4361ee;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
        }
        
        .schedule-card input[type="radio"] {
            display: none;
        }
        
        .schedule-card input[type="radio"]:checked + .schedule-content {
            border-left: 4px solid #4361ee;
            padding-left: 1rem;
        }
        
        .schedule-card.selected {
            border-color: #4361ee;
            background: rgba(67, 97, 238, 0.05);
        }
    </style>
</head>
<body>
    
    <div class="dashboard-wrapper">
        <?php include '../includes/patient_sidebar.php'; ?>
        
        <div class="main-content">
            <?php include '../includes/patient_navbar.php'; ?>
            
            <div class="content-area">
                
                <div class="content-card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-calendar-plus"></i> Book New Appointment
                        </h5>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator mb-4">
                        <div class="step active" id="step1Indicator">
                            <div class="step-number">1</div>
                            <div class="step-title">Select Doctor</div>
                        </div>
                        <div class="step" id="step2Indicator">
                            <div class="step-number">2</div>
                            <div class="step-title">Choose Schedule</div>
                        </div>
                        <div class="step" id="step3Indicator">
                            <div class="step-number">3</div>
                            <div class="step-title">Provide Details</div>
                        </div>
                    </div>
                    
                    <form method="POST" action="" id="bookingForm" data-prevent-double-submit>
                        <input type="hidden" name="csrf_token" value="<?php echo $csrfToken; ?>">
                        <input type="hidden" name="doctor_id" id="doctorId" value="<?php echo $selectedDoctorId; ?>">
                        <input type="hidden" name="schedule_id" id="scheduleId">
                        
                        <!-- Step 1: Select Doctor -->
                        <div class="booking-step" id="step1">
                            <h5 class="mb-3">Select a Doctor</h5>
                            
                            <?php if (!empty($doctors)): ?>
                                <div class="row g-3">
                                    <?php foreach ($doctors as $doctor): ?>
                                        <div class="col-md-6">
                                            <div class="doctor-card" onclick="selectDoctor(<?php echo $doctor['docid']; ?>)" 
                                                 id="doctor<?php echo $doctor['docid']; ?>"
                                                 style="cursor: pointer; <?php echo ($selectedDoctorId == $doctor['docid']) ? 'border: 2px solid #4361ee; background: rgba(67, 97, 238, 0.05);' : ''; ?>">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo UPLOADS_URL . '/avatars/' . htmlspecialchars($doctor['profile_image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($doctor['docname']); ?>"
                                                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #4361ee;"
                                                         onerror="this.src='<?php echo UPLOADS_URL; ?>/avatars/default-doctor.png'">
                                                    <div class="ms-3">
                                                        <h6 class="mb-1"><?php echo htmlspecialchars($doctor['docname']); ?></h6>
                                                        <p class="mb-0 text-primary">
                                                            <i class="<?php echo htmlspecialchars($doctor['specialty_icon']); ?> me-1"></i>
                                                            <small><?php echo htmlspecialchars($doctor['specialty_name']); ?></small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="text-end mt-4">
                                    <button type="button" class="btn btn-primary" onclick="goToStep(2)" id="step1Next" disabled>
                                        Next: Choose Schedule <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>
                                    No doctors with available schedules at the moment. Please check back later.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Step 2: Choose Schedule -->
                        <div class="booking-step" id="step2" style="display: none;">
                            <h5 class="mb-3">Choose Available Schedule</h5>
                            <div id="schedulesList">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3 text-muted">Loading available schedules...</p>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" onclick="goToStep(1)">
                                    <i class="fas fa-arrow-left me-2"></i> Back
                                </button>
                                <button type="button" class="btn btn-primary" onclick="goToStep(3)" id="step2Next" disabled>
                                    Next: Provide Details <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Step 3: Provide Details -->
                        <div class="booking-step" id="step3" style="display: none;">
                            <h5 class="mb-3">Appointment Details</h5>
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="symptoms" class="form-label">
                                        Reason for Visit / Symptoms <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" 
                                              id="symptoms" 
                                              name="symptoms" 
                                              rows="4" 
                                              placeholder="Please describe your symptoms or reason for consultation..."
                                              required></textarea>
                                    <div class="form-text">Be as detailed as possible to help the doctor prepare</div>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="notes" class="form-label">Additional Notes (Optional)</label>
                                    <textarea class="form-control" 
                                              id="notes" 
                                              name="notes" 
                                              rows="3" 
                                              placeholder="Any other information you'd like the doctor to know..."></textarea>
                                </div>
                            </div>
                            
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Please Note:</strong> Your appointment will be confirmed once reviewed by the doctor. 
                                You will receive a notification about the status.
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" onclick="goToStep(2)">
                                    <i class="fas fa-arrow-left me-2"></i> Back
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-2"></i> Confirm Booking
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_PATH; ?>/js/main.js"></script>
    
    <script>
        let currentStep = 1;
        let selectedDoctor = <?php echo $selectedDoctorId; ?>;
        let selectedSchedule = 0;
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            if (selectedDoctor > 0) {
                document.getElementById('step1Next').disabled = false;
                highlightDoctor(selectedDoctor);
            }
        });
        
        function selectDoctor(doctorId) {
            selectedDoctor = doctorId;
            document.getElementById('doctorId').value = doctorId;
            document.getElementById('step1Next').disabled = false;
            
            // Visual feedback
            document.querySelectorAll('[id^="doctor"]').forEach(card => {
                card.style.border = '1px solid #dee2e6';
                card.style.background = 'white';
            });
            
            highlightDoctor(doctorId);
        }
        
        function highlightDoctor(doctorId) {
            const card = document.getElementById('doctor' + doctorId);
            if (card) {
                card.style.border = '2px solid #4361ee';
                card.style.background = 'rgba(67, 97, 238, 0.05)';
            }
        }
        
        function goToStep(step) {
            // Hide all steps
            for (let i = 1; i <= 3; i++) {
                document.getElementById('step' + i).style.display = 'none';
                document.getElementById('step' + i + 'Indicator').classList.remove('active');
                document.getElementById('step' + i + 'Indicator').classList.remove('completed');
            }
            
            // Mark completed steps
            for (let i = 1; i < step; i++) {
                document.getElementById('step' + i + 'Indicator').classList.add('completed');
            }
            
            // Show current step
            document.getElementById('step' + step).style.display = 'block';
            document.getElementById('step' + step + 'Indicator').classList.add('active');
            
            currentStep = step;
            
            // Load schedules when entering step 2
            if (step === 2 && selectedDoctor > 0) {
                loadSchedules(selectedDoctor);
            }
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        async function loadSchedules(doctorId) {
            const container = document.getElementById('schedulesList');
            
            try {
                const response = await fetch(`../api/get-schedules.php?doctor_id=${doctorId}`);
                const data = await response.json();
                
                if (data.success && data.schedules.length > 0) {
                    let html = '<div class="row g-3">';
                    
                    data.schedules.forEach(schedule => {
                        const available = schedule.nop - schedule.booked;
                        html += `
                            <div class="col-md-6">
                                <label class="schedule-card" onclick="selectSchedule(${schedule.scheduleid})">
                                    <input type="radio" name="schedule_radio" value="${schedule.scheduleid}">
                                    <div class="schedule-content">
                                        <h6 class="mb-2">${schedule.title}</h6>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div><i class="fas fa-calendar me-2 text-primary"></i>${formatDate(schedule.scheduledate)}</div>
                                                <div><i class="fas fa-clock me-2 text-primary"></i>${formatTime(schedule.scheduletime)}</div>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success">${available} slots left</span>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            No available schedules for this doctor at the moment.
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading schedules:', error);
                container.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Error loading schedules. Please try again.
                    </div>
                `;
            }
        }
        
        function selectSchedule(scheduleId) {
            selectedSchedule = scheduleId;
            document.getElementById('scheduleId').value = scheduleId;
            document.getElementById('step2Next').disabled = false;
            
            // Visual feedback
            document.querySelectorAll('.schedule-card').forEach(card => {
                card.classList.remove('selected');
            });
            event.currentTarget.classList.add('selected');
        }
        
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        }
        
        function formatTime(timeStr) {
            const [hours, minutes] = timeStr.split(':');
            const hour = parseInt(hours);
            const ampm = hour >= 12 ? 'PM' : 'AM';
            const displayHour = hour % 12 || 12;
            return `${displayHour}:${minutes} ${ampm}`;
        }
    </script>
</body>
</html>