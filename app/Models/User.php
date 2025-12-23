<?php
namespace App\Models;

use App\Config\Security;

class User extends BaseModel {
    
    protected string $table = 'webuser';
    protected string $primaryKey = 'email';
    
    protected array $fillable = [
        'email', 'password', 'usertype', 'status'
    ];
    
    protected array $hidden = ['password'];
    
    /**
     * Find user by email
     */
    public function findByEmail(string $email): ?array {
        return $this->first(['email' => $email]);
    }
    
    /**
     * Create new user with hashed password
     */
    public function createUser(array $data): bool {
        if (isset($data['password'])) {
            $data['password'] = Security::hashPassword($data['password']);
        }
        
        return $this->create($data) !== null;
    }
    
    /**
     * Verify login credentials
     */
    public function verifyCredentials(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        
        if (!$user) {
            return null;
        }
        
        if (Security::verifyPassword($password, $user['password'])) {
            return $user;
        }
        
        return null;
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin(string $email): bool {
        try {
            $sql = "UPDATE {$this->table} SET last_login = NOW() WHERE email = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$email]);
        } catch (\PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
            return false;
        }
    }
}