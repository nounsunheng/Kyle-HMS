<?php
namespace App\Models;

class Appointment extends BaseModel {
    
    protected string $table = 'appointment';
    protected string $primaryKey = 'appoid';
    
    protected array $fillable = [
        'pid', 'apponum', 'scheduleid', 'appodate', 'appotime',
        'appointment_number', 'status', 'symptoms', 'notes'
    ];
    
    /**
     * Get appointments with full details
     */
    public function getWithDetails(int $appointmentId): ?array {
        try {
            $sql = "SELECT * FROM vw_appointment_details WHERE appoid = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$appointmentId]);
            return $stmt->fetch() ?: null;
        } catch (\PDOException $e) {
            error_log("Get appointment details error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get patient appointments
     */
    public function getPatientAppointments(int $patientId, string $status = null): array {
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
        } catch (\PDOException $e) {
            error_log("Get patient appointments error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get doctor appointments
     */
    public function getDoctorAppointments(int $doctorId, string $date = null): array {
        try {
            $sql = "
                SELECT a.*, p.pname, p.ptel, p.pemail, s.title as schedule_title
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
            
            $sql .= " ORDER BY a.appodate DESC, a.appotime DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get doctor appointments error: " . $e->getMessage());
            return [];
        }
    }
}