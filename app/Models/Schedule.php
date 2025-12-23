<?php
namespace App\Models;

class Schedule extends BaseModel {
    
    protected string $table = 'schedule';
    protected string $primaryKey = 'scheduleid';
    
    protected array $fillable = [
        'docid', 'title', 'scheduledate', 'scheduletime',
        'duration', 'nop', 'booked', 'status'
    ];
    
    /**
     * Get available schedules
     */
    public function getAvailableSchedules(int $doctorId = null, string $date = null): array {
        try {
            $sql = "
                SELECT * FROM vw_doctor_schedule
                WHERE schedule_status = 'active'
                AND available_slots > 0
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
            
            $sql .= " ORDER BY scheduledate, scheduletime";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("Get available schedules error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Check schedule availability
     */
    public function checkAvailability(int $scheduleId): array {
        try {
            $sql = "
                SELECT scheduleid, nop, booked, (nop - booked) as available
                FROM {$this->table}
                WHERE scheduleid = ? AND status = 'active'
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$scheduleId]);
            return $stmt->fetch() ?: [];
        } catch (\PDOException $e) {
            error_log("Check availability error: " . $e->getMessage());
            return [];
        }
    }
}