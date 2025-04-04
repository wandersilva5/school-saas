<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class Alert
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get all alerts for the specified institution with pagination
     */
    public function getAlerts($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as created_by_name
            FROM alerts a
            LEFT JOIN users u ON a.created_by = u.id
            WHERE a.institution_id = ? 
            AND a.deleted_at IS NULL
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get alerts for specific user based on roles
     */
    public function getAlertsForUser($userId, $institutionId, $limit = 10, $offset = 0)
    {
        // Get user roles
        $stmtRoles = $this->db->prepare("
            SELECT r.name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.id
            WHERE ur.user_id = ?
        ");
        $stmtRoles->execute([$userId]);
        $userRoles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);
        
        // If user has no roles, return empty array
        if (empty($userRoles)) {
            return [];
        }
        
        // Convert roles to string for FIND_IN_SET function
        $userRolesStr = implode(',', $userRoles);
        
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as created_by_name
            FROM alerts a
            LEFT JOIN users u ON a.created_by = u.id
            WHERE a.institution_id = ?
            AND a.deleted_at IS NULL
            AND (
                a.target_roles = 'all' 
                OR FIND_IN_SET(?, a.target_roles) > 0
                OR (a.target_roles REGEXP ? AND a.target_roles != '')
            )
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        // For each role, create a regex pattern to find it
        $rolePattern = '';
        foreach ($userRoles as $role) {
            if ($rolePattern) {
                $rolePattern .= '|';
            }
            $rolePattern .= $role;
        }
        
        $stmt->execute([$institutionId, $userRolesStr, $rolePattern, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get total number of alerts for pagination
     */
    public function getTotalAlerts($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM alerts 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /**
     * Get a single alert by ID
     */
    public function getAlertById($id, $institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, u.name as created_by_name 
            FROM alerts a
            LEFT JOIN users u ON a.created_by = u.id
            WHERE a.id = ? 
            AND a.institution_id = ? 
            AND a.deleted_at IS NULL
        ");
        $stmt->execute([$id, $institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new alert
     */
    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO alerts (
                    title, 
                    message, 
                    priority, 
                    target_roles, 
                    start_date, 
                    end_date, 
                    created_by, 
                    institution_id, 
                    created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $data['title'],
                $data['message'],
                $data['priority'],
                $data['target_roles'],
                $data['start_date'],
                $data['end_date'],
                $data['created_by'],
                $data['institution_id']
            ]);

            $id = $this->db->lastInsertId();
            $this->db->commit();
            return $id;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Error creating alert: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing alert
     */
    public function update($id, $data)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE alerts 
                SET 
                    title = ?, 
                    message = ?,
                    priority = ?,
                    target_roles = ?,
                    start_date = ?,
                    end_date = ?,
                    updated_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");

            $result = $stmt->execute([
                $data['title'],
                $data['message'],
                $data['priority'],
                $data['target_roles'],
                $data['start_date'],
                $data['end_date'],
                $id,
                $data['institution_id']
            ]);

            $this->db->commit();
            return $result;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw new \Exception('Error updating alert: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete an alert
     */
    public function delete($id, $institutionId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE alerts 
                SET 
                    deleted_at = NOW()
                WHERE id = ? AND institution_id = ?
            ");
            return $stmt->execute([$id, $institutionId]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            throw new \Exception('Error deleting alert: ' . $e->getMessage());
        }
    }

    /**
     * Get the count of active alerts
     */
    public function getActiveAlertsCount($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM alerts 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
            AND (
                (start_date IS NULL OR start_date <= CURRENT_DATE())
                AND (end_date IS NULL OR end_date >= CURRENT_DATE())
            )
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    /**
     * Get roles from the institution
     */
    public function getRoles($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT id, name, description 
            FROM roles 
            WHERE institution_id = ?
            AND name != 'SupUser'
            ORDER BY name ASC
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
        /**
        * Get all active alerts for the specified institution
        */
        public function getActiveAlerts($institutionId)
        {
            $stmt = $this->db->prepare("
                SELECT a.*, u.name as created_by_name
                FROM alerts a
                LEFT JOIN users u ON a.created_by = u.id
                WHERE a.institution_id = ? 
                AND a.deleted_at IS NULL
                AND (
                    (a.start_date IS NULL OR a.start_date <= CURRENT_DATE())
                    AND (a.end_date IS NULL OR a.end_date >= CURRENT_DATE())
                )
                ORDER BY a.created_at DESC
            ");
            $stmt->execute([$institutionId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
}