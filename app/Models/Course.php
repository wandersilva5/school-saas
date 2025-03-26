<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Course
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all courses for an institution with pagination
     */
    public function getCourses($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT * 
            FROM courses
            WHERE institution_id = ? 
            AND deleted_at IS NULL
            ORDER BY name DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total number of courses for pagination
     */
    public function getTotalCourses($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM courses 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Get a single course by ID
     */
    public function getCourseById($id, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT * 
            FROM courses 
            WHERE id = ? 
            AND institution_id = ? 
            AND deleted_at IS NULL
        ");
            $stmt->execute([$id, $institutionId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                error_log("Course not found with ID: $id and institution_id: $institutionId");
                return null;
            }

            return $result;
        } catch (\PDOException $e) {
            error_log("Database error in getCourseById: " . $e->getMessage());
            throw new \Exception("Database error: " . $e->getMessage());
        }
    }

    /**
     * Create a new course
     */
    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO courses (
                    name, code, description, workload, 
                    duration, requirements, institution_id, active
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['code'],
                $data['description'] ?? null,
                $data['workload'] ?? null,
                $data['duration'] ?? null,
                $data['requirements'] ?? null,
                $data['institution_id'],
                $data['active'] = 1,
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();

            return $id;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Error creating course: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing course
     */
    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE courses 
                SET 
                    name = ?, 
                    code = ?,
                    description = ?,
                    workload = ?,
                    duration = ?,
                    requirements = ?,
                    active = ?,
                    updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");

            $result = $stmt->execute([
                $data['name'],
                $data['code'],
                $data['description'] ?? null,
                $data['workload'] ?? null,
                $data['duration'] ?? null,
                $data['requirements'] ?? null,
                isset($data['active']) ? $data['active'] : 1,
                $id,
                $data['institution_id']
            ]);

            $this->db->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Error updating course: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a course
     */
    public function delete($id, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE courses 
                SET 
                    deleted_at = NOW(), 
                    active = 0
                WHERE id = ? AND institution_id = ?
            ");
            return $stmt->execute([$id, $institutionId]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Error deleting course: ' . $e->getMessage());
        }
    }

    /**
     * Check if a course code exists (for validation)
     */
    public function codeExists($code, $institutionId, $excludeId = null)
    {
        $sql = "
            SELECT COUNT(*) as count 
            FROM courses 
            WHERE code = ? 
            AND institution_id = ? 
            AND deleted_at IS NULL
        ";

        $params = [$code, $institutionId];

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
    }
}
