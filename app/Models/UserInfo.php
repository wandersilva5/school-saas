<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class UserInfo
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // [Existing methods remain unchanged]
    
    /**
     * Get student info from user_info table
     */
    public function getAlunoInfoById($alunoId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM user_info
                WHERE user_id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$alunoId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao buscar informações do aluno: ' . $e->getMessage());
        }
    }
    
    /**
     * Create new student info
     */
    public function createAlunoInfo($data)
    {
        try {
            $this->db->beginTransaction();
            
            // Verifica se já existe registro para este aluno
            $stmt = $this->db->prepare("
                SELECT id FROM user_info WHERE user_id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$data['user_id']]);
            
            if ($stmt->fetch()) {
                throw new \Exception('Já existe informação para este aluno. Utilize a função de atualização.');
            }
            
            // Prepara a query de inserção
            $fields = array_keys($data);
            $placeholders = array_fill(0, count($fields), '?');
            
            $sql = "INSERT INTO user_info (" . implode(', ', $fields) . ")
                    VALUES (" . implode(', ', $placeholders) . ")";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute(array_values($data));
            
            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao criar informações do aluno: ' . $e->getMessage());
        }
    }
    
    /**
     * Update student info
     */
    public function updateAlunoInfo($infoId, $data)
    {
        try {
            $this->db->beginTransaction();
            
            // Remove o user_id do array de atualização (não deve ser alterado)
            $userId = $data['user_id'];
            unset($data['user_id']);
            
            // Prepara a query de atualização
            $fields = array_map(function($field) {
                return "$field = ?";
            }, array_keys($data));
            
            $sql = "UPDATE user_info SET " . implode(', ', $fields) . ", updated_at = NOW()
                    WHERE id = ? AND user_id = ?";
            
            // Adiciona os parâmetros ID e user_id ao final
            $params = array_values($data);
            $params[] = $infoId;
            $params[] = $userId;
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);
            
            if (!$result) {
                throw new \Exception('Falha ao atualizar informações do aluno');
            }
            
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao atualizar informações do aluno: ' . $e->getMessage());
        }
    }

    public function getStudentInfo($studentId, $institutionId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, u.name, u.email, u.phone, u.active,
                    si.registration_number, si.birth_date, 
                    si.address_street, si.address_number, si.address_district,
                    si.address_city, si.address_state, si.address_zipcode,
                    si.health_insurance, si.health_observations,
                    gs.guardian_user_id,
                    gu.name as guardian_name
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                LEFT JOIN student_info si ON u.id = si.user_id
                LEFT JOIN guardians_students gs ON u.id = gs.student_user_id
                LEFT JOIN users gu ON gs.guardian_user_id = gu.id
                WHERE u.id = ? AND u.institution_id = ?
            ");
            
            $stmt->execute([$studentId, $institutionId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Error fetching student info: " . $e->getMessage());
        }
    }
}