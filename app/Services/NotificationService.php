<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Services;

use App\Config\Database;
use PDO;
use PDOException;

class NotificationService {
    
    protected PDO $db;
    protected string $table = 'notifications';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create new notification
     * 
     * @param array $data Notification data
     * @return bool Success status
     */
    public function create(array $data): bool {
        try {
            $sql = "
                INSERT INTO {$this->table} (user_email, title, message, type)
                VALUES (?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['user_email'],
                $data['title'],
                $data['message'],
                $data['type'] ?? 'system'
            ]);
            
        } catch (PDOException $e) {
            error_log("Notification Creation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user notifications
     * 
     * @param string $userEmail User email
     * @param int $limit Number of notifications
     * @return array Notifications
     */
    public function getUserNotifications(string $userEmail, int $limit = 10): array {
        try {
            $sql = "
                SELECT * FROM {$this->table}
                WHERE user_email = ?
                ORDER BY created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userEmail, $limit]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get Notifications Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get unread notifications count
     * 
     * @param string $userEmail User email
     * @return int Count
     */
    public function getUnreadCount(string $userEmail): int {
        try {
            $sql = "
                SELECT COUNT(*) as count
                FROM {$this->table}
                WHERE user_email = ? AND is_read = 0
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userEmail]);
            $result = $stmt->fetch();
            
            return (int) $result['count'];
            
        } catch (PDOException $e) {
            error_log("Get Unread Count Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Mark notification as read
     * 
     * @param int $notificationId Notification ID
     * @return bool Success status
     */
    public function markAsRead(int $notificationId): bool {
        try {
            $sql = "UPDATE {$this->table} SET is_read = 1 WHERE notif_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$notificationId]);
            
        } catch (PDOException $e) {
            error_log("Mark As Read Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Mark all notifications as read
     * 
     * @param string $userEmail User email
     * @return bool Success status
     */
    public function markAllAsRead(string $userEmail): bool {
        try {
            $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userEmail]);
            
        } catch (PDOException $e) {
            error_log("Mark All As Read Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete notification
     * 
     * @param int $notificationId Notification ID
     * @return bool Success status
     */
    public function delete(int $notificationId): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE notif_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$notificationId]);
            
        } catch (PDOException $e) {
            error_log("Delete Notification Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete all user notifications
     * 
     * @param string $userEmail User email
     * @return bool Success status
     */
    public function deleteAll(string $userEmail): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE user_email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$userEmail]);
            
        } catch (PDOException $e) {
            error_log("Delete All Notifications Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get unread notifications (full data)
     * 
     * @param string $userEmail User email
     * @param int $limit Number of notifications
     * @return array Unread notifications
     */
    public function getUnread(string $userEmail, int $limit = 10): array {
        try {
            $sql = "
                SELECT * FROM {$this->table}
                WHERE user_email = ? AND is_read = 0
                ORDER BY created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userEmail, $limit]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("Get Unread Notifications Error: " . $e->getMessage());
            return [];
        }
    }
}