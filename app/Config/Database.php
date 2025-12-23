<?php
/**
 * Database Configuration - Singleton Pattern
 * 
 * @package Kyle-HMS
 * @author Noun Sunheng
 * @version 2.0.0
 */

namespace App\Config;

use PDO;
use PDOException;

class Database {
    
    // Database credentials
    private const HOST = 'localhost';
    private const DB_NAME = 'kyle_hms';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const CHARSET = 'utf8mb4';
    
    // Singleton instance
    private static ?self $instance = null;
    
    // PDO connection
    private ?PDO $connection = null;
    
    /**
     * Private constructor - Singleton pattern
     */
    private function __construct() {
        $this->connect();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Create database connection
     */
    private function connect(): void {
        try {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::HOST,
                self::DB_NAME,
                self::CHARSET
            );
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . self::CHARSET
            ];
            
            $this->connection = new PDO($dsn, self::USERNAME, self::PASSWORD, $options);
            
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            
            if (App::ENVIRONMENT === 'development') {
                die("Database Error: " . $e->getMessage());
            } else {
                die("Database connection failed. Please contact administrator.");
            }
        }
    }
    
    /**
     * Get PDO connection
     */
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction(): bool {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit(): bool {
        return $this->connection->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback(): bool {
        return $this->connection->rollBack();
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}