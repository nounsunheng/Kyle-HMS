<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ValidationService;
use App\Services\NotificationService;
use App\Repositories\DoctorRepository;
use App\Repositories\UserRepository;
use App\Repositories\SpecialtyRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Config\Security;

/**
 * Admin Doctor Controller
 * Manages doctor accounts (CRUD operations)
 */
class DoctorController extends Controller {
    
    private DoctorRepository $doctorRepo;
    private UserRepository $userRepo;
    private SpecialtyRepository $specialtyRepo;
    private ValidationService $validator;
    private NotificationService $notificationService;
    
    public function __construct() {
        $this->doctorRepo = new DoctorRepository();
        $this->userRepo = new UserRepository();
        $this->specialtyRepo = new SpecialtyRepository();
        $this->validator = new ValidationService();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * List all doctors
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        // Get all doctors with specialty info
        $doctors = $this->doctorRepo->getAllWithSpecialties();
        
        // Get specialties for filter dropdown
        $specialties = $this->specialtyRepo->getAll();
        
        $data = [
            'doctors' => $doctors,
            'specialties' => $specialties
        ];
        
        $this->view('admin.doctors', $data);
    }
    
    /**
     * Show create doctor form
     */
    public function create(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        // Get specialties for dropdown
        $specialties = $this->specialtyRepo->getAll();
        
        $data = [
            'specialties' => $specialties
        ];
        
        $this->view('admin.doctor-create', $data);
    }
    
    /**
     * Store new doctor
     */
    public function store(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/doctors');
            return;
        }
        
        // Validate input
        $rules = [
            'docemail' => 'required|email',
            'docname' => 'required|min:3',
            'doctel' => 'required',
            'specialties' => 'required',
            'docdegree' => 'required',
            'password' => 'required|min:8'
        ];
        
        $data = [
            'docemail' => $this->input('docemail'),
            'docname' => $this->input('docname'),
            'doctel' => $this->input('doctel'),
            'specialties' => $this->input('specialties'),
            'docdegree' => $this->input('docdegree'),
            'docexperience' => $this->input('docexperience', 0),
            'docbio' => $this->input('docbio'),
            'docconsultation_fee' => $this->input('docconsultation_fee', 0),
            'password' => $this->input('password')
        ];
        
        $errors = $this->validate($rules, $data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/doctors/create');
            return;
        }
        
        // Check if email already exists
        if ($this->userRepo->emailExists($data['docemail'])) {
            flash('Email already registered', 'error');
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/doctors/create');
            return;
        }
        
