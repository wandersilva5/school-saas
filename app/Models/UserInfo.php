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
}