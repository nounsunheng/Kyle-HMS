<?php
/**
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Repositories;

use App\Config\Database;
use App\Config\Security;
use PDO;
use PDOException;

/**
 * User Repository
 * Handles all database operations for webuser table
 */
class UserRepository {
    
    protected PDO $db;
    protected string $table = 'webuser';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Find user by email
     * 
     * @param string $email User email address
     * @return array|null User data or null if not found
     */
    public function findByEmail(string $email): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            
            $result = $stmt->fetch();
            return $result ?: null;
            
        } catch (PDOException $e) {
            error_log("UserRepository::findByEmail - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Verify user credentials
     * 
     * @param string $email User email
     * @param string $password Plain text password
     * @return array|null User data if credentials valid, null otherwise
     */
    public function verifyCredentials(string $email, string $password): ?array {
        try {
            $user = $this->findByEmail($email);
            
            if (!$user) {
                return null;
            }
            
            // Check if account is active
            if ($user['status'] !== 'active') {
                return null;
            }
            
            // Verify password
            if (Security::verifyPassword($password, $user['password'])) {
                return $user;
            }
            
            return null;
            
        } catch (PDOException $e) {
            error_log("UserRepository::verifyCredentials - Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create new user
     * 
     * @param array $data User data (email, password, usertype)
     * @return bool Success status
     */
    public function create(array $data): bool {
        try {
            // Hash password before storing
            if (isset($data['password'])) {
                $data['password'] = Security::hashPassword($data['password']);
            }
            
            $sql = "
                INSERT INTO {$this->table} (email, password, usertype, status) 
                VALUES (?, ?, ?, ?)
            ";
            
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['email'],
                $data['password'],
                $data['usertype'],
                $data['status'] ?? 'active'
            ]);
            
        } catch (PDOException $e) {
            error_log("UserRepository::create - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update last login timestamp
     * 
     * @param string $email User email
     * @return bool Success status
     */
    public function updateLastLogin(string $email): bool {
        try {
            $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$email]);
            
        } catch (PDOException $e) {
            error_log("UserRepository::updateLastLogin - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user password
     * 
     * @param string $email User email
     * @param string $newPassword New plain text password
     * @return bool Success status
     */
    public function updatePassword(string $email, string $newPassword): bool {
        try {
            $hashedPassword = Security::hashPassword($newPassword);
            
            $sql = "UPDATE {$this->table} SET password = ? WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$hashedPassword, $email]);
            
        } catch (PDOException $e) {
            error_log("UserRepository::updatePassword - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user status
     * 
     * @param string $email User email
     * @param string $status New status (active, inactive, suspended)
     * @return bool Success status
     */
    public function updateStatus(string $email, string $status): bool {
        try {
            $sql = "UPDATE {$this->table} SET status = ? WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$status, $email]);
            
        } catch (PDOException $e) {
            error_log("UserRepository::updateStatus - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if email exists
     * 
     * @param string $email Email to check
     * @return bool True if exists
     */
    public function emailExists(string $email): bool {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            
            $result = $stmt->fetch();
            return $result['count'] > 0;
            
        } catch (PDOException $e) {
            error_log("UserRepository::emailExists - Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all users by type
     * 
     * @param string $usertype User type (p, d, a)
     * @return array List of users
     */
    public function getUsersByType(string $usertype): array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE usertype = ? ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$usertype]);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            error_log("UserRepository::getUsersByType - Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Delete user (cascade will delete related records)
     * 
     * @param string $email User email
     * @return bool Success status
     */
    public function delete(string $email): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$email]);
            
        } catch (PDOException $e) {
            error_log("UserRepository::delete - Error: " . $e->getMessage());
            return false;
        }
    }
}