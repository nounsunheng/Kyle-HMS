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
    Route::get('/doctors', function () {
        return view('patient.doctors.index');
    })->name('doctors.index');

    // Appointments
    Route::get('/appointments', function () {
        return view('patient.appointments.index');
    })->name('appointments.index');

    // Medical Records
    Route::get('/medical-records', function () {
        return view('patient.medical-records.index');
    })->name('medical-records.index');
});

/*
|--------------------------------------------------------------------------
| Doctor Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'isDoctor'])->prefix('doctor')->name('doctor.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('doctor.dashboard');
    })->name('dashboard');

    // Schedule
    Route::get('/schedule', function () {
        return view('doctor.schedule.index');
    })->name('schedule.index');

    // Appointments
    Route::get('/appointments', function () {
        return view('doctor.appointments.index');
    })->name('appointments.index');

    // Patients
    Route::get('/patients', function () {
        return view('doctor.patients.index');
    })->name('patients.index');
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
