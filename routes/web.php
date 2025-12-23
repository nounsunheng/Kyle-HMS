<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\Patient\DashboardController as PatientDashboard;
use App\Controllers\Patient\DoctorController as PatientDoctor;
use App\Controllers\Patient\AppointmentController as PatientAppointment;
use App\Controllers\Patient\ProfileController as PatientProfile;
use App\Controllers\Doctor\DashboardController as DoctorDashboard;
use App\Controllers\Doctor\AppointmentController as DoctorAppointment;
use App\Controllers\Doctor\ScheduleController as DoctorSchedule;
use App\Controllers\Admin\DashboardController as AdminDashboard;
use App\Controllers\Admin\DoctorController as AdminDoctor;
use App\Controllers\Admin\PatientController as AdminPatient;
use App\Controllers\Admin\AppointmentController as AdminAppointment;
use App\Controllers\Admin\SpecialtyController as AdminSpecialty;

$router = new Router();

// Public
$router->get('/', function() {
    $pageTitle = 'Home - Kyle HMS';
    $contentView = 'home.index';
    require __DIR__ . '/../views/layouts/main.php';
});

// Auth
$router->get('/auth/login', [AuthController::class, 'showLogin']);
$router->post('/auth/login', [AuthController::class, 'login']);
$router->get('/auth/register', [AuthController::class, 'showRegister']);
$router->post('/auth/register', [AuthController::class, 'register']);
$router->get('/auth/logout', [AuthController::class, 'logout']);

// Patient
$router->get('/patient/dashboard', [PatientDashboard::class, 'index']);
$router->get('/patient/doctors', [PatientDoctor::class, 'index']);
$router->get('/patient/appointments', [PatientAppointment::class, 'index']);
$router->get('/patient/book-appointment', [PatientAppointment::class, 'create']);
$router->post('/patient/book-appointment', [PatientAppointment::class, 'store']);
$router->get('/patient/profile', [PatientProfile::class, 'index']);
$router->post('/patient/profile', [PatientProfile::class, 'update']);

// Doctor
$router->get('/doctor/dashboard', [DoctorDashboard::class, 'index']);
$router->get('/doctor/appointments', [DoctorAppointment::class, 'index']);
$router->get('/doctor/schedule', [DoctorSchedule::class, 'index']);
$router->post('/doctor/schedule', [DoctorSchedule::class, 'store']);

// Admin
$router->get('/admin/dashboard', [AdminDashboard::class, 'index']);
$router->get('/admin/doctors', [AdminDoctor::class, 'index']);
$router->get('/admin/doctors/create', [AdminDoctor::class, 'create']);
$router->post('/admin/doctors', [AdminDoctor::class, 'store']);
$router->get('/admin/doctors/{id}/edit', [AdminDoctor::class, 'edit']);
$router->post('/admin/doctors/{id}', [AdminDoctor::class, 'update']);
$router->post('/admin/doctors/{id}/delete', [AdminDoctor::class, 'delete']);

$router->get('/admin/patients', [AdminPatient::class, 'index']);
$router->get('/admin/patients/create', [AdminPatient::class, 'create']);
$router->post('/admin/patients', [AdminPatient::class, 'store']);
$router->get('/admin/patients/{id}/edit', [AdminPatient::class, 'edit']);
$router->post('/admin/patients/{id}', [AdminPatient::class, 'update']);

$router->get('/admin/appointments', [AdminAppointment::class, 'index']);
$router->get('/admin/specialties', [AdminSpecialty::class, 'index']);
$router->get('/admin/specialties/create', [AdminSpecialty::class, 'create']);
$router->post('/admin/specialties', [AdminSpecialty::class, 'store']);

// Dispatch
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($uri, $method);