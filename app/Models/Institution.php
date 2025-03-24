<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Institution {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data) {
        $sql = "INSERT INTO institutions (name, domain, logo_url) VALUES (:name, :domain, :logo_url)";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $data['name'],
                ':domain' => $data['domain'],
                ':logo_url' => $data['logo_url'] ?? null
            ]);
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao criar instituição: " . $e->getMessage());
        }
    }

    public function getById(int $id) {
        $sql = "SELECT * FROM institutions WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar instituição: " . $e->getMessage());
        }
    }

    public function getInstitutions() {
        $sql = "SELECT * FROM institutions";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao buscar instituição: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data) {
        $sql = "UPDATE institutions SET 
                name = :name,
                domain = :domain,
                logo_url = :logo_url,
                active = :active
                WHERE id = :id";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':name' => $data['name'],
                ':domain' => $data['domain'],
                ':logo_url' => $data['logo_url'] ?? null,
                ':active' => $data['active'] ?? true
            ]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao atualizar instituição: " . $e->getMessage());
        }
    }

    public function delete(int $id) {
        $sql = "DELETE FROM institutions WHERE id = :id";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        } catch (\PDOException $e) {
            throw new \Exception("Erro ao deletar instituição: " . $e->getMessage());
        }
    }

      
    public function getInstitutionsById(int $id): ?array
    {
        try {
            $query = "SELECT * FROM institutions WHERE id = :id";
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

}