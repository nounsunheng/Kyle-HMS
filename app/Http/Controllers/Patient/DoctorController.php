<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of doctors
     */
    public function index(Request $request)
    {
        $query = Doctor::with(['user', 'specialty'])
                      ->available();

        // Search by doctor name
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by specialty
        if ($request->filled('specialty')) {
            $query->where('specialty_id', $request->specialty);
        }

        $doctors = $query->paginate(12);
        $specialties = Specialty::orderBy('name')->get();

        return view('patient.doctors.index', compact('doctors', 'specialties'));
    }

    /**
     * Display the specified doctor
     */
    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'specialty', 'schedules' => function ($query) {
            $query->upcoming()->available()->orderBy('schedule_date');
        }]);

        return view('patient.doctors.show', compact('doctor'));
    }
}