        try {
            // Create user account
            $userCreated = $this->userRepo->create([
                'email' => $data['docemail'],
                'password' => Security::hashPassword($data['password']),
                'usertype' => 'd',
                'status' => 'active'
            ]);
            
            if (!$userCreated) {
                throw new \Exception('Failed to create user account');
            }
            
            // Create doctor profile
            $doctorData = [
                'docemail' => $data['docemail'],
                'docname' => $data['docname'],
                'doctel' => $data['doctel'],
                'specialties' => $data['specialties'],
                'docdegree' => $data['docdegree'],
                'docexperience' => $data['docexperience'],
                'docbio' => $data['docbio'],
                'docconsultation_fee' => $data['docconsultation_fee'],
                'status' => 'active'
            ];
            
            $doctorId = $this->doctorRepo->create($doctorData);
            
            if (!$doctorId) {
                throw new \Exception('Failed to create doctor profile');
            }
            
            // Send notification to doctor
            $this->notificationService->create([
                'user_email' => $data['docemail'],
                'title' => 'Welcome to Kyle-HMS',
                'message' => 'Your doctor account has been created successfully.',
                'type' => 'system'
            ]);
            
            flash('Doctor added successfully', 'success');
            $this->redirect('/admin/doctors');
            
        } catch (\Exception $e) {
            error_log('Create doctor error: ' . $e->getMessage());
            flash('Failed to create doctor account', 'error');
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/doctors/create');
        }
    }
    
    /**
     * Show edit doctor form
     */
    public function edit(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $doctor = $this->doctorRepo->find($id);
        
        if (!$doctor) {
            flash('Doctor not found', 'error');
            $this->redirect('/admin/doctors');
            return;
        }
        
        // Get specialties for dropdown
        $specialties = $this->specialtyRepo->getAll();
        
        $data = [
            'doctor' => $doctor,
            'specialties' => $specialties
        ];
        
        $this->view('admin.doctor-edit', $data);
    }
    
    /**
     * Update doctor
     */
    public function update(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/doctors');
            return;
        }
        
        $doctor = $this->doctorRepo->find($id);
        
        if (!$doctor) {
            flash('Doctor not found', 'error');
            $this->redirect('/admin/doctors');
            return;
        }
        
        // Validate input
        $rules = [
            'docname' => 'required|min:3',
            'doctel' => 'required',
            'specialties' => 'required',
            'docdegree' => 'required'
        ];
        
        $data = [
            'docname' => $this->input('docname'),
            'doctel' => $this->input('doctel'),
            'specialties' => $this->input('specialties'),
            'docdegree' => $this->input('docdegree'),
            'docexperience' => $this->input('docexperience', 0),
            'docbio' => $this->input('docbio'),
            'docconsultation_fee' => $this->input('docconsultation_fee', 0),
            'status' => $this->input('status', 'active')
        ];
        
        $errors = $this->validate($rules, $data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/doctors/edit/' . $id);
            return;
        }
        
        try {
            $updated = $this->doctorRepo->update($id, $data);
            
            if (!$updated) {
                throw new \Exception('Failed to update doctor');
            }
            
            // Update user status if changed
            if ($data['status'] !== $doctor['status']) {
                $this->userRepo->updateStatus($doctor['docemail'], $data['status']);
            }
            
            flash('Doctor updated successfully', 'success');
            $this->redirect('/admin/doctors');
            
        } catch (\Exception $e) {
            error_log('Update doctor error: ' . $e->getMessage());
            flash('Failed to update doctor', 'error');
            $this->redirect('/admin/doctors/edit/' . $id);
        }
    }
    
    /**
     * Delete doctor
     */
    public function delete(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/doctors');
            return;
        }
        
        $doctor = $this->doctorRepo->find($id);
        
        if (!$doctor) {
            flash('Doctor not found', 'error');
            $this->redirect('/admin/doctors');
            return;
        }
        
        try {
            // Delete doctor (cascade will delete user account)
            $deleted = $this->doctorRepo->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Failed to delete doctor');
            }
            
            flash('Doctor deleted successfully', 'success');
            
        } catch (\Exception $e) {
            error_log('Delete doctor error: ' . $e->getMessage());
            flash('Failed to delete doctor', 'error');
        }
        
        $this->redirect('/admin/doctors');
    }
    
    /**
     * Toggle doctor status (active/inactive)
     */
    public function toggleStatus(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        $doctor = $this->doctorRepo->find($id);
        
        if (!$doctor) {
            $this->json(['error' => 'Doctor not found'], 404);
            return;
        }
        
        try {
            $newStatus = $doctor['status'] === 'active' ? 'inactive' : 'active';
            
            $updated = $this->doctorRepo->update($id, [
                'status' => $newStatus
            ]);
            
            if (!$updated) {
                throw new \Exception('Failed to update status');
            }
            
            // Update user account status
            $this->userRepo->updateStatus($doctor['docemail'], $newStatus);
            
            $this->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $newStatus
            ]);
            
        } catch (\Exception $e) {
            error_log('Toggle status error: ' . $e->getMessage());
            $this->json(['error' => 'Failed to update status'], 500);
        }
    }
    
    /**
     * View doctor details
     */
    public function show(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $doctor = $this->doctorRepo->getWithDetails($id);
        
        if (!$doctor) {
            flash('Doctor not found', 'error');
            $this->redirect('/admin/doctors');
            return;
        }
        
        // Get doctor statistics
        $stats = $this->doctorRepo->getStatistics($id);
        
        $data = [
            'doctor' => $doctor,
            'stats' => $stats
        ];
        
        $this->view('admin.doctor-details', $data);
    }
}