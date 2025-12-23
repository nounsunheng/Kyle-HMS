<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Services;

use App\Repositories\ScheduleRepository;
use App\Repositories\DoctorRepository;

/**
 * Schedule Service
 * Handles schedule creation, validation, and management
 */
class ScheduleService {
    
    private ScheduleRepository $scheduleRepo;
    private DoctorRepository $doctorRepo;
    
    public function __construct() {
        $this->scheduleRepo = new ScheduleRepository();
        $this->doctorRepo = new DoctorRepository();
    }
    
    /**
     * Create new schedule for doctor
     * 
     * @param array $data Schedule data
     * @return array Result
     */
    public function createSchedule(array $data): array {
        // Validate required fields
        if (empty($data['docid']) || empty($data['scheduledate']) || empty($data['scheduletime'])) {
            return [
                'success' => false,
                'message' => 'Missing required information'
            ];
        }
        
        // Validate date is not in the past
        if (strtotime($data['scheduledate']) < strtotime('today')) {
            return [
                'success' => false,
                'message' => 'Cannot create schedule for past dates'
            ];
        }
        
        // Check for scheduling conflicts
        $hasConflict = $this->scheduleRepo->hasConflict(
            $data['docid'],
            $data['scheduledate'],
            $data['scheduletime']
        );
        
        if ($hasConflict) {
            return [
                'success' => false,
                'message' => 'Schedule conflict detected. Doctor already has a schedule at this time.'
            ];
        }
        
        // Validate capacity
        if (empty($data['nop']) || $data['nop'] < 1) {
            return [
                'success' => false,
                'message' => 'Invalid patient capacity'
            ];
        }
        
        // Create schedule
        try {
            $scheduleId = $this->scheduleRepo->create([
                'docid' => $data['docid'],
                'title' => $data['title'] ?? 'General Consultation',
                'scheduledate' => $data['scheduledate'],
                'scheduletime' => $data['scheduletime'],
                'duration' => $data['duration'] ?? 30,
                'nop' => $data['nop'],
                'status' => 'active'
            ]);
            
            if (!$scheduleId) {
                throw new \Exception('Failed to create schedule');
            }
            
            // Log activity
            logActivity('create_schedule', "Created schedule: {$data['title']} on {$data['scheduledate']}");
            
            return [
                'success' => true,
                'message' => 'Schedule created successfully',
                'schedule_id' => $scheduleId
            ];
            
        } catch (\Exception $e) {
            error_log("Schedule Creation Error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to create schedule. Please try again.'
            ];
        }
    }
    
    /**
     * Update schedule
     * 
     * @param int $scheduleId Schedule ID
     * @param array $data Updated data
     * @return array Result
     */
    public function updateSchedule(int $scheduleId, array $data): array {
        // Get existing schedule
        $schedule = $this->scheduleRepo->findById($scheduleId);
        
        if (!$schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found'
            ];
        }
        
        // Check if schedule has appointments
        if ($schedule['booked'] > 0) {
            return [
                'success' => false,
                'message' => 'Cannot update schedule with existing appointments'
            ];
        }
        
        // Check for conflicts (excluding current schedule)
        $hasConflict = $this->scheduleRepo->hasConflict(
            $schedule['docid'],
            $data['scheduledate'],
            $data['scheduletime'],
            $scheduleId
        );
        
        if ($hasConflict) {
            return [
                'success' => false,
                'message' => 'Schedule conflict detected'
            ];
        }
        
        // Update schedule
        $updated = $this->scheduleRepo->update($scheduleId, $data);
        
        if ($updated) {
            logActivity('update_schedule', "Updated schedule ID: $scheduleId");
            
            return [
                'success' => true,
                'message' => 'Schedule updated successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to update schedule'
        ];
    }
    
    /**
     * Delete schedule
     * 
     * @param int $scheduleId Schedule ID
     * @return array Result
     */
    public function deleteSchedule(int $scheduleId): array {
        $schedule = $this->scheduleRepo->findById($scheduleId);
        
        if (!$schedule) {
            return [
                'success' => false,
                'message' => 'Schedule not found'
            ];
        }
        
        // Check if schedule has appointments
        if ($schedule['booked'] > 0) {
            return [
                'success' => false,
                'message' => 'Cannot delete schedule with existing appointments. Please cancel appointments first.'
            ];
        }
        
        $deleted = $this->scheduleRepo->delete($scheduleId);
        
        if ($deleted) {
            logActivity('delete_schedule', "Deleted schedule ID: $scheduleId");
            
            return [
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to delete schedule'
        ];
    }
    
    /**
     * Get available schedules for booking
     * 
     * @param int|null $doctorId Filter by doctor
     * @param string|null $date Filter by date
     * @return array Available schedules
     */
    public function getAvailableSchedules(?int $doctorId = null, ?string $date = null): array {
        return $this->scheduleRepo->getAvailable($doctorId, $date);
    }
    
    /**
     * Get doctor schedules
     * 
     * @param int $doctorId Doctor ID
     * @return array Schedules
     */
    public function getDoctorSchedules(int $doctorId): array {
        return $this->scheduleRepo->getDoctorSchedules($doctorId, 'active');
    }
}