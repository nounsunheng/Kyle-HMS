<?php
/**
 * Repository Testing Script
 * Run this to verify all repositories work correctly
 */

require_once 'vendor/autoload.php';

use App\Repositories\UserRepository;
use App\Repositories\PatientRepository;
use App\Repositories\DoctorRepository;
use App\Repositories\AppointmentRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\SpecialtyRepository;

echo "🧪 Testing Kyle-HMS Repositories\n";
echo "================================\n\n";

// Test 1: User Repository
echo "1️⃣ Testing UserRepository...\n";
try {
    $userRepo = new UserRepository();
    $user = $userRepo->findByEmail('kyle@gmail.com');
    
    if ($user) {
        echo "   ✅ Found user: {$user['email']}\n";
        echo "   ✅ User type: {$user['usertype']}\n";
    } else {
        echo "   ❌ User not found\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Patient Repository
echo "2️⃣ Testing PatientRepository...\n";
try {
    $patientRepo = new PatientRepository();
    $patient = $patientRepo->findByEmail('kyle@gmail.com');
    
    if ($patient) {
        echo "   ✅ Found patient: {$patient['pname']}\n";
        echo "   ✅ Patient ID: {$patient['pid']}\n";
        
        // Test statistics
        $stats = $patientRepo->getWithStatistics($patient['pid']);
        echo "   ✅ Total appointments: {$stats['total_appointments']}\n";
    } else {
        echo "   ❌ Patient not found\n";
    }
    
    // Test count
    $total = $patientRepo->count();
    echo "   ✅ Total patients in system: {$total}\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Doctor Repository
echo "3️⃣ Testing DoctorRepository...\n";
try {
    $doctorRepo = new DoctorRepository();
    $doctors = $doctorRepo->getAllWithSpecialties();
    
    echo "   ✅ Found " . count($doctors) . " doctors\n";
    
    if (!empty($doctors)) {
        $doctor = $doctors[0];
        echo "   ✅ First doctor: {$doctor['docname']}\n";
        echo "   ✅ Specialty: {$doctor['specialty_name']}\n";
        
        // Test statistics
        $stats = $doctorRepo->getWithStatistics($doctor['docid']);
        echo "   ✅ Total appointments: {$stats['total_appointments']}\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 4: Appointment Repository
echo "4️⃣ Testing AppointmentRepository...\n";
try {
    $appointmentRepo = new AppointmentRepository();
    $upcoming = $appointmentRepo->getUpcoming(5);
    
    echo "   ✅ Found " . count($upcoming) . " upcoming appointments\n";
    
    // Test statistics
    $stats = $appointmentRepo->getStatistics();
    echo "   ✅ Total appointments: {$stats['total']}\n";
    echo "   ✅ Pending: {$stats['pending']}\n";
    echo "   ✅ Confirmed: {$stats['confirmed']}\n";
    echo "   ✅ Completed: {$stats['completed']}\n";
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Schedule Repository
echo "5️⃣ Testing ScheduleRepository...\n";
try {
    $scheduleRepo = new ScheduleRepository();
    $available = $scheduleRepo->getAvailable();
    
    echo "   ✅ Found " . count($available) . " available schedules\n";
    
    if (!empty($available)) {
        $schedule = $available[0];
        echo "   ✅ First schedule: {$schedule['title']}\n";
        echo "   ✅ Date: {$schedule['scheduledate']}\n";
        echo "   ✅ Available slots: {$schedule['available_slots']}\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 6: Specialty Repository
echo "6️⃣ Testing SpecialtyRepository...\n";
try {
    $specialtyRepo = new SpecialtyRepository();
    $specialties = $specialtyRepo->getAll();
    
    echo "   ✅ Found " . count($specialties) . " specialties\n";
    
    // Test with doctor count
    $withDoctors = $specialtyRepo->getWithDoctorCount();
    
    if (!empty($withDoctors)) {
        $specialty = $withDoctors[0];
        echo "   ✅ First specialty: {$specialty['name']}\n";
        echo "   ✅ Doctors: {$specialty['doctor_count']}\n";
    }
} catch (Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "================================\n";
echo "✅ All repository tests completed!\n";