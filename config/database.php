<?php
/**
 * Kyle-HMS Database Configuration
 * Secure PDO database connection with error handling
 * 
 * @author Noun Sunheng
 * @version 1.0
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_NAME', 'kyle_hms');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP has no password
define('DB_CHARSET', 'utf8mb4');

class Database {
    private static $instance = null;
    private $connection;
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Log error to file instead of displaying
            error_log("Database Connection Error: " . $e->getMessage());
            die("Database connection failed. Please contact administrator.");
        }
    }
    
    /**
     * Get singleton instance
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Prevent cloning of instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization of instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

// Create global database connection variable
$database = Database::getInstance();
$conn = $database->getConnection();
?>