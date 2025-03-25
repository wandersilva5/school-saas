<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Institution {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all institutions with pagination
     */
    public function getInstitutions($limit = 10, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM institutions 
                WHERE deleted_at IS NULL
                ORDER BY created_at DESC
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("Erro ao buscar instituições: " . $e->getMessage());
        }
    }
    
    /**
     * Get total number of institutions
     */
    public function getTotalInstitutions() {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM institutions 
                WHERE deleted_at IS NULL
            ");
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("Erro ao contar instituições: " . $e->getMessage());
        }
    }
    
    /**
     * Get institution by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM institutions WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("Erro ao buscar instituição: " . $e->getMessage());
        }
    }
    
    /**
     * Create a new institution
     */
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO institutions (name, domain, logo_url, email, phone, name_contact, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['name'],
                $data['domain'],
                $data['logo_url'],
                $data['email'],
                $data['phone'],
                $data['name_contact']
            ]);
            
            $institutionId = $this->db->lastInsertId();
            $this->db->commit();
            
            return $institutionId;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception("Erro ao criar instituição: " . $e->getMessage());
        }
    }
    
    /**
     * Update an existing institution
     */
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                UPDATE institutions 
                SET name = ?, domain = ?, logo_url = ?, email = ?, 
                    phone = ?, name_contact = ?, updated_at = NOW() 
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $data['name'],
                $data['domain'],
                $data['logo_url'],
                $data['email'],
                $data['phone'],
                $data['name_contact'],
                $id
            ]);
            
            $this->db->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception("Erro ao atualizar instituição: " . $e->getMessage());
        }
    }
    
    /**
     * Delete/deactivate an institution
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE institutions 
                SET deleted_at = NOW(), active = 0 
                WHERE id = ?
            ");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("Erro ao excluir instituição: " . $e->getMessage());
        }
    }
    
    /**
     * Get institutions where a guardian has students
     */
    public function getInstitutionsForGuardian($guardianId) {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT i.id, i.name, i.logo_url, i.active
                FROM institutions i
                JOIN guardians_students gs ON gs.institution_id = i.id
                WHERE gs.guardian_user_id = ?
                AND i.deleted_at IS NULL
                ORDER BY i.name
            ");
            $stmt->execute([$guardianId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("Erro ao buscar instituições do responsável: " . $e->getMessage());
        }
    }
    
    /**
     * Verify if a guardian has access to an institution
     */
    public function verifyGuardianAccess($guardianId, $institutionId) {
        try {
            $stmt = $this->db->prepare("
                SELECT i.id
                FROM institutions i
                JOIN guardians_students gs ON gs.institution_id = i.id
                WHERE gs.guardian_user_id = ?
                AND i.id = ?
                AND i.deleted_at IS NULL
                LIMIT 1
            ");
            $stmt->execute([$guardianId, $institutionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ? true : false;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception("Erro ao verificar acesso: " . $e->getMessage());
        }
    }
    
    /**
     * Handle file upload for logo
     */
    public function uploadLogo($file) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Erro no upload do arquivo');
        }
        
        $uploadDir = 'uploads/institutions/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileExtension;
        $targetPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/institutions/' . $fileName;
        } else {
            throw new \Exception('Falha ao mover o arquivo carregado');
        }
    }
}