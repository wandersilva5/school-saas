<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class ClassModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getClasses($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.id, 
                c.name, 
                c.shift, 
                c.year, 
                c.capacity,
                c.active,
                c.created_at,
                (SELECT COUNT(*) FROM class_students cs WHERE cs.class_id = c.id AND cs.deleted_at IS NULL) as student_count
            FROM classes c
            WHERE c.institution_id = ? 
            AND c.deleted_at IS NULL
            ORDER BY c.year DESC, c.name ASC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalClasses($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM classes 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getClassById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM classes 
            WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO classes (name, shift, year, capacity, institution_id, created_at, active) 
                VALUES (?, ?, ?, ?, ?, NOW(), 1)
            ");

            $stmt->execute([
                $data['name'],
                $data['shift'],
                $data['year'],
                $data['active'],
                $data['capacity'],
                $data['institution_id']
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao criar turma: ' . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE classes 
                SET name = ?, shift = ?, year = ?, capacity = ?, updated_at = NOW(), active = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $data['name'],
                $data['shift'],
                $data['year'],
                $data['capacity'],
                $data['active'],
                $id
            ]);

            $this->db->commit();
            return true;

        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Erro ao atualizar turma: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE classes 
                SET deleted_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao excluir turma: ' . $e->getMessage());
        }
    }

    public function getStudentsByClass($classId)
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
                gu.name AS guardian_name,
                cs.student_status as status,
                cs.joined_at
            FROM class_students cs
            JOIN users u ON cs.user_id = u.id
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            LEFT JOIN user_info ui ON u.id = ui.user_id
            LEFT JOIN guardians_students gs ON u.id = gs.student_user_id
            LEFT JOIN users gu ON gs.guardian_user_id = gu.id
            LEFT JOIN user_roles gur ON gu.id = gur.user_id
            LEFT JOIN roles gr ON gur.role_id = gr.id AND gr.name = 'Responsavel'
            WHERE cs.class_id = ? 
            ORDER BY u.name ASC
        ");
        
        $stmt->execute([$classId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addStudentToClass($classId, $studentId)
    {
        try {
            // Verificar se o aluno já está na turma
            $check = $this->db->prepare("
                SELECT COUNT(*) FROM class_students 
                WHERE class_id = ? AND user_id = ? AND deleted_at IS NULL
            ");
            $check->execute([$classId, $studentId]);
            
            if ($check->fetchColumn() > 0) {
                throw new \Exception('Este aluno já está matriculado nesta turma');
            }
            
            // Verificar se a turma tem capacidade
            $checkCapacity = $this->db->prepare("
                SELECT 
                    c.capacity,
                    (SELECT COUNT(*) 
                    FROM class_students cs 
                    WHERE cs.class_id = c.id 
                    AND cs.deleted_at IS NULL) as student_count
                FROM classes c
                WHERE c.id = ?
            ");
            $checkCapacity->execute([$classId]);
            $classInfo = $checkCapacity->fetch(PDO::FETCH_ASSOC);
            
            if ($classInfo['student_count'] >= $classInfo['capacity']) {
                throw new \Exception('A turma atingiu sua capacidade máxima');
            }
            
            // Adicionar aluno à turma
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO class_students (class_id, user_id, status, joined_at) 
                VALUES (?, ?, 'Ativo', NOW())
            ");
            $stmt->execute([$classId, $studentId]);
            
            $this->db->commit();
            return true;
            
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log($e->getMessage());
            throw new \Exception('Erro ao adicionar aluno à turma: ' . $e->getMessage());
        }
    }

    public function removeStudentFromClass($classId, $studentId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE class_students 
                SET deleted_at = NOW(), status = 'Transferido'
                WHERE class_id = ? AND user_id = ?
            ");
            $stmt->execute([$classId, $studentId]);
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao remover aluno da turma: ' . $e->getMessage());
        }
    }

    public function getAvailableStudents($institutionId, $classId)
    {
        // Buscar alunos que não estão na turma atual
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.name,
                u.email
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ?
            AND r.name = 'Aluno'
            AND u.deleted_at IS NULL
            AND u.id NOT IN (
                SELECT user_id FROM class_students 
                WHERE class_id = ? AND deleted_at IS NULL
            )
            ORDER BY u.name ASC
        ");
        
        $stmt->execute([$institutionId, $classId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}