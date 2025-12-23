<?php
namespace App\Models;

class Patient extends BaseModel {
    
    protected string $table = 'patient';
    protected string $primaryKey = 'pid';
    
    protected array $fillable = [
        'pemail', 'pname', 'pdob', 'pgender', 'ptel', 
        'paddress', 'pbloodgroup', 'pemergency_contact', 
        'pemergency_name', 'profile_image'
    ];
    
    /**
     * Find patient by email
     */
    public function findByEmail(string $email): ?array {
        return $this->first(['pemail' => $email]);
    }
    
    /**
     * Get patient with appointments
     */
    public function getWithAppointments(int $patientId): array {
        try {
            $sql = "
                SELECT p.*, 
                    COUNT(a.appoid) as total_appointments,
                    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_appointments
                FROM {$this->table} p
                LEFT JOIN appointment a ON p.pid = a.pid
                WHERE p.pid = ?
                GROUP BY p.pid
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$patientId]);
            return $stmt->fetch() ?: [];
        } catch (\PDOException $e) {
            error_log("Get patient with appointments error: " . $e->getMessage());
            return [];
        }
    }
}