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
use App\Repositories\PatientRepository;
use App\Repositories\UserRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Config\Security;

/**
 * Admin Patient Controller
 * Manages patient accounts (CRUD operations)
 */
class PatientController extends Controller {
    
    private PatientRepository $patientRepo;
    private UserRepository $userRepo;
    private ValidationService $validator;
    private NotificationService $notificationService;
    
    public function __construct() {
        $this->patientRepo = new PatientRepository();
        $this->userRepo = new UserRepository();
        $this->validator = new ValidationService();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * List all patients
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        // Get search query if exists
        $search = $this->input('search', '');
        
        if (!empty($search)) {
            $patients = $this->patientRepo->search($search);
        } else {
            $patients = $this->patientRepo->getAllWithStatistics();
        }
        
        $data = [
            'patients' => $patients,
            'search' => $search
        ];
        
        $this->view('admin.patients', $data);
    }
    
    /**
     * Show create patient form
     */
    public function create(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $this->view('admin.patient-create');
    }
    
    /**
     * Store new patient
     */
    public function store(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/patients');
            return;
        }
        
        // Validate input
        $rules = [
            'pemail' => 'required|email',
            'pname' => 'required|min:3',
            'pdob' => 'required',
            'pgender' => 'required',
            'ptel' => 'required',
            'paddress' => 'required',
            'password' => 'required|min:8'
        ];
        
        $data = [
            'pemail' => $this->input('pemail'),
            'pname' => $this->input('pname'),
            'pdob' => $this->input('pdob'),
            'pgender' => $this->input('pgender'),
            'ptel' => $this->input('ptel'),
            'paddress' => $this->input('paddress'),
            'pbloodgroup' => $this->input('pbloodgroup'),
            'pemergency_contact' => $this->input('pemergency_contact'),
            'pemergency_name' => $this->input('pemergency_name'),
            'password' => $this->input('password')
        ];
        
        $errors = $this->validate($rules, $data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/patients/create');
            return;
        }
        
        // Check if email already exists
        if ($this->userRepo->emailExists($data['pemail'])) {
            flash('Email already registered', 'error');
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/patients/create');
            return;
        }
        
        try {
            // Create user account
            $userCreated = $this->userRepo->create([
                'email' => $data['pemail'],
                'password' => Security::hashPassword($data['password']),
                'usertype' => 'p',
                'status' => 'active'
            ]);
            
            if (!$userCreated) {
                throw new \Exception('Failed to create user account');
            }
            
            // Create patient profile
            $patientData = [
                'pemail' => $data['pemail'],
                'pname' => $data['pname'],
                'pdob' => $data['pdob'],
                'pgender' => $data['pgender'],
                'ptel' => $data['ptel'],
                'paddress' => $data['paddress'],
                'pbloodgroup' => $data['pbloodgroup'],
                'pemergency_contact' => $data['pemergency_contact'],
                'pemergency_name' => $data['pemergency_name']
            ];
            
            $patientId = $this->patientRepo->create($patientData);
            
            if (!$patientId) {
                throw new \Exception('Failed to create patient profile');
            }
            
            // Send notification to patient
            $this->notificationService->create([
                'user_email' => $data['pemail'],
                'title' => 'Welcome to Kyle-HMS',
                'message' => 'Your patient account has been created successfully.',
                'type' => 'system'
            ]);
            
            flash('Patient added successfully', 'success');
            $this->redirect('/admin/patients');
            
        } catch (\Exception $e) {
            error_log('Create patient error: ' . $e->getMessage());
            flash('Failed to create patient account', 'error');
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/patients/create');
        }
    }
    
    /**
     * Show edit patient form
     */
    public function edit(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $patient = $this->patientRepo->find($id);
        
        if (!$patient) {
            flash('Patient not found', 'error');
            $this->redirect('/admin/patients');
            return;
        }
        
        $data = [
            'patient' => $patient
        ];
        
        $this->view('admin.patient-edit', $data);
    }
    
    /**
     * Update patient
     */
    public function update(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/patients');
            return;
        }
        
        $patient = $this->patientRepo->find($id);
        
        if (!$patient) {
            flash('Patient not found', 'error');
            $this->redirect('/admin/patients');
            return;
        }
        
        // Validate input
        $rules = [
            'pname' => 'required|min:3',
            'pdob' => 'required',
            'pgender' => 'required',
            'ptel' => 'required',
            'paddress' => 'required'
        ];
        
        $data = [
            'pname' => $this->input('pname'),
            'pdob' => $this->input('pdob'),
            'pgender' => $this->input('pgender'),
            'ptel' => $this->input('ptel'),
            'paddress' => $this->input('paddress'),
            'pbloodgroup' => $this->input('pbloodgroup'),
            'pemergency_contact' => $this->input('pemergency_contact'),
            'pemergency_name' => $this->input('pemergency_name')
        ];
        
        $errors = $this->validate($rules, $data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/patients/edit/' . $id);
            return;
        }
        
        try {
            $updated = $this->patientRepo->update($id, $data);
            
            if (!$updated) {
                throw new \Exception('Failed to update patient');
            }
            
            flash('Patient updated successfully', 'success');
            $this->redirect('/admin/patients');
            
        } catch (\Exception $e) {
            error_log('Update patient error: ' . $e->getMessage());
            flash('Failed to update patient', 'error');
            $this->redirect('/admin/patients/edit/' . $id);
        }
    }
    
    /**
     * Delete patient
     */
    public function delete(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/patients');
            return;
        }
        
        $patient = $this->patientRepo->find($id);
        
        if (!$patient) {
            flash('Patient not found', 'error');
            $this->redirect('/admin/patients');
            return;
        }
        
        try {
            // Delete patient (cascade will delete user account)
            $deleted = $this->patientRepo->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Failed to delete patient');
            }
            
            flash('Patient deleted successfully', 'success');
            
        } catch (\Exception $e) {
            error_log('Delete patient error: ' . $e->getMessage());
            flash('Failed to delete patient', 'error');
        }
        
        $this->redirect('/admin/patients');
    }
    
    /**
     * View patient details
     */
    public function show(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $patient = $this->patientRepo->getWithStatistics($id);
        
        if (!$patient) {
            flash('Patient not found', 'error');
            $this->redirect('/admin/patients');
            return;
        }
        
        // Get patient's appointment history
        $appointments = $this->patientRepo->getAppointmentHistory($id);
        
        $data = [
            'patient' => $patient,
            'appointments' => $appointments
        ];
        
        $this->view('admin.patient-details', $data);
    }
}