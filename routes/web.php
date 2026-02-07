<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', function () {
    return view('welcome');
});

// Default dashboard (will be redirected based on role)
Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    } elseif (auth()->user()->hasRole('doctor')) {
        return redirect()->route('doctor.dashboard');
    } elseif (auth()->user()->hasRole('patient')) {
        return redirect()->route('patient.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile routes (shared by all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Avatar routes
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Patient info
    Route::patch('/profile/patient-info', [ProfileController::class, 'updatePatientInfo'])->name('profile.patient-info.update');

    // DOCTOR INFO
    Route::patch('/profile/doctor-info', [ProfileController::class, 'updateDoctorInfo'])->name('profile.doctor-info.update');
});

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'isPatient'])->prefix('patient')->name('patient.')->group(function () {
    // Dashboard - FIXED: Now uses DashboardController instead of Closure
    Route::get('/dashboard', [App\Http\Controllers\Patient\DashboardController::class, 'index'])->name('dashboard');

    // Doctors
    Route::get('/doctors', [App\Http\Controllers\Patient\DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/{doctor}', [App\Http\Controllers\Patient\DoctorController::class, 'show'])->name('doctors.show');

    // Appointments
    Route::get('/appointments', [App\Http\Controllers\Patient\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [App\Http\Controllers\Patient\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [App\Http\Controllers\Patient\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [App\Http\Controllers\Patient\AppointmentController::class, 'show'])->name('appointments.show');
    Route::delete('/appointments/{appointment}', [App\Http\Controllers\Patient\AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Medical Records
    Route::get('/medical-records', [App\Http\Controllers\Patient\MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('/medical-records/{medicalRecord}', [App\Http\Controllers\Patient\MedicalRecordController::class, 'show'])->name('medical-records.show');
});

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'isDoctor'])->prefix('doctor')->name('doctor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('dashboard');

    // Schedule Management
    Route::get('/schedule', [App\Http\Controllers\Doctor\ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/schedule/create', [App\Http\Controllers\Doctor\ScheduleController::class, 'create'])->name('schedule.create');
    Route::post('/schedule', [App\Http\Controllers\Doctor\ScheduleController::class, 'store'])->name('schedule.store');
    Route::get('/schedule/{schedule}', [App\Http\Controllers\Doctor\ScheduleController::class, 'show'])->name('schedule.show');
    Route::get('/schedule/{schedule}/edit', [App\Http\Controllers\Doctor\ScheduleController::class, 'edit'])->name('schedule.edit');
    Route::put('/schedule/{schedule}', [App\Http\Controllers\Doctor\ScheduleController::class, 'update'])->name('schedule.update');
    Route::delete('/schedule/{schedule}', [App\Http\Controllers\Doctor\ScheduleController::class, 'destroy'])->name('schedule.destroy');
    Route::post('/schedule/{schedule}/cancel', [App\Http\Controllers\Doctor\ScheduleController::class, 'cancel'])->name('schedule.cancel');
    Route::resource('schedule', App\Http\Controllers\Doctor\ScheduleController::class);
    Route::post('schedule/{schedule}/cancel', [App\Http\Controllers\Doctor\ScheduleController::class, 'cancel'])->name('schedule.cancel');

    // Appointments
    Route::get('/appointments', [App\Http\Controllers\Doctor\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [App\Http\Controllers\Doctor\AppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/status', [App\Http\Controllers\Doctor\AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
    Route::post('/appointments/{appointment}/cancel', [App\Http\Controllers\Doctor\AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('appointments', [App\Http\Controllers\Doctor\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{appointment}', [App\Http\Controllers\Doctor\AppointmentController::class, 'show'])->name('appointments.show');

    // Appointment Actions
    Route::post('appointments/{appointment}/confirm', [App\Http\Controllers\Doctor\AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::post('appointments/{appointment}/complete', [App\Http\Controllers\Doctor\AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::post('appointments/{appointment}/no-show', [App\Http\Controllers\Doctor\AppointmentController::class, 'noShow'])->name('appointments.no-show');
    Route::post('appointments/{appointment}/cancel', [App\Http\Controllers\Doctor\AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Patients
    Route::get('/patients', [App\Http\Controllers\Doctor\PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{patient}', [App\Http\Controllers\Doctor\PatientController::class, 'show'])->name('patients.show');

    // Medical Records
    Route::get('/medical-records/create/{appointment}', [App\Http\Controllers\Doctor\MedicalRecordController::class, 'create'])->name('medical-records.create');
    Route::post('/medical-records/{appointment}', [App\Http\Controllers\Doctor\MedicalRecordController::class, 'store'])->name('medical-records.store');
    Route::get('/medical-records/{medicalRecord}', [App\Http\Controllers\Doctor\MedicalRecordController::class, 'show'])->name('medical-records.show');
    Route::get('/medical-records/{medicalRecord}/edit', [App\Http\Controllers\Doctor\MedicalRecordController::class, 'edit'])->name('medical-records.edit');
    Route::put('/medical-records/{medicalRecord}', [App\Http\Controllers\Doctor\MedicalRecordController::class, 'update'])->name('medical-records.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Doctors Management
    Route::resource('doctors', App\Http\Controllers\Admin\DoctorController::class);

    // Patients Management
    Route::get('/patients', [App\Http\Controllers\Admin\PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{patient}', [App\Http\Controllers\Admin\PatientController::class, 'show'])->name('patients.show');
    Route::get('/patients/{patient}/edit', [App\Http\Controllers\Admin\PatientController::class, 'edit'])->name('patients.edit');
    Route::put('/patients/{patient}', [App\Http\Controllers\Admin\PatientController::class, 'update'])->name('patients.update');
    Route::delete('/patients/{patient}', [App\Http\Controllers\Admin\PatientController::class, 'destroy'])->name('patients.destroy');

    // Specialties Management
    Route::resource('specialties', App\Http\Controllers\Admin\SpecialtyController::class);

    // Appointments Management
    Route::get('/appointments', [App\Http\Controllers\Admin\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [App\Http\Controllers\Admin\AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Reports & Exports
    Route::get('/reports', [App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [App\Http\Controllers\Admin\ReportController::class, 'exportPage'])->name('reports.export');

    // Export Actions
    Route::get('/reports/export-doctors', [App\Http\Controllers\Admin\ReportController::class, 'exportDoctors'])->name('reports.export-doctors');
    Route::get('/reports/export-patients', [App\Http\Controllers\Admin\ReportController::class, 'exportPatients'])->name('reports.export-patients');
    Route::get('/reports/export-appointments', [App\Http\Controllers\Admin\ReportController::class, 'exportAppointments'])->name('reports.export-appointments');
    Route::get('/reports/export-medical-records', [App\Http\Controllers\Admin\ReportController::class, 'exportMedicalRecords'])->name('reports.export-medical-records');
    Route::get('/reports/export-summary', [App\Http\Controllers\Admin\ReportController::class, 'exportSummary'])->name('reports.export-summary');

    // Doctor Avatar Management (Admin)
    Route::post('/doctors/{doctor}/avatar', [App\Http\Controllers\Admin\DoctorController::class, 'updateAvatar'])->name('doctors.avatar.update');
    Route::delete('/doctors/{doctor}/avatar', [App\Http\Controllers\Admin\DoctorController::class, 'deleteAvatar'])->name('doctors.avatar.delete');

    // Patient Avatar Management (Admin)
    Route::post('/patients/{patient}/avatar', [App\Http\Controllers\Admin\PatientController::class, 'updateAvatar'])->name('patients.avatar.update');
    Route::delete('/patients/{patient}/avatar', [App\Http\Controllers\Admin\PatientController::class, 'deleteAvatar'])->name('patients.avatar.delete');
});

require __DIR__ . '/auth.php';
