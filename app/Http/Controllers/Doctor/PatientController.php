<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    /**
     * Display a listing of patients
     */
    public function index(Request $request)
    {
        $doctor = Auth::user()->doctor;

        // Get unique patients who have appointments with this doctor
        $query = Patient::with('user')
            ->whereHas('appointments.schedule', function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            });

        // Search by patient name
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $patients = $query->paginate(15);

        return view('doctor.patients.index', compact('patients'));
    }

    /**
     * Display the specified patient
     */
    public function show(Patient $patient)
    {
        $doctor = Auth::user()->doctor;

        // Verify doctor has treated this patient
        $hasAppointment = Appointment::where('patient_id', $patient->id)
            ->whereHas('schedule', function ($q) use ($doctor) {
                $q->where('doctor_id', $doctor->id);
            })
            ->exists();

        if (!$hasAppointment) {
            abort(403, 'You do not have access to this patient\'s records.');
        }

        $patient->load(['user', 'appointments.schedule', 'medicalRecords' => function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)->orderBy('visit_date', 'desc');
        }]);

        return view('doctor.patients.show', compact('patient'));
    }
}
