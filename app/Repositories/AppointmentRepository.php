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
 * Appointment Repository
 * Handles all database operations for appointment table
 */
class AppointmentRepository {
    
    protected PDO $db;
    protected string $table = 'appointment';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find appointment by ID
     * 
     * @param int $appointmentId Appointment ID
     * @return array|null Appointment data
     */
    public function findById(int $appointmentId): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE appoid = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$appointmentId]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::findById - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Find appointment by appointment number
     * 
     * @param string $appointmentNumber Unique appointment number
     * @return array|null Appointment data
     */
    public function findByNumber(string $appointmentNumber): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE appointment_number = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$appointmentNumber]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::findByNumber - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get appointment with full details (using view)
     * 
     * @param int $appointmentId Appointment ID
     * @return array|null Complete appointment details
     */
    public function getWithDetails(int $appointmentId): ?array {
        try {
            $sql = "SELECT * FROM vw_appointment_details WHERE appoid = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$appointmentId]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::getWithDetails - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get patient appointments
     * 
     * @param int $patientId Patient ID
     * @param string|null $status Filter by status
     * @return array List of appointments
     */
    public function getPatientAppointments(int $patientId, ?string $status = null): array {
        try {
            $sql = "
                SELECT * FROM vw_appointment_details
                WHERE pid = ?
            ";
            
            $params = [$patientId];
            
            if ($status) {
                $sql .= " AND appointment_status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY appodate DESC, appotime DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::getPatientAppointments - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get doctor appointments
     * 
     * @param int $doctorId Doctor ID
     * @param string|null $date Filter by date
     * @param string|null $status Filter by status
     * @return array List of appointments
     */
    public function getDoctorAppointments(int $doctorId, ?string $date = null, ?string $status = null): array {
        try {
            $sql = "
                SELECT 
                    a.*,
                    p.pname, p.ptel, p.pemail, p.pdob, p.pgender,
                    s.title as schedule_title, s.scheduledate, s.scheduletime
                FROM {$this->table} a
                JOIN patient p ON a.pid = p.pid
                JOIN schedule s ON a.scheduleid = s.scheduleid
                WHERE s.docid = ?
            ";
            
            $params = [$doctorId];
            
            if ($date) {
                $sql .= " AND a.appodate = ?";
                $params[] = $date;
            }
            
            if ($status) {
                $sql .= " AND a.status = ?";
                $params[] = $status;
            }
            
            $sql .= " ORDER BY a.appodate DESC, a.appotime DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::getDoctorAppointments - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get upcoming appointments
     * 
     * @param int|null $limit Number of records
     * @return array Upcoming appointments
     */
    public function getUpcoming(?int $limit = null): array {
        try {
            $sql = "
                SELECT * FROM vw_appointment_details
                WHERE appodate >= CURDATE()
                  AND appointment_status IN ('pending', 'confirmed')
                ORDER BY appodate ASC, appotime ASC
            ";
            
            if ($limit) {
                $sql .= " LIMIT ?";
            }
            
            $stmt = $this->db->prepare($sql);
            
            if ($limit) {
                $stmt->execute([$limit]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::getUpcoming - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get today's appointments for doctor
     * 
     * @param int $doctorId Doctor ID
     * @return array Today's appointments
     */
    public function getTodayAppointments(int $doctorId): array {
        try {
            $sql = "
                SELECT 
                    a.*,
                    p.pname, p.ptel, p.pemail,
                    s.title as schedule_title
                FROM {$this->table} a
                JOIN patient p ON a.pid = p.pid
                JOIN schedule s ON a.scheduleid = s.scheduleid
                WHERE s.docid = ?
                  AND a.appodate = CURDATE()
                  AND a.status IN ('pending', 'confirmed')
                ORDER BY a.appotime ASC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$doctorId]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::getTodayAppointments - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create new appointment
     * 
     * @param array $data Appointment data
     * @return int|null Appointment ID if successful
     */
    public function create(array $data): ?int {
        try {
            $sql = "
                INSERT INTO {$this->table}
                (pid, apponum, scheduleid, appodate, appotime, appointment_number,
                 status, symptoms, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['pid'],
                $data['apponum'],
                $data['scheduleid'],
                $data['appodate'],
                $data['appotime'],
                $data['appointment_number'],
                $data['status'] ?? 'pending',
                $data['symptoms'] ?? null,
                $data['notes'] ?? null
            ]);
            
            return (int) $this->db->lastInsertId();
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::create - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Update appointment status
     * 
     * @param int $appointmentId Appointment ID
     * @param string $status New status
     * @param string|null $reason Cancellation reason
     * @return bool Success status
     */
    public function updateStatus(int $appointmentId, string $status, ?string $reason = null): bool {
        try {
            $sql = "
                UPDATE {$this->table}
                SET status = ?,
                    cancellation_reason = ?,
                    cancelled_at = ?
                WHERE appoid = ?
            ";
            
            $cancelledAt = ($status === 'cancelled') ? date('Y-m-d H:i:s') : null;
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $status,
                $reason,
                $cancelledAt,
                $appointmentId
            ]);
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::updateStatus - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update appointment notes
     * 
     * @param int $appointmentId Appointment ID
     * @param string $notes Notes content
     * @return bool Success status
     */
    public function updateNotes(int $appointmentId, string $notes): bool {
        try {
            $sql = "UPDATE {$this->table} SET notes = ? WHERE appoid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$notes, $appointmentId]);
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::updateNotes - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cancel appointment
     * 
     * @param int $appointmentId Appointment ID
     * @param string $reason Cancellation reason
     * @return bool Success status
     */
    public function cancel(int $appointmentId, string $reason): bool {
        return $this->updateStatus($appointmentId, 'cancelled', $reason);
    }
    
    /**
     * Count appointments by status
     * 
     * @param string $status Status to count
     * @param int|null $patientId Filter by patient
     * @param int|null $doctorId Filter by doctor
     * @return int Count
     */
    public function countByStatus(string $status, ?int $patientId = null, ?int $doctorId = null): int {
        try {
            $sql = "
                SELECT COUNT(*) as total
                FROM {$this->table} a
            ";
            
            $where = ["a.status = ?"];
            $params = [$status];
            
            if ($patientId) {
                $where[] = "a.pid = ?";
                $params[] = $patientId;
            }
            
            if ($doctorId) {
                $sql .= " JOIN schedule s ON a.scheduleid = s.scheduleid";
                $where[] = "s.docid = ?";
                $params[] = $doctorId;
            }
            
            $sql .= " WHERE " . implode(' AND ', $where);
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return (int) $result['total'];
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::countByStatus - Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get appointment statistics
     * 
     * @param int|null $doctorId Filter by doctor
     * @return array Statistics data
     */
    public function getStatistics(?int $doctorId = null): array {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN appodate = CURDATE() THEN 1 ELSE 0 END) as today
                FROM {$this->table} a
            ";
            
            if ($doctorId) {
                $sql .= " JOIN schedule s ON a.scheduleid = s.scheduleid WHERE s.docid = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$doctorId]);
            } else {
                $stmt = $this->db->query($sql);
            }
            
            $result = $stmt->fetch();
            return $result ?: [];
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::getStatistics - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check if patient has existing appointment
     * 
     * @param int $patientId Patient ID
     * @param int $scheduleId Schedule ID
     * @return bool True if exists
     */
    public function hasExistingAppointment(int $patientId, int $scheduleId): bool {
        try {
            $sql = "
                SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE pid = ? AND scheduleid = ? AND status IN ('pending', 'confirmed')
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$patientId, $scheduleId]);
            $result = $stmt->fetch();
            
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::hasExistingAppointment - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete appointment
     * 
     * @param int $appointmentId Appointment ID
     * @return bool Success status
     */
    public function delete(int $appointmentId): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE appoid = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$appointmentId]);
            
        } catch (PDOException $e) {
            error_log("AppointmentRepository::delete - Error: " . $e->getMessage());
            return false;
        }
    }
}