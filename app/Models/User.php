<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function authenticate($email, $password)
    {
        $stmt = $this->db->prepare("
                SELECT u.*, r.name as roles
                FROM users u
                JOIN user_roles ur ON u.id = ur.user_id
                JOIN roles r ON r.id = ur.role_id
                WHERE email = ?"
            );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']); // Remove a senha dos dados da sessão
            return $user;
        }

        return false;
    }

    public function getUsers($institutionId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id, 
                u.name, 
                u.email, 
                u.created_at,
                u.active,
                i.name as institution_name,
                GROUP_CONCAT(r.name) as roles
            FROM users u
            LEFT JOIN institutions i ON u.institution_id = i.id
            LEFT JOIN user_roles ur ON u.id = ur.user_id
            LEFT JOIN roles r ON ur.role_id = r.id
            WHERE u.institution_id = ? 
            AND u.deleted_at IS NULL
            GROUP BY u.id, u.name, u.email, u.created_at, u.active, i.name
            ORDER BY u.created_at DESC
            LIMIT ? OFFSET ?
        ");

        $stmt->execute([$institutionId, $limit, $offset]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalUsers($institutionId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total 
            FROM users 
            WHERE institution_id = ? 
            AND deleted_at IS NULL
        ");
        $stmt->execute([$institutionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function create($userData)
    {
        try {
            $this->db->beginTransaction();

            // Check for existing user with same email in this institution
            $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM users 
            WHERE email = ? AND institution_id = ? AND deleted_at IS NULL
        ");
            $stmt->execute([$userData['email'], $userData['institution_id']]);
            if ($stmt->fetchColumn() > 0) {
                throw new \Exception('Email já está em uso nesta instituição');
            }

            // Insert user
            $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password, institution_id, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");

            $result = $stmt->execute([
                $userData['name'],
                $userData['email'],
                password_hash($userData['password'], PASSWORD_DEFAULT),
                $userData['institution_id']
            ]);

            if (!$result) {
                throw new \Exception('Erro ao inserir usuário no banco de dados');
            }

            $userId = $this->db->lastInsertId();

            // Insert user roles if any
            if (!empty($userData['roles'])) {
                $stmt = $this->db->prepare("
                INSERT INTO user_roles (user_id, role_id) 
                VALUES (?, ?)
            ");

                foreach ($userData['roles'] as $roleId) {
                    $result = $stmt->execute([$userId, $roleId]);
                    if (!$result) {
                        throw new \Exception('Erro ao associar perfil ao usuário');
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log("Database error creating user: " . $e->getMessage());
            throw new \Exception('Erro do banco de dados: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function get($id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.*,
                    GROUP_CONCAT(r.id) as role_ids
                FROM users u
                LEFT JOIN user_roles ur ON u.id = ur.user_id
                LEFT JOIN roles r ON ur.role_id = r.id
                WHERE u.id = ? AND u.deleted_at IS NULL
                GROUP BY u.id
            ");

            $stmt->execute([$id]);
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user) {
                // Convert role_ids string to array
                $user['roles'] = $user['role_ids'] ? explode(',', $user['role_ids']) : [];
                unset($user['role_ids']);
                unset($user['password']); // Remove senha por segurança
            }

            return $user;
        } catch (\PDOException $e) {
            error_log("Erro ao buscar usuário: " . $e->getMessage());
            return false;
        }
    }

    public function update($userData)
    {
        try {
            $this->db->beginTransaction();

            $updateFields = ['name = ?', 'email = ?', 'active = ?'];
            $params = [$userData['name'], $userData['email'], $userData['active']];

            if (!empty($userData['password'])) {
                $updateFields[] = 'password = ?';
                $params[] = password_hash($userData['password'], PASSWORD_DEFAULT);
            }

            $params[] = $userData['id'];
            $params[] = $userData['institution_id'];

            $sql = "UPDATE users SET " . implode(', ', $updateFields) .
                " WHERE id = ? AND institution_id = ?";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            // Atualizar roles
            $stmt = $this->db->prepare(
                "DELETE FROM user_roles 
                WHERE user_id = ?"
            );
            $stmt->execute([$userData['id']]);

            if (!empty($userData['roles'])) {
                $stmt = $this->db->prepare(
                    "INSERT INTO user_roles (user_id, role_id) 
                    VALUES (?, ?)"
                );
                foreach ($userData['roles'] as $roleId) {
                    $stmt->execute([$userData['id'], $roleId]);
                }
            }

            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE users 
                SET deleted_at = NOW(),
                SET active = 0
                WHERE id = ? AND institution_id = ?
            ");
            return $stmt->execute([$id, $_SESSION['user']['institution_id']]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}
