<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Aluno
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAlunos($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT 
                a.id, 
                a.nome, 
                a.data_nascimento, 
                a.matricula,
                a.created_at,
                a.active,
                r.nome as responsavel_nome,
                r.id as responsavel_id
            FROM alunos a
            LEFT JOIN responsaveis r ON a.responsavel_id = r.id
            WHERE a.institution_id = ? 
            AND a.deleted_at IS NULL
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalAlunos($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM alunos 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getAlunoById($id)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, r.nome as responsavel_nome 
            FROM alunos a
            LEFT JOIN responsaveis r ON a.responsavel_id = r.id
            WHERE a.id = ? AND a.deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO alunos (nome, data_nascimento, matricula, responsavel_id, institution_id, created_at, active) 
                VALUES (?, ?, ?, ?, ?, NOW(), 1)
            ");

            $stmt->execute([
                $data['nome'],
                $data['data_nascimento'],
                $data['matricula'],
                $data['responsavel_id'],
                $data['institution_id']
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao criar aluno: ' . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE alunos 
                SET nome = ?, data_nascimento = ?, matricula = ?, responsavel_id = ?, active = ?, updated_at = NOW() 
                WHERE id = ?
            ");

            $stmt->execute([
                $data['nome'],
                $data['data_nascimento'],
                $data['matricula'],
                $data['responsavel_id'],
                isset($data['active']) ? 1 : 0,
                $id
            ]);

            $this->db->commit();
            return true;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao atualizar aluno: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE alunos 
                SET deleted_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao excluir aluno: ' . $e->getMessage());
        }
    }

    public function getResponsaveis()
    {
        $stmt = $this->db->prepare("
            SELECT id, nome 
            FROM responsaveis 
            WHERE deleted_at IS NULL
            ORDER BY nome ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
