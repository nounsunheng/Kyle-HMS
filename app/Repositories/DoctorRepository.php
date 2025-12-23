<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Repositories;

use App\Config\Database;
use PDO;
use PDOException;

/**
 * Doctor Repository
 * Handles all database operations for doctor table
 */
class DoctorRepository {
    
    protected PDO $db;
    protected string $table = 'doctor';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find doctor by ID
     * 
     * @param int $doctorId Doctor ID
     * @return array|null Doctor data
     */
    public function findById(int $doctorId): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE docid = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$doctorId]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::findById - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find doctor by email
     * 
     * @param string $email Doctor email
     * @return array|null Doctor data
     */
    public function findByEmail(string $email): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE docemail = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::findByEmail - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all doctors with specialty information
     * 
     * @param string $status Filter by status (active, inactive, on_leave)
     * @return array List of doctors with specialties
     */
    public function getAllWithSpecialties(string $status = 'active'): array {
        try {
            $sql = "
                SELECT 
                    d.*,
                    s.name as specialty_name,
                    s.icon as specialty_icon,
                    s.description as specialty_description
                FROM {$this->table} d
                LEFT JOIN specialties s ON d.specialties = s.id
                WHERE d.status = ?
                ORDER BY d.docname
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::getAllWithSpecialties - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get doctors by specialty
     * 
     * @param int $specialtyId Specialty ID
     * @return array List of doctors
     */
    public function getBySpecialty(int $specialtyId): array {
        try {
            $sql = "
                SELECT 
                    d.*,
                    s.name as specialty_name
                FROM {$this->table} d
                JOIN specialties s ON d.specialties = s.id
                WHERE d.specialties = ? AND d.status = 'active'
                ORDER BY d.docname
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$specialtyId]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::getBySpecialty - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get doctor with complete statistics
     * 
     * @param int $doctorId Doctor ID
     * @return array Doctor data with statistics
     */
    public function getWithStatistics(int $doctorId): array {
        try {
            $sql = "
                SELECT 
                    d.*,
                    s.name as specialty_name,
                    COUNT(DISTINCT sch.scheduleid) as total_schedules,
                    COUNT(DISTINCT a.appoid) as total_appointments,
                    COUNT(DISTINCT CASE WHEN a.status = 'completed' THEN a.appoid END) as completed_appointments,
                    COUNT(DISTINCT CASE WHEN a.status = 'pending' THEN a.appoid END) as pending_appointments,
                    COUNT(DISTINCT CASE WHEN a.status = 'confirmed' THEN a.appoid END) as confirmed_appointments,
                    COUNT(DISTINCT a.pid) as total_patients
                FROM {$this->table} d
                LEFT JOIN specialties s ON d.specialties = s.id
                LEFT JOIN schedule sch ON d.docid = sch.docid
                LEFT JOIN appointment a ON sch.scheduleid = a.scheduleid
                WHERE d.docid = ?
                GROUP BY d.docid
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$doctorId]);
            
            $result = $stmt->fetch();
            return $result ?: [];
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::getWithStatistics - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search doctors by name or specialty
     * 
     * @param string $searchTerm Search keyword
     * @return array Matching doctors
     */
    public function search(string $searchTerm): array {
        try {
            $searchPattern = "%{$searchTerm}%";
            
            $sql = "
                SELECT 
                    d.*,
                    s.name as specialty_name
                FROM {$this->table} d
                LEFT JOIN specialties s ON d.specialties = s.id
                WHERE d.status = 'active' 
                  AND (d.docname LIKE ? OR s.name LIKE ?)
                ORDER BY d.docname
                LIMIT 50
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchPattern, $searchPattern]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::search - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new doctor
     * 
     * @param array $data Doctor data
     * @return int|null Doctor ID if successful
     */
    public function create(array $data): ?int {
        try {
            $sql = "
                INSERT INTO {$this->table}
                (docemail, docname, doctel, specialties, docdegree, docexperience,
                 docbio, docconsultation_fee, profile_image, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['docemail'],
                $data['docname'],
                $data['doctel'],
                $data['specialties'],
                $data['docdegree'],
                $data['docexperience'] ?? 0,
                $data['docbio'] ?? null,
                $data['docconsultation_fee'] ?? 0.00,
                $data['profile_image'] ?? 'default-doctor.png',
                $data['status'] ?? 'active'
            ]);
            
            return (int) $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::create - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update doctor information
     * 
     * @param int $doctorId Doctor ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(int $doctorId, array $data): bool {
        try {
            $sql = "
                UPDATE {$this->table}
                SET docname = ?, doctel = ?, specialties = ?, docdegree = ?,
                    docexperience = ?, docbio = ?, docconsultation_fee = ?,
                    profile_image = ?, status = ?
                WHERE docid = ?
            ";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['docname'],
                $data['doctel'],
                $data['specialties'],
                $data['docdegree'],
                $data['docexperience'] ?? 0,
                $data['docbio'] ?? null,
                $data['docconsultation_fee'] ?? 0.00,
                $data['profile_image'] ?? 'default-doctor.png',
                $data['status'] ?? 'active',
                $doctorId
            ]);
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::update - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete doctor
     * 
     * @param int $doctorId Doctor ID
     * @return bool Success status
     */
    public function delete(int $doctorId): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE docid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$doctorId]);
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::delete - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total doctors
     * 
     * @param string $status Filter by status
     * @return int Total count
     */
    public function count(string $status = 'active'): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
            $result = $stmt->fetch();
            
            return (int) $result['total'];
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::count - Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get doctors with available schedules
     * 
     * @param string $date Specific date (optional)
     * @return array Doctors with availability
     */
    public function getWithAvailableSchedules(?string $date = null): array {
        try {
            $sql = "
                SELECT DISTINCT
                    d.*,
                    s.name as specialty_name,
                    COUNT(sch.scheduleid) as available_schedules
                FROM {$this->table} d
                JOIN specialties s ON d.specialties = s.id
                JOIN schedule sch ON d.docid = sch.docid
                WHERE d.status = 'active'
                  AND sch.status = 'active'
                  AND (sch.nop - sch.booked) > 0
            ";
            
            if ($date) {
                $sql .= " AND sch.scheduledate = ?";
            }
            
            $sql .= "
                GROUP BY d.docid
                ORDER BY d.docname
            ";
            
            $stmt = $this->db->prepare($sql);
            
            if ($date) {
                $stmt->execute([$date]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("DoctorRepository::getWithAvailableSchedules - Error: " . $e->getMessage());
            return [];
        }
    }
}