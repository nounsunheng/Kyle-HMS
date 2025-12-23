<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Repositories\SpecialtyRepository;
use App\Repositories\DoctorRepository;
use App\Middleware\AuthMiddleware;
use App\Middleware\RoleMiddleware;
use App\Middleware\CsrfMiddleware;

/**
 * Admin Specialty Controller
 * Manages medical specialties (CRUD operations)
 */
class SpecialtyController extends Controller {
    
    private SpecialtyRepository $specialtyRepo;
    private DoctorRepository $doctorRepo;
    
    public function __construct() {
        $this->specialtyRepo = new SpecialtyRepository();
        $this->doctorRepo = new DoctorRepository();
    }
    
    /**
     * List all specialties
     */
    public function index(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        // Get all specialties with doctor count
        $specialties = $this->specialtyRepo->getAllWithDoctorCount();
        
        $data = [
            'specialties' => $specialties
        ];
        
        $this->view('admin.specialties', $data);
    }
    
    /**
     * Show create specialty form
     */
    public function create(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $this->view('admin.specialty-create');
    }
    
    /**
     * Store new specialty
     */
    public function store(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/specialties');
            return;
        }
        
        // Validate input
        $rules = [
            'name' => 'required|min:3',
            'icon' => 'required'
        ];
        
        $data = [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'icon' => $this->input('icon', 'fas fa-stethoscope')
        ];
        
        $errors = $this->validate($rules, $data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/specialties/create');
            return;
        }
        
        // Check if specialty name already exists
        if ($this->specialtyRepo->existsByName($data['name'])) {
            flash('Specialty name already exists', 'error');
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/specialties/create');
            return;
        }
        
        try {
            $specialtyId = $this->specialtyRepo->create($data);
            
            if (!$specialtyId) {
                throw new \Exception('Failed to create specialty');
            }
            
            flash('Specialty added successfully', 'success');
            $this->redirect('/admin/specialties');
            
        } catch (\Exception $e) {
            error_log('Create specialty error: ' . $e->getMessage());
            flash('Failed to create specialty', 'error');
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/specialties/create');
        }
    }
    
    /**
     * Show edit specialty form
     */
    public function edit(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $specialty = $this->specialtyRepo->find($id);
        
        if (!$specialty) {
            flash('Specialty not found', 'error');
            $this->redirect('/admin/specialties');
            return;
        }
        
        $data = [
            'specialty' => $specialty
        ];
        
        $this->view('admin.specialty-edit', $data);
    }
    
    /**
     * Update specialty
     */
    public function update(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/specialties');
            return;
        }
        
        $specialty = $this->specialtyRepo->find($id);
        
        if (!$specialty) {
            flash('Specialty not found', 'error');
            $this->redirect('/admin/specialties');
            return;
        }
        
        // Validate input
        $rules = [
            'name' => 'required|min:3',
            'icon' => 'required'
        ];
        
        $data = [
            'name' => $this->input('name'),
            'description' => $this->input('description'),
            'icon' => $this->input('icon', 'fas fa-stethoscope')
        ];
        
        $errors = $this->validate($rules, $data);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/specialties/edit/' . $id);
            return;
        }
        
        // Check if specialty name already exists (excluding current)
        if ($this->specialtyRepo->existsByName($data['name'], $id)) {
            flash('Specialty name already exists', 'error');
            $_SESSION['old_input'] = $data;
            $this->redirect('/admin/specialties/edit/' . $id);
            return;
        }
        
        try {
            $updated = $this->specialtyRepo->update($id, $data);
            
            if (!$updated) {
                throw new \Exception('Failed to update specialty');
            }
            
            flash('Specialty updated successfully', 'success');
            $this->redirect('/admin/specialties');
            
        } catch (\Exception $e) {
            error_log('Update specialty error: ' . $e->getMessage());
            flash('Failed to update specialty', 'error');
            $this->redirect('/admin/specialties/edit/' . $id);
        }
    }
    
    /**
     * Delete specialty
     */
    public function delete(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        (new CsrfMiddleware())->handle();
        
        if (!isPost()) {
            $this->redirect('/admin/specialties');
            return;
        }
        
        $specialty = $this->specialtyRepo->find($id);
        
        if (!$specialty) {
            flash('Specialty not found', 'error');
            $this->redirect('/admin/specialties');
            return;
        }
        
        // Check if specialty has doctors assigned
        $doctorCount = $this->doctorRepo->getCountBySpecialty($id);
        
        if ($doctorCount > 0) {
            flash('Cannot delete specialty. ' . $doctorCount . ' doctor(s) are assigned to this specialty.', 'error');
            $this->redirect('/admin/specialties');
            return;
        }
        
        try {
            $deleted = $this->specialtyRepo->delete($id);
            
            if (!$deleted) {
                throw new \Exception('Failed to delete specialty');
            }
            
            flash('Specialty deleted successfully', 'success');
            
        } catch (\Exception $e) {
            error_log('Delete specialty error: ' . $e->getMessage());
            flash('Failed to delete specialty', 'error');
        }
        
        $this->redirect('/admin/specialties');
    }
    
    /**
     * View specialty details
     */
    public function show(int $id): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        $specialty = $this->specialtyRepo->find($id);
        
        if (!$specialty) {
            flash('Specialty not found', 'error');
            $this->redirect('/admin/specialties');
            return;
        }
        
        // Get doctors in this specialty
        $doctors = $this->doctorRepo->getBySpecialty($id);
        
        // Get specialty statistics
        $stats = $this->specialtyRepo->getStatistics($id);
        
        $data = [
            'specialty' => $specialty,
            'doctors' => $doctors,
            'stats' => $stats
        ];
        
        $this->view('admin.specialty-details', $data);
    }
    
    /**
     * Get all specialties (AJAX)
     */
    public function getAll(): void {
        // Protect route
        (new AuthMiddleware())->handle();
        (new RoleMiddleware(['a']))->handle();
        
        if (!isAjax()) {
            $this->json(['error' => 'Invalid request'], 400);
            return;
        }
        
        try {
            $specialties = $this->specialtyRepo->getAll();
            $this->json(['success' => true, 'data' => $specialties]);
            
        } catch (\Exception $e) {
            error_log('Get specialties error: ' . $e->getMessage());
            $this->json(['error' => 'Failed to fetch specialties'], 500);
        }
    }
}