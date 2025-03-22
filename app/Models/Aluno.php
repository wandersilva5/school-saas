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
            u.id,
            u.name,
            u.email,
            u.active,
            u.created_at,
            u.institution_id,
            r.name AS role_name,
            ui.registration_number,
            ui.birth_date,
            ui.gender,
            ui.blood_type,
            ui.address_street,
            ui.address_number,
            ui.address_complement,
            ui.address_district,
            ui.address_city,
            ui.address_state,
            ui.address_zipcode,
            ui.emergency_contact,
            ui.emergency_phone,
            ui.health_insurance,
            ui.health_observations,
            ui.previous_school,
            ui.observation,
            gu.id AS guardian_id,
            gu.name AS guardian_name
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        LEFT JOIN user_info ui ON u.id = ui.user_id
        LEFT JOIN guardians_students gs ON u.id = gs.student_user_id
        LEFT JOIN users gu ON gs.guardian_user_id = gu.id
        LEFT JOIN user_roles gur ON gu.id = gur.user_id
        LEFT JOIN roles gr ON gur.role_id = gr.id AND gr.name = 'Responsavel'
        WHERE r.name = 'Aluno'
        AND u.deleted_at IS NULL
        AND u.institution_id = ? 
        LIMIT ? OFFSET ?
        ORDER BY 
        u.name ASC;
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
