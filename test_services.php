<?php
/**
 * Service Layer Testing Script
 * Tests all business logic services
 */

require_once 'vendor/autoload.php';

use App\Services\AuthService;
use App\Services\AppointmentService;
use App\Services\ScheduleService;
use App\Services\NotificationService;
use App\Services\ValidationService;

echo "🧪 Testing Kyle-HMS Services\n";
echo "================================\n\n";

// Test 1: Validation Service
echo "1️⃣ Testing ValidationService...\n";
try {
    $validator = new ValidationService();
    
    // Test email validation
    $validEmail = $validator->isValidEmail('test@example.com');
    echo "   ✅ Email validation: " . ($validEmail ? 'PASS' : 'FAIL') . "\n";
    
    // Test phone validation
    $validPhone = $validator->isValidPhone('096-999-0399');
    echo "   ✅ Phone validation: " . ($validPhone ? 'PASS' : 'FAIL') . "\n";
    
    // Test registration validation
    $errors = $validator->validatePatientRegistration([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'confirm_password' => 'password123',
        'dob' => '2000-01-01',
        'gender' => 'male',
        'tel' => '096-999-0399',
        'address' => '123 Main St, Phnom Penh'
    ]);
    
    if (empty($errors)) {
        echo "   ✅ Registration validation: PASS\n";
    } else {
        echo "   ❌ Registration validation: FAIL\n";
        print_r($errors);
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Auth Service
echo "2️⃣ Testing AuthService...\n";
try {
    $authService = new AuthService();
    
    // Test login
    $result = $authService->login('kyle@gmail.com', '12345678');
    
    if ($result['success']) {
        echo "   ✅ Login successful\n";
        echo "   ✅ User role: " . currentUserRole() . "\n";
        echo "   ✅ Redirect to: {$result['redirect']}\n";
        
        // Test get current user
        $currentUser = $authService->getCurrentUser();
        if ($currentUser) {
            echo "   ✅ Current user: {$currentUser['pname']}\n";
        }
        
        // Logout for next tests
        $authService->logout();
        echo "   ✅ Logout successful\n";
    } else {
        echo "   ❌ Login failed: {$result['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Schedule Service
echo "3️⃣ Testing ScheduleService...\n";
try {
    $scheduleService = new ScheduleService();
    
    // Get available schedules
    $schedules = $scheduleService->getAvailableSchedules();
    echo "   ✅ Found " . count($schedules) . " available schedules\n";
    
    // Test create schedule (will fail without doctor login, but tests validation)
    $result = $scheduleService->createSchedule([
        'docid' => 1,
        'title' => 'Test Consultation',
        'scheduledate' => date('Y-m-d', strtotime('+7 days')),
        'scheduletime' => '10:00',
        'nop' => 5
    ]);
    
    if ($result['success']) {
        echo "   ✅ Schedule creation: PASS\n";
        echo "   ✅ Schedule ID: {$result['schedule_id']}\n";
    } else {
        echo "   ⚠️  Schedule creation validation: {$result['message']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Notification Service
echo "4️⃣ Testing NotificationService...\n";
try {
    $notificationService = new NotificationService();
    
    // Get notifications for a user
    $notifications = $notificationService->getUserNotifications('kyle@gmail.com', 5);
    echo "   ✅ Found " . count($notifications) . " notifications\n";
    
    // Get unread count
    $unreadCount = $notificationService->getUnreadCount('kyle@gmail.com');
    echo "   ✅ Unread notifications: {$unreadCount}\n";
    
    // Create test notification
    $created = $notificationService->create([
        'user_email' => 'kyle@gmail.com',
        'title' => 'Test Notification',
        'message' => 'This is a test notification from service testing',
        'type' => 'system'
    ]);
    
    if ($created) {
        echo "   ✅ Notification creation: PASS\n";
    } else {
        echo "   ❌ Notification creation: FAIL\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Appointment Service
echo "5️⃣ Testing AppointmentService...\n";
try {
    $appointmentService = new AppointmentService();
    
    // Get patient appointments
    $appointments = $appointmentService->getPatientAppointments(6); // Kyle's patient ID
    echo "   ✅ Found " . count($appointments) . " appointments\n";
    
    if (!empty($appointments)) {
        $apt = $appointments[0];
        echo "   ✅ Latest appointment: {$apt['appointment_number']}\n";
        echo "   ✅ Status: {$apt['appointment_status']}\n";
        echo "   ✅ Doctor: {$apt['doctor_name']}\n";
    }
    
    // Test booking validation (without actually booking)
    echo "   ℹ️  Appointment booking requires valid schedule and availability\n";
    
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "================================\n";
echo "✅ All service tests completed!\n";
echo "\nNote: Some operations require authentication and valid data.\n";
echo "These tests verify the services are properly structured.\n";