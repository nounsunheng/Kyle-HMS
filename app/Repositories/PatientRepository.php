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
 * Patient Repository
 * Handles all database operations for patient table
 */
class PatientRepository {
    
    protected PDO $db;
    protected string $table = 'patient';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find patient by ID
     * 
     * @param int $patientId Patient ID
     * @return array|null Patient data
     */
    public function findById(int $patientId): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE pid = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$patientId]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("PatientRepository::findById - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find patient by email
     * 
     * @param string $email Patient email
     * @return array|null Patient data
     */
    public function findByEmail(string $email): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE pemail = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("PatientRepository::findByEmail - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get patient with appointment statistics
     * 
     * @param int $patientId Patient ID
     * @return array Patient data with stats
     */
    public function getWithStatistics(int $patientId): array {
        try {
            $sql = "
                SELECT 
                    p.*,
                    COUNT(a.appoid) as total_appointments,
                    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_appointments,
                    SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending_appointments,
                    SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_appointments,
                    MAX(a.appodate) as last_appointment_date
                FROM {$this->table} p
                LEFT JOIN appointment a ON p.pid = a.pid
                WHERE p.pid = ?
                GROUP BY p.pid
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$patientId]);
            
            $result = $stmt->fetch();
            return $result ?: [];
            
        } catch (PDOException $e) {
            error_log("PatientRepository::getWithStatistics - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all patients with pagination
     * 
     * @param int $limit Number of records per page
     * @param int $offset Starting position
     * @return array List of patients
     */
    public function getAllPaginated(int $limit = 10, int $offset = 0): array {
        try {
            $sql = "
                SELECT p.*, w.status, w.created_at as registered_at
                FROM {$this->table} p
                JOIN webuser w ON p.pemail = w.email
                ORDER BY p.pid DESC
                LIMIT ? OFFSET ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("PatientRepository::getAllPaginated - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search patients by name or email
     * 
     * @param string $searchTerm Search keyword
     * @return array Matching patients
     */
    public function search(string $searchTerm): array {
        try {
            $searchPattern = "%{$searchTerm}%";
            
            $sql = "
                SELECT p.*, w.status
                FROM {$this->table} p
                JOIN webuser w ON p.pemail = w.email
                WHERE p.pname LIKE ? OR p.pemail LIKE ? OR p.ptel LIKE ?
                ORDER BY p.pname
                LIMIT 50
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchPattern, $searchPattern, $searchPattern]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("PatientRepository::search - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new patient
     * 
     * @param array $data Patient data
     * @return int|null Patient ID if successful
     */
    public function create(array $data): ?int {
        try {
            $sql = "
                INSERT INTO {$this->table} 
                (pemail, pname, pdob, pgender, ptel, paddress, pbloodgroup, 
                 pemergency_contact, pemergency_name, profile_image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['pemail'],
                $data['pname'],
                $data['pdob'],
                $data['pgender'],
                $data['ptel'],
                $data['paddress'],
                $data['pbloodgroup'] ?? null,
                $data['pemergency_contact'] ?? null,
                $data['pemergency_name'] ?? null,
                $data['profile_image'] ?? 'default-avatar.png'
            ]);
            
            return (int) $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("PatientRepository::create - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update patient information
     * 
     * @param int $patientId Patient ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(int $patientId, array $data): bool {
        try {
            $sql = "
                UPDATE {$this->table} 
                SET pname = ?, pdob = ?, pgender = ?, ptel = ?, paddress = ?,
                    pbloodgroup = ?, pemergency_contact = ?, pemergency_name = ?,
                    profile_image = ?
                WHERE pid = ?
            ";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['pname'],
                $data['pdob'],
                $data['pgender'],
                $data['ptel'],
                $data['paddress'],
                $data['pbloodgroup'] ?? null,
                $data['pemergency_contact'] ?? null,
                $data['pemergency_name'] ?? null,
                $data['profile_image'] ?? 'default-avatar.png',
                $patientId
            ]);
            
        } catch (PDOException $e) {
            error_log("PatientRepository::update - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete patient
     * 
     * @param int $patientId Patient ID
     * @return bool Success status
     */
    public function delete(int $patientId): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE pid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$patientId]);
            
        } catch (PDOException $e) {
            error_log("PatientRepository::delete - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Count total patients
     * 
     * @return int Total count
     */
    public function count(): int {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$this->table}";
            $stmt = $this->db->query($sql);
            $result = $stmt->fetch();
            
            return (int) $result['total'];
            
        } catch (PDOException $e) {
            error_log("PatientRepository::count - Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recently registered patients
     * 
     * @param int $limit Number of records
     * @return array Recent patients
     */
    public function getRecentlyRegistered(int $limit = 10): array {
        try {
            $sql = "
                SELECT p.*, w.created_at as registered_at
                FROM {$this->table} p
                JOIN webuser w ON p.pemail = w.email
                ORDER BY w.created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("PatientRepository::getRecentlyRegistered - Error: " . $e->getMessage());
            return [];
        }
    }
}