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
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // Doctors Management
    Route::get('/doctors', function () {
        return view('admin.doctors.index');
    })->name('doctors.index');

    // Patients Management
    Route::get('/patients', function () {
        return view('admin.patients.index');
    })->name('patients.index');

    // Specialties Management
    Route::get('/specialties', function () {
        return view('admin.specialties.index');
    })->name('specialties.index');

    // Appointments Management
    Route::get('/appointments', function () {
        return view('admin.appointments.index');
    })->name('appointments.index');

    // Reports
    Route::get('/reports', function () {
        return view('admin.reports.index');
    })->name('reports.index');
});

require __DIR__.'/auth.php';
