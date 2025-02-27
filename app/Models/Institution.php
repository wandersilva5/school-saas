<?php

namespace App\Models;

use App\Config\Database;

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
}