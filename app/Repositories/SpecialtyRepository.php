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
 * Specialty Repository
 * Handles all database operations for specialties table
 */
class SpecialtyRepository {

    protected PDO $db;
    protected string $table = 'specialties';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Find specialty by ID
     */
    public function findById(int $specialtyId): ?array {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$specialtyId]);

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::findById - Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all specialties
     */
    public function getAll(): array {
        try {
            $sql = "SELECT * FROM {$this->table} ORDER BY name ASC";
            $stmt = $this->db->query($sql);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::getAll - Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get specialties with doctor count
     */
    public function getWithDoctorCount(): array {
        try {
            $sql = "
                SELECT 
                    s.*,
                    COUNT(d.docid) as doctor_count,
                    SUM(CASE WHEN d.status = 'active' THEN 1 ELSE 0 END) as active_doctors
                FROM {$this->table} s
                LEFT JOIN doctor d ON s.id = d.specialties
                GROUP BY s.id
                ORDER BY s.name ASC
            ";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::getWithDoctorCount - Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get specialties with active doctors
     */
    public function getWithActiveDoctors(): array {
        try {
            $sql = "
                SELECT DISTINCT
                    s.*,
                    COUNT(d.docid) as doctor_count
                FROM {$this->table} s
                JOIN doctor d ON s.id = d.specialties
                WHERE d.status = 'active'
                GROUP BY s.id
                ORDER BY s.name ASC
            ";

            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::getWithActiveDoctors - Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search specialties by name or description
     */
    public function search(string $searchTerm): array {
        try {
            $searchPattern = "%{$searchTerm}%";

            $sql = "
                SELECT * FROM {$this->table}
                WHERE name LIKE ? OR description LIKE ?
                ORDER BY name ASC
                LIMIT 50
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$searchPattern, $searchPattern]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::search - Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create new specialty
     */
    public function create(array $data): ?int {
        try {
            $sql = "
                INSERT INTO {$this->table} (name, description, icon)
                VALUES (?, ?, ?)
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['icon'] ?? 'fas fa-stethoscope'
            ]);

            return (int)$this->db->lastInsertId();

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::create - Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update specialty
     */
    public function update(int $specialtyId, array $data): bool {
        try {
            $sql = "
                UPDATE {$this->table}
                SET name = ?, description = ?, icon = ?
                WHERE id = ?
            ";

            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['icon'] ?? 'fas fa-stethoscope',
                $specialtyId
            ]);

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::update - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete specialty
     */
    public function delete(int $specialtyId): bool {
        try {
            $sql = "DELETE FROM {$this->table} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$specialtyId]);

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::delete - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if specialty name exists
     */
    public function nameExists(string $name, ?int $excludeId = null): bool {
        try {
            $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE name = ?";
            $params = [$name];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['count'] > 0;

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::nameExists - Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get specialty statistics
     */
    public function getStatistics(): array {
        try {
            $sql = "
                SELECT 
                    COUNT(*) as total_specialties,
                    SUM(CASE WHEN EXISTS(
                        SELECT 1 FROM doctor WHERE specialties = s.id AND status = 'active'
                    ) THEN 1 ELSE 0 END) as specialties_with_doctors
                FROM {$this->table} s
            ";

            $stmt = $this->db->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result ?: [];

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::getStatistics - Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get popular specialties
     */
    public function getPopular(int $limit = 10): array {
        try {
            $sql = "
                SELECT 
                    s.*,
                    COUNT(d.docid) as doctor_count
                FROM {$this->table} s
                LEFT JOIN doctor d ON s.id = d.specialties
                WHERE d.status = 'active'
                GROUP BY s.id
                HAVING doctor_count > 0
                ORDER BY doctor_count DESC
                LIMIT ?
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("SpecialtyRepository::getPopular - Error: " . $e->getMessage());
            return [];
        }
    }
}
