<?php
namespace App\Models;

class Doctor extends BaseModel {
    
    protected string $table = 'doctor';
    protected string $primaryKey = 'docid';
    
    protected array $fillable = [
        'docemail', 'docname', 'doctel', 'specialties',
        'docdegree', 'docexperience', 'docbio',
        'docconsultation_fee', 'profile_image', 'status'
    ];
    
    /**
     * Find doctor by email
     */
    public function findByEmail(string $email): ?array {
        return $this->first(['docemail' => $email]);
    }
    
    /**
     * Get all doctors with specialty info
     */
    public function getAllWithSpecialties(): array {
        try {
            $sql = "
                SELECT d.*, s.name as specialty_name, s.icon as specialty_icon
                FROM {$this->table} d
                LEFT JOIN specialties s ON d.specialties = s.id
                WHERE d.status = 'active'
                ORDER BY d.docname
            ";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get doctors with specialties error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get doctor statistics
     */
    public function getStatistics(int $doctorId): array {
        try {
            $sql = "
                SELECT 
                    COUNT(DISTINCT a.appoid) as total_appointments,
                    COUNT(DISTINCT CASE WHEN a.status = 'completed' THEN a.appoid END) as completed,
                    COUNT(DISTINCT CASE WHEN a.status = 'pending' THEN a.appoid END) as pending,
                    COUNT(DISTINCT a.pid) as total_patients,
                    COUNT(DISTINCT s.scheduleid) as total_schedules
                FROM doctor d
                LEFT JOIN schedule s ON d.docid = s.docid
                LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
                WHERE d.docid = ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$doctorId]);
            return $stmt->fetch() ?: [];
        } catch (\PDOException $e) {
            error_log("Get doctor statistics error: " . $e->getMessage());
            return [];
        }
    }
}