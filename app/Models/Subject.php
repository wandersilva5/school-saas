<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Subject
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all subjects for an institution with pagination
     */
    public function getSubjects($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, c.name as course_name 
            FROM subjects s
            JOIN courses c ON s.course_id = c.id
            WHERE s.institution_id = ? 
            AND s.deleted_at IS NULL
            ORDER BY c.name, s.name
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total number of subjects for pagination
     */
    public function getTotalSubjects($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM subjects 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Get a single subject by ID
     */
    public function getSubjectById($id, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT s.*, c.name as course_name 
                FROM subjects s
                JOIN courses c ON s.course_id = c.id
                WHERE s.id = ? 
                AND s.institution_id = ? 
                AND s.deleted_at IS NULL
            ");
            $stmt->execute([$id, $institutionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                error_log("Subject not found with ID: $id and institution_id: $institutionId");
                return null;
            }

            return $result;
        } catch (\PDOException $e) {
            error_log("Database error in getSubjectById: " . $e->getMessage());
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Get subjects by course ID
     */
    public function getSubjectsByCourse($courseId, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * 
                FROM subjects 
                WHERE course_id = ? 
                AND institution_id = ? 
                AND deleted_at IS NULL
                ORDER BY semester, name
            ");
            $stmt->execute([$courseId, $institutionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getSubjectsByCourse: " . $e->getMessage());
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Create a new subject
     */
    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO subjects (
                    name, code, course_id, workload, semester, description,
                    user_id, institution_id, active, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['code'],
                $data['course_id'],
                $data['workload'] ?? null,
                $data['semester'] ?? null,
                $data['description'] ?? null,
                $data['user_id'],
                $data['institution_id'],
                $data['active'] ?? 1,
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();

            return $id;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Error creating subject: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing subject
     */
    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE subjects 
                SET 
                    name = ?, 
                    code = ?,
                    course_id = ?,
                    workload = ?,
                    semester = ?,
                    description = ?,
                    user_id = ?,
                    active = ?,
                    updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['code'],
                $data['course_id'],
                $data['workload'] ?? null,
                $data['semester'] ?? null,
                $data['description'] ?? null,
                $data['user_id'],
                $data['active'] ?? 1,
                $id,
                $data['institution_id']
            ]);

            $this->db->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Error updating subject: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a subject
     */
    public function delete($id, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE subjects 
                SET 
                    deleted_at = NOW(), 
                    active = 0
                WHERE id = ? AND institution_id = ?
            ");
            return $stmt->execute([$id, $institutionId]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Error deleting subject: ' . $e->getMessage());
        }
    }

    /**
     * Check if a subject code exists for a course (for validation)
     */
    public function codeExists($code, $courseId, $institutionId, $excludeId = null)
    {
        $sql = "
            SELECT COUNT(*) as count 
            FROM subjects 
            WHERE code = ? 
            AND course_id = ?
            AND institution_id = ? 
            AND deleted_at IS NULL
        ";

        $params = [$code, $courseId, $institutionId];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
}