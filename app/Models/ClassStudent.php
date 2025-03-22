<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class ClassStudent
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getStudentClasses($studentId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.id,
                c.name,
                c.shift,
                c.year,
                cs.status,
                cs.joined_at
            FROM class_students cs
            JOIN classes c ON cs.class_id = c.id
            WHERE cs.student_id = ? 
            AND cs.deleted_at IS NULL
            ORDER BY c.year DESC, c.name ASC
        ");
        
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getClassStudents($classId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.name,
                u.email,
                cs.status,
                cs.joined_at,
                cs.left_at
            FROM class_students cs
            JOIN users u ON cs.student_id = u.id
            WHERE cs.class_id = ? 
            AND cs.deleted_at IS NULL
            ORDER BY u.name ASC
        ");
        
        $stmt->execute([$classId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addStudent($classId, $studentId, $status = 'Ativo')
    {
        try {
            // Verificar se o aluno já está na turma
            $check = $this->db->prepare("
                SELECT COUNT(*) FROM class_students 
                WHERE class_id = ? AND student_id = ? AND deleted_at IS NULL
            ");
            $check->execute([$classId, $studentId]);
            
            if ($check->fetchColumn() > 0) {
                throw new \Exception('Este aluno já está matriculado nesta turma');
            }
            
            // Verificar se a turma tem capacidade
            $checkCapacity = $this->db->prepare("
                SELECT 
                    c.capacity,
                    (SELECT COUNT(*) FROM class_students cs WHERE cs.class_id = c.id AND cs.deleted_at IS NULL) as student_count
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
                INSERT INTO class_students (class_id, student_id, status, joined_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$classId, $studentId, $status]);
            
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

    public function removeStudent($classId, $studentId, $reason = 'Transferido')
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE class_students 
                SET 
                    status = ?,
                    left_at = NOW(),
                    deleted_at = NOW()
                WHERE class_id = ? AND student_id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$reason, $classId, $studentId]);
            
            if ($stmt->rowCount() === 0) {
                throw new \Exception('Registro não encontrado ou já removido');
            }
            
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao remover aluno da turma: ' . $e->getMessage());
        }
    }

    public function updateStatus($classId, $studentId, $status)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE class_students 
                SET status = ? 
                WHERE class_id = ? AND student_id = ? AND deleted_at IS NULL
            ");
            $stmt->execute([$status, $classId, $studentId]);
            
            if ($stmt->rowCount() === 0) {
                throw new \Exception('Registro não encontrado ou aluno não está na turma');
            }
            
            // Se o status for 'Concluído' ou 'Desistente', definir left_at
            if ($status === 'Concluído' || $status === 'Desistente') {
                $stmt = $this->db->prepare("
                    UPDATE class_students 
                    SET left_at = NOW() 
                    WHERE class_id = ? AND student_id = ? AND deleted_at IS NULL
                ");
                $stmt->execute([$classId, $studentId]);
            }
            
            return true;
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Erro ao atualizar status do aluno: ' . $e->getMessage());
        }
    }

    public function getStudentCount($classId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM class_students 
            WHERE class_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$classId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
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
            AND u.active = 1
            AND u.deleted_at IS NULL
            AND u.id NOT IN (
                SELECT student_id FROM class_students 
                WHERE class_id = ? AND deleted_at IS NULL
            )
            ORDER BY u.name ASC
        ");
        
        $stmt->execute([$institutionId, $classId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}