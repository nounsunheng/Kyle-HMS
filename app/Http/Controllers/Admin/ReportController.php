<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Specialty;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports page
     */
    public function index()
    {
        // Statistics for the reports page
        $totalUsers = \App\Models\User::count();
        $activeDoctors = Doctor::where('is_available', true)->count();
        $thisMonthAppointments = Appointment::whereMonth('created_at', now()->month)->count();
        $completedAppointments = Appointment::where('status', 'completed')->count();

        // Appointment status breakdown
        $appointmentsByStatus = [
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
            'no_show' => Appointment::where('status', 'no_show')->count(),
        ];

        // Top specialties
        $topSpecialties = Specialty::withCount('doctors')
            ->orderBy('doctors_count', 'desc')
            ->limit(5)
            ->get();

        return view('admin.reports.index', compact(
            'totalUsers',
            'activeDoctors',
            'thisMonthAppointments',
            'completedAppointments',
            'appointmentsByStatus',
            'topSpecialties'
        ));
    }

    /**
     * Display export center page
     */
    public function exportPage()
    {
        return view('admin.reports.export');
    }

    /**
     * Export doctors report
     */
    public function exportDoctors(Request $request)
    {
        $format = $request->get('format', 'csv');

        $doctors = Doctor::with(['user', 'specialty'])
            ->when($request->specialty_id, function ($query) use ($request) {
                return $query->where('specialty_id', $request->specialty_id);
            })
            ->when($request->availability, function ($query) use ($request) {
                return $query->where('is_available', $request->availability === 'available');
            })
            ->get();

        if ($format === 'csv') {
            return $this->exportDoctorsCSV($doctors);
        } elseif ($format === 'excel') {
            return $this->exportDoctorsExcel($doctors);
        }

        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export doctors to CSV
     */
    private function exportDoctorsCSV($doctors)
    {
        $filename = 'doctors_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($doctors) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'Specialty',
                'License Number',
                'Years of Experience',
                'Availability',
                'Qualifications',
                'Registered Date'
            ]);

            // Data rows
            foreach ($doctors as $doctor) {
                fputcsv($file, [
                    $doctor->id,
                    $doctor->user->name,
                    $doctor->user->email,
                    $doctor->phone,
                    $doctor->specialty->name,
                    $doctor->license_number,
                    $doctor->years_of_experience,
                    $doctor->is_available ? 'Available' : 'Unavailable',
                    $doctor->qualifications ?? 'N/A',
                    $doctor->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export doctors to Excel (using CSV with .xlsx extension)
     */
    private function exportDoctorsExcel($doctors)
    {
        $filename = 'doctors_report_' . now()->format('Y-m-d_His') . '.xlsx';

        // For simplicity, we'll export as CSV with .xlsx extension
        // For true Excel support, install: composer require maatwebsite/excel
        return $this->exportDoctorsCSV($doctors)->setHeader(
            'Content-Disposition',
            'attachment; filename="' . $filename . '"'
        );
    }

    /**
     * Export patients report
     */
    public function exportPatients(Request $request)
    {
        $format = $request->get('format', 'csv');

        $patients = Patient::with('user')->get();

        if ($format === 'csv') {
            return $this->exportPatientsCSV($patients);
        }

        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export patients to CSV
     */
    private function exportPatientsCSV($patients)
    {
        $filename = 'patients_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($patients) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'ID',
                'Name',
                'Email',
                'Phone',
                'Date of Birth',
                'Age',
                'Gender',
                'Blood Type',
                'Address',
                'Emergency Contact',
                'Allergies',
                'Registered Date'
            ]);

            foreach ($patients as $patient) {
                fputcsv($file, [
                    $patient->id,
                    $patient->user->name,
                    $patient->user->email,
                    $patient->phone,
                    $patient->date_of_birth->format('Y-m-d'),
                    $patient->age,
                    ucfirst($patient->gender),
                    $patient->blood_type ?? 'N/A',
                    $patient->address,
                    $patient->emergency_contact,
                    $patient->allergies ?? 'None',
                    $patient->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export appointments report
     */
    public function exportAppointments(Request $request)
    {
        $format = $request->get('format', 'csv');

        $appointments = Appointment::with(['patient.user', 'schedule.doctor.user', 'schedule.doctor.specialty'])
            ->when($request->status, function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->date_from, function ($query) use ($request) {
                return $query->whereHas('schedule', function ($q) use ($request) {
                    $q->where('schedule_date', '>=', $request->date_from);
                });
            })
            ->when($request->date_to, function ($query) use ($request) {
                return $query->whereHas('schedule', function ($q) use ($request) {
                    $q->where('schedule_date', '<=', $request->date_to);
                });
            })
            ->get();

        if ($format === 'csv') {
            return $this->exportAppointmentsCSV($appointments);
        }

        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export appointments to CSV
     */
    private function exportAppointmentsCSV($appointments)
    {
        $filename = 'appointments_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($appointments) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Appointment Number',
                'Patient Name',
                'Patient Email',
                'Doctor Name',
                'Specialty',
                'Date',
                'Time',
                'Status',
                'Reason',
                'Booked On'
            ]);

            foreach ($appointments as $appointment) {
                fputcsv($file, [
                    $appointment->appointment_number,
                    $appointment->patient->user->name,
                    $appointment->patient->user->email,
                    'Dr. ' . $appointment->schedule->doctor->user->name,
                    $appointment->schedule->doctor->specialty->name,
                    $appointment->schedule->schedule_date->format('Y-m-d'),
                    $appointment->appointment_time,
                    ucfirst($appointment->status),
                    $appointment->reason ?? 'N/A',
                    $appointment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export medical records report
     */
    public function exportMedicalRecords(Request $request)
    {
        $format = $request->get('format', 'csv');

        $records = MedicalRecord::with(['patient.user', 'doctor.user', 'doctor.specialty'])
            ->when($request->patient_id, function ($query) use ($request) {
                return $query->where('patient_id', $request->patient_id);
            })
            ->when($request->doctor_id, function ($query) use ($request) {
                return $query->where('doctor_id', $request->doctor_id);
            })
            ->when($request->date_from, function ($query) use ($request) {
                return $query->where('visit_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function ($query) use ($request) {
                return $query->where('visit_date', '<=', $request->date_to);
            })
            ->get();

        if ($format === 'csv') {
            return $this->exportMedicalRecordsCSV($records);
        }

        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export medical records to CSV
     */
    private function exportMedicalRecordsCSV($records)
    {
        $filename = 'medical_records_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Record ID',
                'Patient Name',
                'Doctor Name',
                'Visit Date',
                'Diagnosis',
                'Treatment',
                'Prescription',
                'Notes'
            ]);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->id,
                    $record->patient->user->name,
                    'Dr. ' . $record->doctor->user->name,
                    $record->visit_date->format('Y-m-d'),
                    $record->diagnosis,
                    $record->treatment ?? 'N/A',
                    $record->prescription ?? 'N/A',
                    $record->notes ?? 'N/A'
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export summary report
     */
    public function exportSummary(Request $request)
    {
        $format = $request->get('format', 'csv');

        if ($format === 'csv') {
            return $this->exportSummaryCSV();
        }

        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export summary to CSV
     */
    private function exportSummaryCSV()
    {
        $filename = 'system_summary_report_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // System Overview
            fputcsv($file, ['Kyle-HMS System Summary Report']);
            fputcsv($file, ['Generated on: ' . now()->format('F d, Y \a\t g:i A')]);
            fputcsv($file, []);

            // Statistics
            fputcsv($file, ['Category', 'Count']);
            fputcsv($file, ['Total Users', \App\Models\User::count()]);
            fputcsv($file, ['Total Doctors', Doctor::count()]);
            fputcsv($file, ['Available Doctors', Doctor::where('is_available', true)->count()]);
            fputcsv($file, ['Total Patients', Patient::count()]);
            fputcsv($file, ['Total Specialties', Specialty::count()]);
            fputcsv($file, ['Total Appointments', Appointment::count()]);
            fputcsv($file, []);

            // Appointments by Status
            fputcsv($file, ['Appointment Status', 'Count']);
            fputcsv($file, ['Pending', Appointment::where('status', 'pending')->count()]);
            fputcsv($file, ['Confirmed', Appointment::where('status', 'confirmed')->count()]);
            fputcsv($file, ['Completed', Appointment::where('status', 'completed')->count()]);
            fputcsv($file, ['Cancelled', Appointment::where('status', 'cancelled')->count()]);
            fputcsv($file, ['No Show', Appointment::where('status', 'no_show')->count()]);
            fputcsv($file, []);

            // Top Specialties
            fputcsv($file, ['Top Specialties by Doctor Count']);
            fputcsv($file, ['Specialty', 'Doctor Count']);
            $topSpecialties = Specialty::withCount('doctors')->orderBy('doctors_count', 'desc')->limit(10)->get();
            foreach ($topSpecialties as $specialty) {
                fputcsv($file, [$specialty->name, $specialty->doctors_count]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
