<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Student
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllStudents($institutionId)
    {
        // This query should be revised to correctly join and fetch students
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
        ORDER BY 
        u.name ASC;
    ");

        $stmt->execute([$institutionId]);
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug logging
        error_log("Found " . count($students) . " students for institution $institutionId");

        return $students;
    }

    public function getAllGuardians($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT u.id, u.name, u.email
            FROM users u
            JOIN user_roles ur ON u.id = ur.user_id
            JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? 
            AND r.name = 'Responsavel'
            AND u.active = 1
            ORDER BY u.name
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentById($id, $institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.name,
                u.email,
                u.phone,
                u.active,
                gs.guardian_user_id
            FROM users u
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            LEFT JOIN guardians_students gs ON u.id = gs.student_user_id
            LEFT JOIN user_info si ON u.id = si.user_id /* Add this JOIN */
            WHERE u.id = ? 
            AND u.institution_id = ?
            AND r.name = 'Aluno'
        ");
        $stmt->execute([$id, $institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createStudent($data)
    {
        try {
            $this->db->beginTransaction();

            // Log the operation
            error_log("Creating student user: " . json_encode(array_intersect_key($data, array_flip(['name', 'email', 'guardian_id', 'institution_id']))));

            // Create user
            $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, phone, institution_id, active, created_at) 
            VALUES (?, ?, ?, ?, ?, 1, NOW())
        ");
            $stmt->execute([
                $data['name'],
                $data['email'],
                password_hash($data['password'], PASSWORD_DEFAULT),
                $data['phone'] ?? null,
                $data['institution_id']
            ]);
            $studentId = $this->db->lastInsertId();

            // Find role ID for 'Aluno'
            $roleStmt = $this->db->prepare("SELECT id FROM roles WHERE name = 'Aluno' AND institution_id = ?");
            $roleStmt->execute([$data['institution_id']]);
            $roleId = $roleStmt->fetchColumn();

            if (!$roleId) {
                throw new \Exception("Perfil 'Aluno' não encontrado para esta instituição");
            }

            // Assign student role
            $roleAssignStmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $roleAssignStmt->execute([$studentId, $roleId]);

            // Connect with guardian
            if (!empty($data['guardian_id'])) {
                $guardianStmt = $this->db->prepare("
                INSERT INTO guardians_students (student_user_id, guardian_user_id, institution_id) 
                VALUES (?, ?, ?)
            ");
                $guardianStmt->execute([$studentId, $data['guardian_id'], $data['institution_id']]);
            }

            $this->db->commit();
            error_log("Student created successfully with ID: $studentId");
            return $studentId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error creating student: " . $e->getMessage());
            throw $e;
        }
    }

    public function updateStudent($id, $data)
    {
        try {
            $this->db->beginTransaction();

            // Update user data
            $stmt = $this->db->prepare("
                UPDATE users 
                SET 
                    name = ?, 
                    email = ?, 
                    phone = ?,
                    active = ?
                WHERE id = ? 
                AND institution_id = ?
            ");
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['active'] ? 1 : 0,
                $id,
                $data['institution_id']
            ]);

            // Update guardian relationship
            $stmt = $this->db->prepare("
                DELETE FROM guardians_students 
                WHERE student_user_id = ?
            ");
            $stmt->execute([$id]);

            if (!empty($data['guardian_id'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO guardians_students 
                        (student_user_id, guardian_user_id, institution_id) 
                    VALUES 
                        (?, ?, ?)
                ");
                $stmt->execute([
                    $id,
                    $data['guardian_id'],
                    $data['institution_id']
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteStudent($id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM guardians_students 
            WHERE student_user_id = ?
        ");
        $stmt->execute([$id]);

        return true;
    }
}
