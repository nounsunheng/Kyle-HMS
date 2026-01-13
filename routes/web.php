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
});

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'isPatient'])->prefix('patient')->name('patient.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('patient.dashboard');
    })->name('dashboard');

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

    // Appointments
    Route::get('/appointments', [App\Http\Controllers\Doctor\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [App\Http\Controllers\Doctor\AppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/status', [App\Http\Controllers\Doctor\AppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');

    // Patients
    Route::get('/patients', [App\Http\Controllers\Doctor\PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/{patient}', [App\Http\Controllers\Doctor\PatientController::class, 'show'])->name('patients.show');
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
});

require __DIR__.'/auth.php';
