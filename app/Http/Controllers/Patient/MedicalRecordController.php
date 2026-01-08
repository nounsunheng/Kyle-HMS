<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of medical records
     */
    public function index()
    {
        $patient = Auth::user()->patient;

        $medicalRecords = MedicalRecord::with(['doctor.user', 'doctor.specialty', 'appointment'])
            ->where('patient_id', $patient->id)
            ->orderBy('visit_date', 'desc')
            ->paginate(10);

        return view('patient.medical-records.index', compact('medicalRecords'));
    }

    /**
     * Display the specified medical record
     */
    public function show(MedicalRecord $medicalRecord)
    {
        // Ensure patient can only view their own records
        if ($medicalRecord->patient_id !== Auth::user()->patient->id) {
            abort(403, 'Unauthorized access.');
        }

        $medicalRecord->load(['doctor.user', 'doctor.specialty', 'appointment']);

        return view('patient.medical-records.show', compact('medicalRecord'));
    }
}
