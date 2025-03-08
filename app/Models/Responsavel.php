<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Responsavel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getResponsaveis($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.id, 
                r.nome, 
                r.email, 
                r.telefone,
                r.cpf,
                r.created_at,
                r.active,
                COUNT(a.id) as total_alunos
            FROM responsaveis r
            LEFT JOIN alunos a ON r.id = a.responsavel_id
            WHERE r.institution_id = ? 
            AND r.deleted_at IS NULL
            GROUP BY r.id, r.nome, r.email, r.telefone, r.cpf, r.created_at, r.active
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalResponsaveis($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM responsaveis 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getResponsavelById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM responsaveis 
            WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAlunosByResponsavel($responsavelId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM alunos 
            WHERE responsavel_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$responsavelId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO responsaveis (nome, email, telefone, cpf, institution_id, created_at, active) 
                VALUES (?, ?, ?, ?, ?, NOW(), 1)
            ");

            $stmt->execute([
                $data['nome'],
                $data['email'],
                $data['telefone'],
                $data['cpf'],
                $data['institution_id']
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao criar responsável: ' . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE responsaveis 
                SET nome = ?, email = ?, telefone = ?, cpf = ?, active = ?, updated_at = NOW() 
                WHERE id = ?
            ");

            $stmt->execute([
                $data['nome'],
                $data['email'],
                $data['telefone'],
                $data['cpf'],
                isset($data['active']) ? 1 : 0,
                $id
            ]);

            $this->db->commit();
            return true;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao atualizar responsável: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE responsaveis 
                SET deleted_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao excluir responsável: ' . $e->getMessage());
        }
    }
}
