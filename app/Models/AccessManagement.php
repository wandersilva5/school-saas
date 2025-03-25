<?php

namespace App\Models;

use \Exception;
use \PDO;

class AccessManagement
{
    private $db;

    public function __construct()
    {
        $this->db = \App\Config\Database::getInstance()->getConnection();
    }

    /**
     * Get users for the specified institution with pagination
     */
    public function getUsers($institutionId, $limit = 10, $offset = 0)
    {
        try {
            // Count total records
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT u.id) as total
                FROM users u
                LEFT JOIN institutions i ON u.institution_id = i.id
                WHERE u.institution_id = ?
            ");
            $stmt->execute([$institutionId]);
            $totalUsers = $stmt->fetch(\PDO::FETCH_ASSOC)['total'];
            $totalPages = ceil($totalUsers / $limit);

            // Get users with pagination
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, 
                    u.name, 
                    u.email, 
                    u.created_at, 
                    i.name as institution_name, 
                    GROUP_CONCAT(r.name) as roles
                FROM users u
                LEFT JOIN institutions i ON u.institution_id = i.id
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.institution_id = ?
                GROUP BY u.id, u.name, u.email, u.created_at, i.name
                ORDER BY u.created_at DESC
                LIMIT ? OFFSET ?
            ");

            $stmt->execute([$institutionId, $limit, $offset]);
            $users = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return [
                'users' => $users,
                'totalUsers' => $totalUsers,
                'totalPages' => $totalPages
            ];
        } catch (\PDOException $e) {
            error_log("Error in getUsers: " . $e->getMessage());
            throw new \Exception('Erro ao buscar usuários: ' . $e->getMessage());
        }
    }

    /**
     * Get all active institutions
     */
    public function getInstitutions()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name 
                FROM institutions 
                WHERE deleted_at IS NULL
                ORDER BY name
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getInstitutions: " . $e->getMessage());
            throw new \Exception('Erro ao buscar instituições: ' . $e->getMessage());
        }
    }

    /**
     * Get all roles
     */
    public function getRoles()
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id, name, description 
                FROM roles 
                ORDER BY name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getRoles: " . $e->getMessage());
            throw new \Exception('Erro ao buscar perfis: ' . $e->getMessage());
        }
    }

    /**
     * Update user roles
     */
    public function updateUserRoles($userId, $roleIds, $institutionId)
    {
        try {
            // Verify user belongs to the same institution
            $stmt = $this->db->prepare("SELECT id FROM users WHERE id = ? AND institution_id = ?");
            $stmt->execute([$userId, $institutionId]);
            if (!$stmt->fetch()) {
                throw new \Exception('Usuário não encontrado ou não pertence à sua instituição');
            }

            $this->db->beginTransaction();

            // Remove existing roles
            $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Add new roles
            if (!empty($roleIds)) {
                $placeholders = implode(',', array_fill(0, count($roleIds), '?'));
                $stmt = $this->db->prepare("
                    INSERT INTO user_roles (user_id, role_id) 
                    SELECT ?, id FROM roles 
                    WHERE id IN ({$placeholders})
                    AND institution_id = ?
                ");

                $params = array_merge([$userId], $roleIds, [$institutionId]);
                $stmt->execute($params);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error in updateUserRoles: " . $e->getMessage());
            throw new \Exception('Erro ao atualizar perfis: ' . $e->getMessage());
        }
    }

    /**
     * Create a new user
     */
    public function createUser($userData)
    {
        $transactionStarted = false;
        
        try {
            // Validate required fields
            $name = trim($userData['name'] ?? '');
            $email = filter_var($userData['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $roleId = $userData['role_id'] ?? null;
            $institutionId = $userData['institution_id'] ?? null;

            if (empty($name)) {
                throw new \Exception('O nome é obrigatório');
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email inválido');
            }

            if (empty($roleId)) {
                throw new \Exception('Selecione pelo menos um perfil');
            }

            // Check if email is already in use
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                throw new \Exception('Este email já está em uso');
            }

            // Generate a random password
            $password = bin2hex(random_bytes(8));
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $this->db->beginTransaction();
            $transactionStarted = true;

            // Insert the user
            $stmt = $this->db->prepare(
                "INSERT INTO users (name, email, password, institution_id, created_at) 
                VALUES (?, ?, ?, ?, NOW())"
            );

            $stmt->execute([
                $name,
                $email,
                $hashedPassword,
                $institutionId
            ]);

            $userId = $this->db->lastInsertId();

            // Associate role with user
            $stmt = $this->db->prepare(
                "INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)"
            );
            $stmt->execute([$userId, $roleId]);

            $this->db->commit();
            
            // For the calling code to handle email sending
            return [
                'userId' => $userId,
                'email' => $email,
                'password' => $password,
                'name' => $name
            ];
        } catch (\Exception $e) {
            if ($transactionStarted) {
                $this->db->rollBack();
            }
            error_log("Error in createUser: " . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get user role
     */
    public function getUserRole($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT role_id 
                FROM user_roles 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getUserRole: " . $e->getMessage());
            throw new \Exception('Erro ao buscar perfil do usuário: ' . $e->getMessage());
        }
    }

    /**
     * Update a user's role
     */
    public function updateUserRole($userId, $roleId)
    {
        try {
            if (!$userId || !$roleId) {
                throw new \Exception('Dados inválidos');
            }

            // First remove existing roles
            $stmt = $this->db->prepare("DELETE FROM user_roles WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Insert new role
            $stmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
            $stmt->execute([$userId, $roleId]);

            return true;
        } catch (\Exception $e) {
            error_log("Error in updateUserRole: " . $e->getMessage());
            throw new \Exception('Erro ao atualizar perfil: ' . $e->getMessage());
        }
    }

    /**
     * Create a TI user
     */
    public function createUserTI($userData)
    {
        try {
            $name = $userData['name'];
            $email = filter_var($userData['email'], FILTER_SANITIZE_EMAIL);
            $password = $userData['password'];
            $institutionId = $userData['institution_id'];

            $this->db->beginTransaction();

            // Insert the user
            $stmt = $this->db->prepare(
                "INSERT INTO users (name, email, password, institution_id, created_at) 
                 VALUES (?, ?, ?, ?, NOW())"
            );

            $stmt->execute([
                $name,
                $email,
                password_hash($password, PASSWORD_DEFAULT),
                $institutionId
            ]);

            // Get the role ID for TI
            $roleStmt = $this->db->prepare("SELECT id FROM roles WHERE name = 'TI' AND institution_id = ?");
            $roleStmt->execute([$institutionId]);
            $roleId = $roleStmt->fetchColumn();
            
            if ($roleId) {
                // Assign TI role
                $roleAssignStmt = $this->db->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                $roleAssignStmt->execute([$this->db->lastInsertId(), $roleId]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Error in createUserTI: " . $e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}