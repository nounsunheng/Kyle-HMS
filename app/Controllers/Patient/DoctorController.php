<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers\Patient;

use App\Core\Controller;
use App\Services\ScheduleService;
use App\Repositories\DoctorRepository;
use App\Repositories\SpecialtyRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;

/**
 * Patient Doctor Controller
 * Browse and view doctors
 */
class DoctorController extends Controller {
    
    private DoctorRepository $doctorRepo;
    private SpecialtyRepository $specialtyRepo;
    private ScheduleService $scheduleService;
    
    public function __construct() {
        $this->doctorRepo = new DoctorRepository();
        $this->specialtyRepo = new SpecialtyRepository();
        $this->scheduleService = new ScheduleService();
    }
    
    /**
     * Show doctors list
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        $specialtyId = $_GET['specialty'] ?? null;
        $search = $_GET['search'] ?? null;
        
        // Get doctors
        if ($search) {
            $doctors = $this->doctorRepo->search($search);
        } elseif ($specialtyId) {
            $doctors = $this->doctorRepo->getBySpecialty($specialtyId);
        } else {
            $doctors = $this->doctorRepo->getAllWithSpecialties();
        }
        
        // Get specialties for filter
        $specialties = $this->specialtyRepo->getWithActiveDoctors();
        
        $data = [
            'doctors' => $doctors,
            'specialties' => $specialties,
            'selected_specialty' => $specialtyId,
            'search_term' => $search
        ];
        
        $this->view('patient.doctors', $data);
    }
    
    /**
     * Show doctor details
     */
    public function show(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['p']))->handle();
        
        $doctorId = $_GET['id'] ?? null;
        
        if (!$doctorId) {
            flash('Doctor not found', 'error');
            $this->redirect('/patient/doctors.php');
        }
        
        // Get doctor details with statistics
        $doctor = $this->doctorRepo->getWithStatistics($doctorId);
        
        if (!$doctor) {
            flash('Doctor not found', 'error');
            $this->redirect('/patient/doctors.php');
        }
        
        // Get available schedules for this doctor
        $schedules = $this->scheduleService->getAvailableSchedules($doctorId);
        
        $data = [
            'doctor' => $doctor,
            'schedules' => $schedules
        ];
        
        $this->view('patient.doctor-detail', $data);
    }
}