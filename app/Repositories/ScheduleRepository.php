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
 * Schedule Repository
 * Handles all database operations for schedule table
 */
class ScheduleRepository {
    
    protected PDO $db;
    protected string $table = 'schedule';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find schedule by ID
     * 
     * @param int $scheduleId Schedule ID
     * @return array|null Schedule data
     */
    public function findById(int $scheduleId): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE scheduleid = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$scheduleId]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::findById - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get schedule with details (using view)
     * 
     * @param int $scheduleId Schedule ID
     * @return array|null Complete schedule details
     */
    public function getWithDetails(int $scheduleId): ?array {
        try {
            $sql = "SELECT * FROM vw_doctor_schedule WHERE scheduleid = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$scheduleId]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::getWithDetails - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get available schedules
     * 
     * @param int|null $doctorId Filter by doctor
     * @param string|null $date Filter by date
     * @return array List of available schedules
     */
    public function getAvailable(?int $doctorId = null, ?string $date = null): array {
        try {
            $sql = "
                SELECT * FROM vw_doctor_schedule
                WHERE schedule_status = 'active'
                  AND available_slots > 0
                  AND scheduledate >= CURDATE()
            ";
            
            $params = [];
            
            if ($doctorId) {
                $sql .= " AND docid = ?";
                $params[] = $doctorId;
            }
            
            if ($date) {
                $sql .= " AND scheduledate = ?";
                $params[] = $date;
            }
            
            $sql .= " ORDER BY scheduledate ASC, scheduletime ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::getAvailable - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get doctor schedules
     * 
     * @param int $doctorId Doctor ID
     * @param string|null $status Filter by status
     * @return array List of schedules
     */
    public function getDoctorSchedules(int $doctorId, ?string $status = null): array {
        try {
            $sql = "
                SELECT s.*, 
                    (s.nop - s.booked) as available_slots,
                    COUNT(a.appoid) as appointment_count
                FROM {$this->table} s
                LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
                WHERE s.docid = ?
            ";
            
            $params = [$doctorId];
            
            if ($status) {
                $sql .= " AND s.status = ?";
                $params[] = $status;
            }
            
            $sql .= "
                GROUP BY s.scheduleid
                ORDER BY s.scheduledate DESC, s.scheduletime DESC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::getDoctorSchedules - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get upcoming schedules for doctor
     * 
     * @param int $doctorId Doctor ID
     * @param int $limit Number of records
     * @return array Upcoming schedules
     */
    public function getUpcoming(int $doctorId, int $limit = 10): array {
        try {
            $sql = "
                SELECT * FROM vw_doctor_schedule
                WHERE docid = ?
                  AND scheduledate >= CURDATE()
                  AND schedule_status = 'active'
                ORDER BY scheduledate ASC, scheduletime ASC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$doctorId, $limit]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::getUpcoming - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check schedule availability
     * 
     * @param int $scheduleId Schedule ID
     * @return array Availability info (total, booked, available)
     */
    public function checkAvailability(int $scheduleId): array {
        try {
            $sql = "
                SELECT 
                    scheduleid,
                    nop as total_slots,
                    booked as booked_slots,
                    (nop - booked) as available_slots,
                    status,
                    scheduledate,
                    scheduletime
                FROM {$this->table}
                WHERE scheduleid = ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$scheduleId]);
            
            $result = $stmt->fetch();
            return $result ?: [];
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::checkAvailability - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new schedule
     * 
     * @param array $data Schedule data
     * @return int|null Schedule ID if successful
     */
    public function create(array $data): ?int {
        try {
            $sql = "
                INSERT INTO {$this->table}
                (docid, title, scheduledate, scheduletime, duration, nop, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['docid'],
                $data['title'],
                $data['scheduledate'],
                $data['scheduletime'],
                $data['duration'] ?? 30,
                $data['nop'],
                $data['status'] ?? 'active'
            ]);
            
            return (int) $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::create - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update schedule
     * 
     * @param int $scheduleId Schedule ID
     * @param array $data Updated data
     * @return bool Success status
     */
    public function update(int $scheduleId, array $data): bool {
        try {
            $sql = "
                UPDATE {$this->table}
                SET title = ?, scheduledate = ?, scheduletime = ?,
                    duration = ?, nop = ?, status = ?
                WHERE scheduleid = ?
            ";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['title'],
                $data['scheduledate'],
                $data['scheduletime'],
                $data['duration'] ?? 30,
                $data['nop'],
                $data['status'] ?? 'active',
                $scheduleId
            ]);
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::update - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update schedule status
     * 
     * @param int $scheduleId Schedule ID
     * @param string $status New status
     * @return bool Success status
     */
    public function updateStatus(int $scheduleId, string $status): bool {
        try {
            $sql = "UPDATE {$this->table} SET status = ? WHERE scheduleid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $scheduleId]);
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::updateStatus - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Increment booked count
     * Note: This is handled by trigger, but kept for manual operations
     * 
     * @param int $scheduleId Schedule ID
     * @return bool Success status
     */
    public function incrementBooked(int $scheduleId): bool {
        try {
            $sql = "UPDATE {$this->table} SET booked = booked + 1 WHERE scheduleid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$scheduleId]);
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::incrementBooked - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Decrement booked count
     * Note: This is handled by trigger, but kept for manual operations
     * 
     * @param int $scheduleId Schedule ID
     * @return bool Success status
     */
    public function decrementBooked(int $scheduleId): bool {
        try {
            $sql = "UPDATE {$this->table} SET booked = booked - 1 WHERE scheduleid = ? AND booked > 0";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$scheduleId]);
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::decrementBooked - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check for scheduling conflicts
     * 
     * @param int $doctorId Doctor ID
     * @param string $date Schedule date
     * @param string $time Schedule time
     * @param int|null $excludeScheduleId Exclude this schedule from check
     * @return bool True if conflict exists
     */
    public function hasConflict(int $doctorId, string $date, string $time, ?int $excludeScheduleId = null): bool {
        try {
            $sql = "
                SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE docid = ?
                  AND scheduledate = ?
                  AND scheduletime = ?
                  AND status = 'active'
            ";
            
            $params = [$doctorId, $date, $time];
            
            if ($excludeScheduleId) {
                $sql .= " AND scheduleid != ?";
                $params[] = $excludeScheduleId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::hasConflict - Error: " . $e->getMessage());
            return true; // Return true on error to be safe
        }
    }
    
    /**
     * Delete schedule
     * 
     * @param int $scheduleId Schedule ID
     * @return bool Success status
     */
    public function delete(int $scheduleId): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE scheduleid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$scheduleId]);
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::delete - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get schedule statistics
     * 
     * @param int|null $doctorId Filter by doctor
     * @return array Statistics data
     */
    public function getStatistics(?int $doctorId = null): array {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_schedules,
                    SUM(nop) as total_capacity,
                    SUM(booked) as total_booked,
                    SUM(nop - booked) as total_available,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_schedules,
                    SUM(CASE WHEN scheduledate = CURDATE() THEN 1 ELSE 0 END) as today_schedules
                FROM {$this->table}
            ";
            
            if ($doctorId) {
                $sql .= " WHERE docid = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$doctorId]);
            } else {
                $stmt = $this->db->query($sql);
            }
            
            $result = $stmt->fetch();
            return $result ?: [];
            
        } catch (PDOException $e) {
            error_log("ScheduleRepository::getStatistics - Error: " . $e->getMessage());
            return [];
        }
    }
}