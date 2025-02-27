<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Role
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllRoles(): array
    {
        try {
            $query = "SELECT * FROM roles ORDER BY name";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Log the error in a proper way in a production environment
            error_log("Error fetching roles: " . $e->getMessage());
            return [];
        }
    }

    
    public function getRoleById(int $id): ?array
    {
        try {
            $query = "SELECT * FROM roles WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (\PDOException $e) {
            error_log("Error fetching role by ID: " . $e->getMessage());
            return null;
        }
    }

    public function getRolesByInstitution(int $institutionId): array
    {
        try {
            $query = "SELECT * FROM roles WHERE institution_id = :institution_id ORDER BY name";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':institution_id', $institutionId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching roles by institution: " . $e->getMessage());
            return [];
        }
    }


}